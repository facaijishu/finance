<?php

namespace app\console\model;

use think\Model;

class CaijiStatus extends Model
{
    public function getZfmxesStatusByCode($code){
        $info = $this->where(['neeq' => $code])->find();
        return $info;
    }
}