<?php

namespace app\home\model;

use think\Model;

class Bonus extends Model
{
    public function getBonusMoney(){
        $result = [];
        $day = $this->where(['type' => 'day'])->order("b_id desc")->limit(1)->find();
        $month = $this->where(['type' => 'month' , 'status' => 1])->order("b_id desc")->select();
        $day['total_amount'] = intval($day['total_amount']/10000);
        $result['day'] = $day;
        $result['month'] = $month;
        return $result;
    }
}