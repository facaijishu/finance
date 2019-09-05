<?php

namespace app\console\model;

use think\Model;

class ThroughDefaultInfo extends Model
{
    public function createThroughDefaultInfo($data){
        if(empty($data)){
            $this->error = '缺少拒绝理由';
            return false;
        }
        //验证数据信息
        $validate = validate('ThroughDefaultInfo');
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
            $data['create_time'] = time();
            $data['create_uid'] = $info['uid'];
            $this->allowField(true)->save($data);
            model("Through")->where(['uid' => $data['uid']])->update(['status' => -1]);
            $info = model("Member")->where(['uid' => $data['uid']])->find();
            $info['content'] = $data['reason'];
            // 提交事务
            $this->commit();
            return $info;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
}