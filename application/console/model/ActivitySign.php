<?php

namespace app\console\model;

use think\Model;

class ActivitySign extends Model
{
    
    public function addSign($data){
        //开启事务
        $this->startTrans();
        try{
            if($this->allowField(true)->saveAll($data)){
                $this->commit();
                return true;
            }else{
                // 回滚事务
                $this->rollback();
                return false;
            }
            
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
}