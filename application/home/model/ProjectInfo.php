<?php

namespace app\home\model;

use think\Model;

class ProjectInfo extends Model
{
    public function setCollection($id , $sign){
        if(empty($id)){
            $this->error = '项目id不存在';
            return false;
        }
        if(empty($sign)){
            $this->error = '缺少收藏标签sign';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $arr = [];
            $user = session("FINANCE_USER");
            $collection = explode(',', $user['collection']);
            if($sign == 'true'){
                if(in_array($id, $collection)){
                    $this->error = '该项目已收藏,不能重复收藏';
                    return false;
                } else {
                    array_push($collection, $id);
                    $coll = trim(implode(',', $collection) , ',');
                    $model = model("Member");
                    $model->where(['uid' => $user['uid']])->update(['collection' => $coll]);
                    $user = $model->where(['uid' => $user['uid']])->find();
                    session('FINANCE_USER' , $user);
                }
            } else {
                if(in_array($id, $collection)){
                    foreach ($collection as $key => $value) {
                        if($value == $id){
                            unset($collection[$key]);
                        }
                    }
                    $coll = trim(implode(',', $collection) , ',');
                    $model = model("Member");
                    $model->where(['uid' => $user['uid']])->update(['collection' => $coll]);
                    $user = $model->where(['uid' => $user['uid']])->find();
                    session('FINANCE_USER' , $user);
                } else {
                    $this->error = '该项目未收藏,不能取消收藏';
                    return false;
                }
            }
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