<?php

namespace app\home\model;

use think\Model;

class Order extends Model
{
    public function getOrderListByUser(){
        $user = session('FINANCE_USER');
        $list = $this->where(['uid' => $user['uid']])->select();
        if ((!empty($list))){
            foreach ($list as $key => $value) {
                $list[$key]['total_fee'] = floatval($value['total_fee']/100);
            }
        }
        return $list;
    }
    
    /**
     * 活动是否已报名
     * @param 活动编号 $id
     * @param 订单类型  $typeName 1：活动
     * @return 返回true为已经报名或报名等待支付
     */
    public function isSignUp($id,$typeName){
        $user = session('FINANCE_USER');
        $type = "";
        $flg = 0;
        if($typeName==1){
            $type = "activity";
        }
        $res  = $this->where(['uid' => $user['uid'] , 'id' => $id , 'type' => $type, 'status' => 1 ,'is_pay' => 1 ])->find();
        if($res['o_id']>0){
            $flg = 1;
        }
        
        return $flg;
    }
    
    
}