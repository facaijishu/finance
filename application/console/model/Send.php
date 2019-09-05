<?php
namespace app\console\model;

use think\Model;

class Send extends Model
{
    public function getSendInfoById($id) {
       /* $info = $this->where(['id' => $id])->find();
        $true = model('SendList')->where(['send_id' => $id, 'result' => 'true'])->count();
        $false = model('SendList')->where(['send_id' => $id, 'result' => 'false'])->count();
        $info['true'] = $true;
        $info['false'] = $false;*/
         if(empty($id)){
            $this->error = '该推送id不存在';
            return false;
        }
        $info = $this->where(['id' => $id])->find(); 
        return $info;
    }

    public function addSendInfo($data) {
        if(empty($data)){
            $this->error = '缺少消息推送数据';
            return false;
        }

        //开启事务
        $this->startTrans();
        try{
            $data['created_at'] = date('Y-m-d H:i:s', time());
            $this->allowField(true)->save($data);
            $id = $this->getLastInsID();
            // 提交事务
            $this->commit();
            return $id;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    public function refreshSend($id) {
        $this->startTrans();
        try{
            $model = model('SendList');
            $info = $this->where(['id' => $id])->find();
            $array = json_decode($info['content'], true);
            $list = $model->where(['send_id' => $id, 'result' => 'false'])->select();
            foreach ($list as $key => $value) {
                if ($info['type'] == 'project') {
                    $url = $_SERVER['SERVER_NAME'].'/home/project_info/index/id/';
                }
                if ($info['type'] == 'zryxes') {
                    $url = $_SERVER['SERVER_NAME'].'/home/zryxes_effect_info/index/id/';
                }
                $end = sendTemplateMessage(config("new_project"), $value['openId'], $url.$info['act_id'], $array);
                $end = getSendCodeMsg($end);
                $result = explode('-', $end);
                $result = $result[0] == 0 ? 'true' : 'false';
                $ars = ['content' => $end, 'result' => $result];
                $model->where(['id' => $value['id']])->update($ars);
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
}
