<?php

namespace app\home\model;

use think\Model;

class SelfCustomerService extends Model
{
    public function getCustomerServiceById($id){
        $result = $this->where(['scs_id' => $id])->find();
        return $result;
    }
    public function getCustomerServiceByOpenid($openId){
        $result = $this->where(['kf_openId' => $openId , 'status' => 1])->find();
        return $result;
    }
    public function getRandomCustomerService($uid){
        $model = model("Member");
        //根据$uid去Member表中查询个人的openId;
        $stage = $model->where("uid = ".$uid)->find();
        //如果该用户的上一级不为0
        if($stage['superior'] != 0){
            $now = $model -> where("uid =".$stage['superior'])->find();
            //该用户的上一级确实存在
            if($now){
                //取出上一级用户的openId
                $openId = $now['openId'];
                //判断该用户的上一级是什么类型（客服，外部人员）
                $custome = $this->where(['status' => 1 , 'type' => ''])->where(["kf_openId"=>$openId])->find();
                //如果存在，说明是上一级是客服人员，否则，为外部人员，直接随机分配
                if($custome){
                    //如果为客服人员直接更新
                    $model->where(['uid' => $uid])->update(['customerService' => $custome['scs_id']]);
                    $user = $model->where(['uid' => $uid])->find();
                    session('FINANCE_USER' , $user);
                    $service = $this->where(['scs_id' => $custome['scs_id']])->find();
                    $num = intval($service['count_member']);
                    $num++;
                    $this->where(['scs_id' => $custome['scs_id']])->update(['count_member' => $num]);
                    return $service;
                }
            }
        }

        $result = $this->where(['status' => 1 , 'type' => ''])->whereOr(['is_select' => 1])->order("scs_id asc")->select();
        $id = $result[0]['scs_id'];
        foreach ($result as $key => $value) {
            if($value['is_select'] === 1){
                if(isset($result[$key+1])){
                    $id = $result[$key+1]['scs_id'];
                }
            } else {
                continue;
            }
        }
        $this->where(['is_select' => 1])->update(['is_select' => 0]);
        $this->where(['scs_id' => $id])->update(['is_select' => 1]);

        $res = $model->where(['uid' => $uid])->update(['customerService' => $id]);
        $user = $model->where(['uid' => $uid])->find();
        session('FINANCE_USER' , $user);
        $service = $this->where(['scs_id' => $id])->find();
        $num = intval($service['count_member']);
        $num++;
        $this->where(['scs_id' => $id])->update(['count_member' => $num]);
        return $service;
    }
}