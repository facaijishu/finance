<?php

namespace app\home\model;

use think\Model;

class ZryxesIntention extends Model
{
    public function createZryxesIntention($data){
        if(empty($data)){
            $this->error = '意向不存在';
            return false;
        }
        $user = session('FINANCE_USER');
        //验证数据信息
        $validate = validate('ZryxesIntention');
        $scene = 'add';
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }

        //开启事务
        $this->startTrans();
        try{
            $info = model("ZryxesEffect")->getZryxesEffectSimpleInfo($data['id']);
            $arr = [];
            $arr['name'] = $info['sec_uri_tyshortname'];
            $arr['neeq'] = $info['neeq'];
            $arr['direction'] = $info['direction'];
            $arr['num'] = $data['num'];
            $arr['price'] = $info['price'];
            $arr['money'] = $data['money'];
            $arr['company'] = $data['company'];
            $arr['phone'] = $data['phone'];
            $arr['create_uid'] = $user['uid'];
            $arr['create_time'] = time();
            $arr['done_uid'] = 0;
            $arr['status'] = 0;
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