<?php

namespace app\console\model;

use think\Model;

class Order extends Model
{
    public function getOrderListByActivity($id){
        $result = $this->where(['type' => 'activity' , 'id' => $id , 'is_pay' => 1])->select();
        if(!empty($result)){
            $model = model("Member");
            foreach ($result as $key => $value) {
                $info = $model->getMemberInfoById($value['uid']);
                $result[$key]['img'] = $info['userPhoto'];
            } 
        }
        return $result;
    }
}