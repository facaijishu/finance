<?php

namespace app\home\controller;
use sys\Wechat;

class CustomerService extends Base {

    public function __construct() {
        //获取公众号配置信息
        parent::__construct();
        $this->AllowTime = config('AllowTime');
    }

    public function index() {
        $id = $this->request->param('id/d');
        $AccessToken = getAccessTokenFromFile();
        $user = session('FINANCE_USER');
        $result = $this->isFollow($user['openId'] , $AccessToken);
//        if ($result) {
//            $model = model("SelfCustomerService");
//            if ($user['customerService'] == 0) {
//                $service = $model->getRandomCustomerService($user['uid']);
//            } else {
//                $service = $model->getCustomerServiceById($user['customerService']);
//                if(empty($service)){
//                    $service = $model->getRandomCustomerService($user['uid']);
//                }
//            }
//            $model = model("MaterialLibrary");
//            $kf_qr_code = $model->getMaterialInfoById($service['kf_qr_code']);
//            $service['kf_qr_code'] = $kf_qr_code['url_thumb'];
//            $this->assign('customerService', $service);
//            $this->assign('title' , '联系客服-金融合伙人');
//            $this->assign('img' , '');
//            $this->assign('des' , '金融合伙人客服很高兴为您服务!');
//            return view();
//        }
//        else {
            $model = model("project");
            $info = $model->getProjectSimpleInfoById($id);
            $this->assign('url' , $info['qr_code']);
            $this->assign('title' , '- 联系客服');
            $this->assign('img' , '');
            $this->assign('des' , '金融合伙人客服很高兴为您服务!');
            return view("public_signal");
//        }
    }
    //创建会话
    public function createConversation() {
        $model = model("CustomerService");
        $AccessToken = getAccessTokenFromFile();
        $openId = isset($_GET['openId']) ? $_GET['openId'] : session('FINANCE_USER.openId');
        if(session('FINANCE_USER.customerService') == 0){
            $model->getRandomCustomerService(session('FINANCE_USER.uid'));
        }
        $id = session('FINANCE_USER.customerService');
        $allow_time = date('H', time());
        $weObj = new Wechat(config("WeiXinCg"));
        $result = $weObj->checkAuth('', '', $AccessToken);
        if ($allow_time > $this->AllowTime['start'] && $allow_time < $this->AllowTime['end']) {
//            $id = $this->request->param('id/d');
//            $model = model("CustomerService");
            $service = $model->getCustomerServiceById($id);
            $result = $weObj->createKFSession($openId, $service['kf_account']);
            if (!$result) {
                if($weObj->errCode == '65415'){
                    //发送客服列表
                    $customerService = $weObj->getCustomServiceOnlineKFlist();
                    $customerService = $customerService['kf_online_list'];
                    if ($customerService != null) {
                        $kf = $this->getRanDomCustomerService($customerService);
                        $result = $weObj->createKFSession($openId, $kf['kf_account']);
                        $data = ['touser'=>$openId , 'msgtype' => 'text' , 'text' => ['content' => '您好,很高兴为你服务']];
                        $result = $weObj->sendCustomMessage($data);
//                        $result['content'] = '绑定客服不存在,将随机匹配在线客服';
//                        $this->result($result);
                        return;
                    } else {
                        $data = ['touser'=>$openId , 'msgtype' => 'text' , 'text' => ['content' => '抱歉,目前没有客服在线']];
                        $result = $weObj->sendCustomMessage($data);
//                        $result['content'] = '抱歉,指定客服不存在且没有其他客服在线';
//                        $this->result($result);
                        return;
                    }
                } else {
//                    $result['errmsg'] = $weObj->errMsg;
//                    $result['errcode'] = $weObj->errCode;
//                    $this->result($result);
                    return;
                }
            } else {
                $data = ['touser'=>$openId , 'msgtype' => 'text' , 'text' => ['content' => '您好,很高兴为你服务']];
                $result = $weObj->sendCustomMessage($data);
//                $result['content'] = '创建会话';
//                $this->result($result);
                return;
            }
        } else {
//            $url = 'http://fin.jrfacai.com/home/check_wechat/checkSignature';
//            $xml = '<xml>
//                        <ToUserName><![CDATA[gh_842688629d52]]></ToUserName>
//                        <FromUserName><![CDATA['.$openId.']]></FromUserName>
//                        <CreateTime>1348831860</CreateTime>
//                        <MsgType><![CDATA[text]]></MsgType>
//                        <Content><![CDATA[this is a test]]></Content>
//                        <MsgId>1234567890123456</MsgId>
//                    </xml>';
            $data = ['touser'=>$openId , 'msgtype' => 'text' , 'text' => ['content' => '客服服务时间为:上午'. $this->AllowTime['start'] .':00-晚上'.$this->AllowTime['end'].':00']];
            $result = $weObj->sendCustomMessage($data);
            $result['content'] = '不在客服时间内';
            $this->result($result);
        }
    }

    //判断是否关注公众号
//    public function isFollow($openId , $access_token) {
//        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $access_token . '&openid=' . $openId . '&lang=zh_CN';
//        $result = httpGet($url);
//        $result = json_decode($result, true);
//        if ($result['subscribe'] == 0) {
//            return false;
//        } elseif ($result['subscribe'] == 1) {
//            return true;
//        }
//    }
    //获取在线随机客服
    private function getRanDomCustomerService($customerService) {
        //算法可改
        shuffle($customerService);
        return $customerService[0];
    }
}
