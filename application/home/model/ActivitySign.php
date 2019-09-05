<?php

namespace app\home\model;

use think\Model;

class ActivitySign extends Model
{
    public function createActivitySign($data){
        //开启事务
        $this->startTrans();
        try{
            
            if($this->allowField(true)->save($data)){
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
