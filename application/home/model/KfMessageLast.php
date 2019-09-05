<?php

namespace app\home\model;

use think\Model;

class KfMessageLast extends Model
{
    public function getNewInfoByUid($uid){
        $result = $this->where(['m_uid' => $uid])->find();
        if($result['is_read'] != 0){
            $this->where(['m_uid' => $uid])->update(['is_read' => 0 , 'first_time' => 0]);
        }
        return $result;
    }
}