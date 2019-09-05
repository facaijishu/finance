<?php

namespace app\console\model;

use think\Model;

class ProjectClueInfo extends Model
{
    public function completeProjectClue($data){
        if(empty($data)){
            $this->error = '备注数据不存在';
            return false;
        }
        //验证数据信息
        $validate = validate('ProjectClueInfo');
        $scene = 'add';
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $result = model("ProjectClue")->where(['clue_id' => $data['pc_id']])->update(['status' => 1]);
            $info = \app\console\service\User::getInstance()->getInfo();
            $arr = [];
            $arr['pc_id'] = $data['pc_id'];
            $arr['content'] = $data['content'];
            $arr['create_time'] = time();
            $arr['create_uid'] = $info['uid'];
            $this->allowField(true)->save($arr);
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