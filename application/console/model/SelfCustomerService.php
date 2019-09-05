<?php

namespace app\console\model;

use think\Model;

class SelfCustomerService extends Model
{
    public function getSelfCustomerServiceInfoByUid($uid){
        $result = $this->where(['uid' => $uid])->find();
        return $result;
    }
    public function getSelfCustomerServiceInfoById($id){
        $result = $this->where(['scs_id' => $id])->find();
        return $result;
    }

    public function createSelfCustomerService($data){
        if(empty($data)){
            $this->error = '缺少客服数据';
            return false;
        }
        //验证数据信息
        $validate = validate('SelfCustomerService');
        $scene = isset($data['scs_id']) ? 'edit' : 'add';
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }

        //开启事务
        $this->startTrans();
        try{
            $data['kf_account'] = $data['kf_account'].'@'. config("kefu");
            if(isset($data['scs_id'])){
                $arr = [];
                $arr = $data;
                $this->allowField(true)->save($arr , ['scs_id' => $data['scs_id']]);
            } else {
                $customer = $this->where(['kf_account' => $data['kf_account']])->find();
                if(!empty($customer)){
                    $this->error = '客服账号已存在';
                    return false;
                }
                model("User")->where(['uid' => $data['uid']])->update(['is_binding_customer' => 1]);
                $user = model("User")->getUserById($data['uid']);
                $info = \app\console\service\User::getInstance()->getInfo();
                $data['key_number'] = genRandomString(6).'#'.rand(100000000, 999999999);
                $data['status'] = 0;
                $data['real_name'] = $user['real_name'];
                $data['create_time'] = time();
                $data['create_uid'] = $info['uid'];
                $this->allowField(true)->save($data);
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
    public function deleteCustomerService($id){
        //开启事务
        $this->startTrans();
        try{
            $result = $this->where(['scs_id' => $id])->update(['status' => -1]);
            model("Member")->where(['customerService' => $id])->update(['customerService' => 0]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    public function recoverCustomerService($id){
        //开启事务
        $this->startTrans();
        try{
            $result = $this->where(['scs_id' => $id])->update(['status' => 1]);
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
