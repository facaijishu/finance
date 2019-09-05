<?php

namespace app\home\model;

use think\Model;

class ProSearchHistory extends Model
{
    public function getProSearchHistory(){
        $begin_time = intval(time())-3600*24*7;
        $result = $this->field("content,count(time) tt")
                ->where('create_time' , 'gt' , $begin_time)
                ->group("content")
                ->order("tt desc")
                ->limit(5)
                ->select();
        return $result;
    }

    public function addSearchHistory($text){
        if(empty($text)){
            $this->error = '内容不能为空';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $arr = [];
            $arr['content'] = $text;
            $arr['create_time'] = time();
            $arr['create_uid'] = session('FINANCE_USER.uid');
            $arr['time'] = date('Y-m-d' , time());
            $this->allowField(true)->save($arr);
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