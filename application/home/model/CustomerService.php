<?php

namespace app\home\model;

use think\Model;

class CustomerService extends Model
{
    public function getCustomerServiceById($id){
        $result = $this->where(['cs_id' => $id])->find();
        return $result;
    }
    public function getRandomCustomerService($uid){
        $result = $this->where(['status' => 1])->order("count_member asc")->select();
        $model = model("Member");
//        shuffle($result);
        $model->where(['uid' => $uid])->update(['customerService' => $result[0]['cs_id']]);
        $user = $model->where(['uid' => $uid])->find();
        session('FINANCE_USER' , $user);
        $service = $this->where(['cs_id' => $result[0]['cs_id']])->find();
        $num = intval($service['count_member']);
        $num++;
        $this->where(['cs_id' => $result[0]['cs_id']])->update(['count_member' => $num]);
        return $result[0];
    }
}