<?php
namespace app\home\model;

use think\Model;

class MemberOrganize extends Model
{
    public function getMemberOrganizeInfoByMid($mid)
    {
        if(empty($mid)){
            $this->error = 'ID不存在';
            return false;
        }
        $re  = $this->where(['mid'=>$mid])->find();
        $info['mid'] = $re['mid'];
        $info['orgId'] = $re['orgId'];
        $info['org_customer_service'] = $re['org_customer_service'];
        $info['startTime'] = $re['startTime'];
        return $info;
    }
}