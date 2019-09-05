<?php

namespace app\home\model;

use think\Model;

class PresentRecord extends Model
{
    public function getPresentRecordList(){
        $user = session('FINANCE_USER');
        $list = $this->where(['create_uid' => $user['uid']])->select();
        foreach ($list as $key => $value) {
            $list[$key]['amount'] = floatval($value['amount']/100);
        }
        return $list;
    }

    public function createPresentRecord($data){
        if(empty($data)){
            $this->error = '提现数据不存在';
            return false;
        }
        //验证数据信息
        $validate = validate('PresentRecord');
        $scene = 'add';
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }
        //验证金额信息
        $user = session('FINANCE_USER');
        $model = model("Member");
        $userinfo = $model->getMemberInfoById($user['uid']);
        if(empty($data['fee']) || $data['fee'] == 0 || $data['fee'] < 1 || $data['fee'] > $userinfo['balance']){
            $this->error = '提现金额不正确';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            vendor("WxPay.WxPayJsApiPay");
            $pay['mch_appid'] = \WxPayConfig::APPID;
            $pay['mchid'] = \WxPayConfig::MCHID;
            $pay['nonce_str'] = \WxPayConfig::MCHID.date("YmdHis");
            $pay['partner_trade_no'] = date("YmdHis"). intval($data['fee']*100);
            $pay['openid'] = $userinfo['openId'];
            $pay['check_name'] = 'NO_CHECK';
            $pay['amount'] = intval($data['fee']*100);
            $pay['desc'] = '申请提现';
            $pay['spbill_create_ip'] = get_client_ip();
            $stringA = "amount=".$pay['amount']."&check_name=".$pay['check_name']."&desc=".$pay['desc']."&mch_appid=".$pay['mch_appid']."&mchid=".$pay['mchid']."&nonce_str=".$pay['nonce_str']."&openid=".$pay['openid']."&partner_trade_no=".$pay['partner_trade_no']."&spbill_create_ip=".$pay['spbill_create_ip'];
            $stringSignTemp = $stringA."&key=".\WxPayConfig::KEY;
            $sign = strtoupper(md5($stringSignTemp));
            $pay['sign'] = $sign;
            $pay['status'] = 0;
            $pay['create_time'] = time();
            $pay['pay_time'] = 0;
            $pay['create_uid'] = $userinfo['uid'];
            $pay['pay_uid'] = 0;
            $this->allowField(true)->save($pay);
            $balance = floatval($userinfo['balance'])-floatval($data['fee']);
            model("Member")->where(['uid' => $userinfo['uid']])->update(['balance' => $balance]);
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