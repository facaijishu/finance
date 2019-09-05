<?php

namespace app\home\model;

use think\Model;

class Study extends Model{
    public function getStudyInfoByOpenId($openId){
        $result = $this->where(['openId' => $openId])->find();
        return $result;
    }

    public function createStudyInfo($data){
        //开启事务
        $this->startTrans();
        try{
            $result = $this->where(['openId' => $data['openid']])->find();
            if(!empty($result)){
                $arr = [];
                $arr['update_time'] = time();
                $arr['status'] = 0;
                $this->allowField(true)->where(['openId' => $data['openid']])->update($arr);
            } else {
                $arr = [];
                $arr['openId'] = $data['openid'];
                $arr['create_time'] = time();
                $arr['update_time'] = time();
                $arr['status'] = 0;
                $this->allowField(true)->save($arr);
            }
//            $result = model("Member")->alias("m")
//                    ->join("Through t" , "m.uid=t.uid")
//                    ->where("m.openId=".$data['openid'])
//                    ->field("t.*")
//                    ->find();
            
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
}