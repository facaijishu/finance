<?php

namespace app\console\model;

use think\Model;

class SendError extends Model
{      
        protected $resultSetType = 'collection';
	public function addSendErrorInfo($data)
	{
		if(empty($data)){
                 $this->error = '缺少消息推送失败用户数据';
                 return false;
                }

               //开启事务
                $this->startTrans();
                try{
                    $this->allowField(true)->saveAll($data);
               // 提交事务
                    $this->commit();
                    return true;
                } catch (\Exception $e) {
                  // 回滚事务
                  $this->rollback();
                  return false;
                }
	}
        public function getSendErrorByActId($id , $start = '' , $length = '') {
         if(empty($id)){
            $this->error = '该推送id不存在';
            return false;
         }
         if($start == '' && $length == ''){
            $limit = '';
         } else {
            $limit = $start.','.$length;
         }
          $result = $this->where(['send_id' => $id])->limit($limit)->order('id desc')->select();
          return $result->toArray();
      }
      
}
