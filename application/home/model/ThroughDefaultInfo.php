<?php

namespace app\home\model;

use think\Model;

class ThroughDefaultInfo extends Model
{
    public function getDefaultNewInfo($uid){
        $result = $this->where(['uid' => $uid])->order("create_time desc")->select();
        if(!empty($result)){
            return $result[0];
        } else {
            return $result;
        }
    }
}