<?php

namespace app\console\model;

use think\Model;

class RoadShow extends Model
{
    public function getRoadShowInfoById($id){
        $result = $this->where(['rs_id' => $id])->find();
        $time = changeLongTime($result['duration']);
        $result['duration'] = $time;
        return $result;
    }
    public function createRoadShow($data){
        if(empty($data)){
            $this->error = '缺少路演数据';
            return false;
        }
        $data['content'] = $data['summernote'];
        //验证数据信息
        $validate = validate('RoadShow');
        $scene = isset($data['rs_id']) ? 'edit' : 'add';
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }

        //开启事务
        $this->startTrans();
        try{
            $model = model("material_library");
            $video = $model->getMaterialInfoById($data['video']);
            $top_img = $model->getMaterialInfoById($data['top_img']);
            $data['video_url'] = $video['url'];
            $file = ROOT_PATH.'public/uploads/'.$video['url'];
            $data['duration'] = $this->getVideoLongTime($file);
            $data['top_img_url'] = $top_img['url'];
            if($data['speaker'] != ''){
                $speaker = $model->getMaterialInfoById($data['speaker']);
                $data['speaker_url'] = $speaker['url'];
            }
            if(isset($data['rs_id'])){
                $arr = [];
                $arr = $data;
                $this->allowField(true)->save($arr , ['rs_id' => $data['rs_id']]);
            } else {
                $info = \app\console\service\User::getInstance()->getInfo();
                $data['create_time'] = time();
                $data['create_uid'] = $info['uid'];
                $data['status'] = 1;
                $this->allowField(true)->save($data);
            }
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    public function stopRoadShow($id){
        if(empty($id)){
            $this->error = '该路演id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['rs_id' => $id])->update(['status' => -1]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    public function startRoadShow($id){
        if(empty($id)){
            $this->error = '该路演id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['rs_id' => $id])->update(['status' => 1]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    public function getVideoLongTime($file){
        $ffmpeg = 'ffmpeg';
        ob_start();
        passthru(sprintf($ffmpeg.' -i "%s" 2>&1', $file));
        $info = ob_get_contents();
        ob_end_clean();
        if (preg_match("/Duration: (.*?), start: (.*?), bitrate: (\d*) kb\/s/", $info, $match)) {
            $da = explode(':', $match[1]); 
            $seconds = $da[0] * 3600 + $da[1] * 60 + $da[2]; // 转换为秒
        }
        return $seconds;
    }
    
    /**
     * 设置排序
     * @param 路演编号 $id
     * @param 排序数字 $order
     * @return boolean
     */
    public function setOrder($data){
        if(empty($data)){
            $this->error = '缺少相关数据';
            return false;
        }

        //开启事务
        $this->startTrans();
        try{
            $this->where(['rs_id' => $data['id']])->update(['list_order' => $data['list_order']]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
}