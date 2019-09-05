<?php

namespace app\home\model;

use think\Model;

class SearchKeywords extends Model
{
    /**
     * 插入数据
     * @param 数据集合 $data
     * @return boolean
     */
    public function createSearchKeywords($data){
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
