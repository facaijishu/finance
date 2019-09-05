<?php

namespace app\console\model;

use think\Model;

class PresentRecord extends Model
{
    public function dealPresentRecord($id){
        if(empty($id)){
            $this->error = '该申请id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $result = $this->where(['pr_id' => $id])->find();
            vendor("WxPay.WxPayJsApiPay");
            $xml = "<xml>
            	<amount>".($result['amount'])."</amount>
            	<check_name>".$result['check_name']."</check_name>
            	<desc>".$result['desc']."</desc>
            	<mch_appid>".$result['mch_appid']."</mch_appid>
            	<mchid>".$result['mchid']."</mchid>
            	<nonce_str>".$result['nonce_str']."</nonce_str>
            	<openid>".$result['openid']."</openid>
            	<partner_trade_no>".$result['partner_trade_no']."</partner_trade_no>
            	<spbill_create_ip>".$result['spbill_create_ip']."</spbill_create_ip>
            	<sign>".$result['sign']."</sign>
            </xml>";
            $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers";
            $result1 = \WxPayApi::postXmlCurl($xml, $url , true);
            libxml_disable_entity_loader(true); 
            $xmlstring = simplexml_load_string($result1, 'SimpleXMLElement', LIBXML_NOCDATA); 
            $val = json_decode(json_encode($xmlstring),true); 
            //判断企业打款是否成功
            if($val['return_code'] == 'SUCCESS'){
                if($val['result_code'] == 'SUCCESS'){
                    $info = \app\console\service\User::getInstance()->getInfo();
                    $this->where(['pr_id' => $id])->update(['status' => 1 , 'pay_time' => time() , 'pay_uid' => $info['uid']]);
//                    $userinfo = model("Member")->where(['openId' => $result['openid']])->find();
//                    $array = [
//                        'first' => ['value' => '你好,您的奖金已发放!' , 'color' => '#173177'],
//                        'keyword1' => ['value' => $userinfo['realName'] , 'color' => '#E51717'],
//                        'keyword2' => ['value' => '认证合伙人' , 'color' => '#E51717'],
//                        'keyword3' => ['value' => floatval($result['amount']/100).'元' , 'color' => '#E51717'],
//                        'keyword4' => ['value' => date('Y-m-d H:i:s' , time()) , 'color' => '#E51717'],
//                        'remark' => ['value' => '点击处理查看' , 'color' => '#3B55EA'],
//                    ];
//                    $end = sendTemplateMessage(config("company_play_money"), $result['openid'], $_SERVER['SERVER_NAME'].'/'.url('MyCenter/present_record_list'), $array);
//                    if(!$end){
//                        $this->error = '模板消息发送失败';
//                        return false;
//                    }
                } else {
                    $this->error = $val['err_code'].':'.$val['err_code_des'];
                    return false;
                }
            } else {
                $this->error = $val['return_code'].':'.$val['return_msg'];
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