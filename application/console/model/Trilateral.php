<?php

namespace app\console\model;

use think\Model;

class Trilateral extends Model
{
    /**
     * 或者三方演绎详细信息
     * @param 路演编号 $id
     * @return 数据集合
     */
    public function getTrilateralById($id){
        $result  = $this->where(['id' => $id])->find();
        $time    = changeLongTime($result['video_duration']);
        $result['v_time'] = $time;
        return $result;
    }
    
    /**
     * 创建三方演绎数据记录
     * @param 提交数据集合 $data
     * @return boolean
     */
    public function createTrilateral($data){
        if(empty($data)){
            $this->error = '缺少路演数据';
            return false;
        }
        
        $data['content_img'] = $data['summernote'];
        //验证数据信息
        $validate  = validate('Trilateral');
        $scene     = isset($data['id']) ? 'edit' : 'add';
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }
        
        //开启事务
        $this->startTrans();
        try{
            $model      = model("material_library");
            if($data['video']!==''){
                $video                  = $model->getMaterialInfoById($data['video']);
                $data['video_url']      = $video['url'];
                $file = ROOT_PATH.'public/uploads/'.$video['url'];
                $data['video_duration'] = $this->getVideoLongTime($file);
            }
            
            $top_img    = $model->getMaterialInfoById($data['top_img']);
            $index_img  = $model->getMaterialInfoById($data['index_img']);
            
            $data['top_img_url']    = $top_img['url'];
            $data['index_img_url']  = $index_img['url'];
            
  
            if(isset($data['id'])){
                $arr = [];
                $arr = $data;
                $this->allowField(true)->save($arr , ['id' => $data['id']]);
            } else {     
                $info = \app\console\service\User::getInstance()->getInfo();
                $data['create_time'] = time();
                $data['create_uid']  = $info['uid'];
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
    
    /**
     * 下架记录
     * @param 路演编号 $id
     * @return boolean
     */
    public function stopTrilateral($id){
        if(empty($id)){
            $this->error = '该路演id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['id' => $id])->update(['status' => -1]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    
    /**
     * 上架记录
     * @param 路演编号 $id
     * @return boolean
     */
    public function startTrilateral($id){
        if(empty($id)){
            $this->error = '该路演id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['id' => $id])->update(['status' => 1]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
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
            $this->where(['id' => $data['id']])->update(['list_order' => $data['list_order']]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    
    /**
     * 获取视频长度
     * @param 文件路径  $file
     * @return 返回时长（秒）
     */
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
}