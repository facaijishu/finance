<?php

namespace app\console\model;

use think\Model;

class ZryxesIntention extends Model
{
    public function getZryxesIntentionInfoById($id){
        $info = $this->where(['id' => $id])->find();
        return $info;
    }
}