<?php

namespace app\home\model;

use think\Model;

class Cwbbs extends Model
{
    public function getNetProfitByNeeq($neeq){
        $result = $this->where(['neeq' => $neeq , 'type' => 1])->order("reportdate desc")->select();
        if(empty($result)){
            return 0;
        } else {
            $info = $result[0];
            $info = db("cwbbsjs")->where(['neeq' => $info['neeq'] , 'type' => $info['type'] , 'bbrq' => $info['reportdate'] ,'bbxm' => 'NETPROFIT'])->find();
            return $info['xmsz'];
        }
    }
}