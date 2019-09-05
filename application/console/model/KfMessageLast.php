<?php

namespace app\console\model;

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
    public function getMessageInfoByUid($uid){
        $result = $this->where(['m_uid' => $uid])->find();
        return $result;
    }

    public function getMessageListByScsId($scs_id){
        $top_list = $this->alias("kml")
                ->join("member m" , "kml.m_uid=m.uid")
                ->where('scs_id' , 'eq' , $scs_id)
                ->where('is_read' , 'neq' , 0)
                ->field("kml.*,m.userName,m.userPhoto")
                ->order("first_time asc")
                ->select();
        $button_list = $this->alias("kml")
                ->join("member m" , "kml.m_uid=m.uid")
                ->where('scs_id' , 'eq' , $scs_id)
                ->where('is_read' , 'eq' , 0)
                ->field("kml.*,m.userName,m.userPhoto")
                ->order("last_time desc")
                ->select();
        $list = [];
        foreach ($top_list as $key => $value) {
            array_push($list, $value);
        }
        foreach ($button_list as $key => $value) {
            array_push($list, $value);
        }
        return $list;
    }
}