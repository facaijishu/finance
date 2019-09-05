<?php

namespace app\console\model;

use think\Model;

class CaijiRemark extends Model
{
    public function getRemarkByCodeDateAndType($code , $plannoticeddate , $type , $pid){
        $info = $this->where(['neeq' => $code , 'plannoticeddate' => $plannoticeddate , 'type' => $type , 'pid' => $pid])->select();
        if(!empty($info)){
            $model = model("User");
            foreach ($info as $key => $value) {
                $user = $model->getUserById($value['create_uid']);
                $info[$key]['create_uid'] = $user['real_name'];
            }
        }
        return $info;
    }
    public function createCaijiRemark($data){
        if(empty($data)){
            $this->error = '缺少相关备注数据';
            return false;
        }
        //验证数据信息
        $validate = validate('CaijiRemark');
        $scene = 'add';
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }

        //开启事务
        $this->startTrans();
        try{
            $info = \app\console\service\User::getInstance()->getInfo();
            $arr = [];
            $arr['neeq'] = $data['neeq'];
            $arr['pid'] = $data['pid'];
            $arr['plannoticeddate'] = $data['plannoticeddate'];
            $arr['type'] = $data['type'];
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