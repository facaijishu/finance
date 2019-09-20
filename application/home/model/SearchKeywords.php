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
    
    public function getSearchCountApi($uid){
        $res  = [];
        if($uid>0){
            $count = $this->where(['uid' => $uid,'del_flg' => 0])->count();
            $res['code'] = 200;
            $res['msg']  = '';
            $res['data'] = $count;
            return $res;
        }else{
            $res['code'] = 201;
            $res['msg']  = '';
            $res['data'] = 0;
            return $res;
        }
    }
    
    public function getSearchByUidApi($uid){
        $res  = []; 
        if($uid>0){
            $list = $this->field("distinct keyword")->where(['uid' => $uid,'del_flg' => 0])->order("id desc")->select();
            if(empty($list)){
                $res['code'] = 201;
                $res['msg']  = '数据不存在';
                $res['data'] = '';
                return $res;
            }else{
                $data = [];
                foreach ($list as $key => $item) {
                    $list[$key]['s_id'] = "sid_".$key;
                    $data[] = $item->toArray();
                }
                $return['data'] = $data;
                $res['code'] = 200;
                $res['msg'] = '';
                $res['data'] = $data;
                return $res;
            }
        }else{
            $res['code'] = 201;;
            $res['msg']  = '数据不存在';
            $res['data'] = '';
            return $res;
        }
        
    }
    
    public function delSearchByUidApi($uid,$keywords){
        
        $data['del_flg']    = 1;
        $update = $this->where(['uid' => $uid,'keyword'=>$keywords])->update($data);
        
        $res  = [];
        if($update){
                $res['code']    = 200;
                $res['msg']     = '';
                return $res;
        }else{
            $res['code']    = 201;
            $res['msg']     = '删除处理失败';
            return $res;
        }
    }
    
    
    public function delAllSearchByUidApi($uid){
        
        $data['del_flg']    = 1;
        $update = $this->where(['uid' => $uid])->update($data);
        
        $res  = [];
        if($update){
            $res['code']    = 200;
            $res['msg']     = '';
            return $res;
        }else{
            $res['code']    = 201;
            $res['msg']     = '删除处理失败';
            return $res;
        }
    }
}
