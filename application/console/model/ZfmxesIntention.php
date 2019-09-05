<?php

namespace app\console\model;

use think\Model;

class ZfmxesIntention extends Model
{
    public function getZfmxesIntentionInfoById($id){
        $info = $this->where(['id' => $id])->find();
        return $info;
    }
}