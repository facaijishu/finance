<?php

namespace app\home\model;

use think\Model;

class Activity extends Model
{
    
    /**
     * 获取活动详细信息
     * @param 活动编号 $id
     * @return 数据集
     */
    public function getActivityInfo($id){
        $info       = $this->where(['a_id' => $id])->find();
        
        $model      = model("MaterialLibrary");
        $top_img    = $model->getMaterialInfoById($info['top_img']);
        $info['top_img_url']  = $top_img['url'];
        $info['top_img_name'] = $top_img['name'];
        
        $now        = time();
        $sign_start = $info['sign_start_time'];
        $sign_end   = $info['sign_end_time'];
        
        $model      = model("Order");
        $is_signed  = $model->isSignUp($id,1);
        if($is_signed==1){
            $info['icon_name'] = "已报名";
            $info['sign_name'] = "已报名";
            $info['icon']      = 0;
        }else{
            if($info['is_show']==1){
                if($sign_start>$now){
                    $info['icon_name'] = "未开始";
                    $info['sign_name'] = "未开始";
                    $info['icon']      = 0;
                }else{
                    if($sign_end>=$now){
                        $info['icon_name'] = "进行中";
                        $info['sign_name'] = "报名";
                        $info['icon']      = 1;
                    }else{
                        $info['icon_name'] = "停止报名";
                        $info['sign_name'] = "停止报名";
                        $info['icon']      = 0;
                    }
                }
            }else{
                $info['icon_name'] = "停止报名";
                $info['sign_name'] = "停止报名";
                $info['icon']      = 0;
            }
            
            
        }
        return $info;
    }
    
    public function getActivityListByUser($str){
        $arr = explode(',', $str);
        $arr = array_reverse($arr);
        foreach ($arr as $key => $value) {
            $info = $this->getActivity($value);
            $arr[$key] = $info;
        }
        return $arr;
    }
    
    /**
     * 我的活动获取（收藏，报名列表页）
     * @param 活动编号 $id
     * @return 活动信息集合
     */
    public function getActivity($id){
        $info       = $this->where(['a_id' => $id])->find();
        
        $model      = model("MaterialLibrary");
        $top_img    = $model->getMaterialInfoById($info['top_img']);
        $info['top_img_url']  = $top_img['url'];
        $info['top_img_name'] = $top_img['name'];
        
        $now        = time();
        $sign_start = $info['sign_start_time'];
        $sign_end   = $info['sign_end_time'];
        
        $model      = model("Order");
        $is_signed  = $model->isSignUp($id,1);
        if($info['is_show']==1){
            if($sign_start>$now){
                $info['icon_name'] = "未开始";
                $info['sign_name'] = "未开始";
                $info['icon']      = 0;
            }else{
                if($sign_end>=$now){
                    $info['icon_name'] = "进行中";
                    $info['sign_name'] = "报名";
                    $info['icon']      = 1;
                }else{
                    $info['icon_name'] = "停止报名";
                    $info['sign_name'] = "停止报名";
                    $info['icon']      = 0;
                }
            }
        }else{
            $info['icon_name'] = "停止报名";
            $info['sign_name'] = "停止报名";
            $info['icon']      = 0;
        }
        return $info;
    }

    public function getActivityList(){

		//$result = $this->where(['is_use' => 1])->where(['is_show' => 1])->where("status","neq",4)->order("list_order desc")->select();
		$result = $this->where(['is_show' => 1])->order("list_order desc")->select();
		
        $model  = model("MaterialLibrary");
        foreach ($result as $key => $value) {
            $now        = time();
            $sign_start = $value['sign_start_time'];
            $sign_end   = $value['sign_end_time'];
            if($sign_start>$now){
                $result[$key]['icon_name'] = "未开始";
            }else{
                if($sign_end>=$now){
                    $result[$key]['icon_name'] = "进行中";
                }else{
                    $result[$key]['icon_name'] = "停止报名";
                }
            }
            $top_img = $model->getMaterialInfoById($value['top_img']);
            
            $result[$key]['top_img_url'] = $top_img['url'];
        }
        return $result;
    }
    public function setCollection($id , $sign){
        if(empty($id)){
            $this->error = '活动id不存在';
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
            $collection = explode(',', $user['collection_activity']);
            if($sign == 'true'){
                if(in_array($id, $collection)){
                    $this->error = '该项目已收藏,不能重复收藏';
                    return false;
                } else {
                    array_push($collection, $id);
                    $coll = trim(implode(',', $collection) , ',');
                    $model = model("Member");
                    $model->where(['uid' => $user['uid']])->update(['collection_activity' => $coll]);
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
                    $model->where(['uid' => $user['uid']])->update(['collection_activity' => $coll]);
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
