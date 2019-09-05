<?php

namespace app\console\model;

use think\Model;

class Cwbbs extends Model
{
    public function getCwbbsInfoById($id){
        $info = $this->where(['id' => $id])->find();
        if($info['type'] == 0){
            $info['type_name'] = '资产负债表';
        }elseif ($info['type'] == 1) {
            $info['type_name'] = '利润表';
        }elseif($info['type'] == 2){
            $info['type_name'] = '现金流量表';
        }else{
            $info['type_name'] = '财务分析表';
        }
        //公司信息
        $model = model("Gszls");
        $gszls = $model->getGszlsInfoByNeeq($info['neeq']);
        $info['gszls'] = $gszls;
        //财报详细信息
        $model = model("Cwbbsjs");
        $cwbbsjs = $model->where(['neeq' => $info['neeq'] , 'type' => $info['type'] , 'bbrq' => $info['reportdate']])->select();
        $model = model("Cwbbxms");
        foreach ($cwbbsjs as $key => $value) {
            $cwbbxms = $model->where(['neeq' => $value['neeq'] , 'type' => $value['type'] , 'code' => $value['bbxm']])->find();
            $cwbbsjs[$key]['bbxm_name'] = $cwbbxms['name'];
        }
        $info['cwbbsjs'] = $cwbbsjs;
        return $info;
    }
}