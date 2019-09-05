<?php

namespace app\console\model;

use think\Model;
use sys\Wechat;

class CustomerService extends Model
{
    public function getCustomerServiceInfoById($id){
        $result = $this->where(['cs_id' => $id])->find();
        return $result;
    }

    public function getCustomerServiceList(){
        $result = $this->where('status','eq',0)->select();
        return $result;
    }

    public function createCustomerService($data){
        if(empty($data)){
            $this->error = '缺少客服数据';
            return false;
        }
        //验证数据信息
        $validate = validate('CustomerService');
        $scene = isset($data['cs_id']) ? 'edit' : 'add';
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }

        //开启事务
        $this->startTrans();
        try{
            $data['kf_account'] = $data['kf_account'].'@'. config("weixin");
            $weObj = new Wechat(config("WeiXinCg"));
            $result = $weObj->checkAuth('', '', getAccessTokenFromFile());
            if(isset($data['cs_id'])){
                $arr = [];
                $arr = $data;
                $this->allowField(true)->save($arr , ['cs_id' => $data['cs_id']]);
                $result = $weObj->updateKFAccount($data['kf_account'], $data['kf_nick'], '123456');
                if(!$result){
                    $code = $weObj->errCode;
                    switch ($code) {
                        case 65400:
                            $error = 'API不可用，即没有开通/升级到新客服功能';break;
                        case 65403:
                            $error = '客服昵称不合法';break;
                        case 65401:
                            $error = '无效客服帐号';break;
                        default :
                            $error = $weObj->errMsg;
                    }
                    $this->error = $error;
                    return false;
                }
            } else {
                $customer = $this->where(['kf_account' => $data['kf_account']])->find();
                if(!empty($customer)){
                    $this->error = '客服账号已存在';
                    return false;
                }
                model("User")->where(['uid' => $data['uid']])->update(['is_binding_customer' => 1]);
                $user = model("User")->getUserById($data['uid']);
                $info = \app\console\service\User::getInstance()->getInfo();
                $data['status'] = 0;
                $data['real_name'] = $user['real_name'];
                $data['createTime'] = time();
                $data['create_uid'] = $info['uid'];
                $this->allowField(true)->save($data);
                $result = $weObj->addKFAccount($data['kf_account'], $data['kf_nick'], '123456');
                if(!$result){
                    $code = $weObj->errCode;
                    switch ($code) {
                        case 65400:
                            $error = 'API不可用，即没有开通/升级到新客服功能';break;
                        case 65403:
                            $error = '客服昵称不合法';break;
                        case 65404:
                            $error = '客服帐号不合法';break;
                        case 65405:
                            $error = '帐号数目已达到上限，不能继续添加';break;
                        case 65406:
                            $error = '已经存在的客服帐号';break;
                        default :
                            $error = $weObj->errMsg;
                    }
                    $this->error = $error;
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
    public function waitBindingCustomer($data){
        if(empty($data)){
            $this->error = '邀请绑定数据为空';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['kf_account' => $data['kf_account']])->update(['status' => 0]);
            $post = '{
                        "kf_account" : "'.$data['kf_account'].'",
                        "invite_wx" : "'.$data['kf_wx'].'"
                     }';
            $url = 'https://api.weixin.qq.com/customservice/kfaccount/inviteworker?access_token='.getAccessTokenFromFile();
            $result = httpPost($url, $post);
            $result = (array)json_decode($result);
            if($result['errcode'] != 0){
                switch ($result['errcode']) {
                    case 65400:
                        $error = 'API不可用，即没有开通/升级到新客服功能';break;
                    case 65401:
                        $error = '无效客服帐号';break;
                    case 65407:
                        $error = '邀请对象已经是本公众号客服';break;
                    case 65408:
                        $error = '本公众号已发送邀请给该微信号';break;
                    case 65409:
                        $error = '无效的微信号';break;
                    case 65410:
                        $error = '邀请对象绑定公众号客服数量达到上限(5)';break;
                    case 65411:
                        $error = '该帐号已经有一个等待确认的邀请，不能重复邀请';break;
                    case 65412:
                        $error = '该帐号已经绑定微信号，不能进行邀请';break;
                    default :
                        $error = $result['errmsg'];
                }
                $this->error = $error;
                return false;
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

    public function synchronizationCustomerService($data){
        if(empty($data)){
            $this->error = '客服数据为空,请添加客服账号';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            foreach ($data as $key => $value) {
                $value = (array)$value;
                if($value['kf_headimgurl'] != ''){
                    continue;
                }
                unset($value['kf_headimgurl']);
                $result = $this->where(['kf_account' => $value['kf_account']])->find();
                if(empty($result)){
                    if($value['kf_wx'] != ''){
                        $value['status'] = 1;
                        $this->allowField(true)->save($value);
                    } else {
                        $this->allowField(true)->save($value);
                    }
                } else {
                    $arr = $value;
                    unset($arr['kf_account']);
                    if(isset($value['kf_wx']) && $value['kf_wx'] != ''){
                        $arr['status'] = 1;
                        $this->where(['kf_account' => $value['kf_account']])->update($arr);
                    } else {
                        $this->where(['kf_account' => $value['kf_account']])->update($arr);
                    }
                    $model = model("MaterialLibrary");
                    $info = $model->getMaterialInfoById($result['kf_headimgurl']);
                    $path = ROOT_PATH . 'public' . DS . 'uploads' . DS . $info['url_thumb'];
                    $url = 'https://api.weixin.qq.com/customservice/kfaccount/uploadheadimg?access_token='. getAccessTokenFromFile().'&kf_account='.$value['kf_account'];
                    $data = ['media' => new \CURLFile(realpath($path))];
                    $result = httpPost($url, $data);
                    $result = (array)json_decode($result);
                    var_dump($result);
                    if($result['errcode'] != 0){
                        $this->error = $result['errmsg'];
                        return false;
                    }
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
    public function deleteCustomerService($id){
        //开启事务
        $this->startTrans();
        try{
            $info = $this->where(['cs_id' => $id])->find();
            $result = $this->where(['cs_id' => $id])->update(['status' => -1]);
            model("Member")->where(['customerService' => $id])->update(['customerService' => 0]);
            if($result){
                if($user = model("User")->getUserById($info['uid'])){
                    model("User")->where(['uid' => $info['uid']])->update(['is_binding_customer' => -1]);
                }
                $weObj = new Wechat(config("WeiXinCg"));
                $weObj->checkAuth('', '', getAccessTokenFromFile());
                $result = $weObj->deleteKFAccount($info['kf_account']);
                if(!$result){
                    $code = $weObj->errCode;
                    switch ($code) {
                        case 65400:
                            $error = 'API不可用，即没有开通/升级到新客服功能';break;
                        case 65401:
                            $error = '无效客服帐号';break;
                        default :
                            $error = $weObj->errMsg;
                    }
                    $this->error = $error;
                    return false;
                }
            } else {
                $this->error = '数据库删除失败';
                return false;
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