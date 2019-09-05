<?php
namespace app\home\controller;
use sys\Wechat;
class CheckWechat {
    //放在微信拦截器之外
    public function checkSignature() {
        $config = config("WeiXinCg");
        $options = array(
            'token' => config("token"), //填写你设定的key
            'encodingaeskey' => config("encodingaeskey"), //填写加密用的EncodingAESKey，如接口为明文模式可忽略
            'appid' => $config['appid'],
            'appsecret' => $config['appsecret']
        );
        $weObj = new Wechat($options);
        $weObj->valid(); //明文或兼容模式可以在接口验证通过后注释此句，但加密模式一定不能注释，否则会验证失败
        $type = $weObj->getRev()->getRevType();
        $openId = $weObj->getRevFrom();
        $weObj->checkAuth('', '', getAccessTokenFromFile());
        switch ($type) {
            case Wechat::MSGTYPE_TEXT:
                $text = $weObj->getRevContent();
                $text = trim($text, "\n ");
                $length = strlen($text);
                if($length == 16 && $text[6] == '#'){
                    $model = model("SelfCustomerService");
                    $info = $model->where(['key_number' => $text])->find();
                    if(empty($info)){
                        $weObj->text('该客服秘钥不存在,请重试')->reply();
                    } else {
                        if($info['status'] != 0){
                            $weObj->text($info['status'])->reply();
                        } else {
                            $result = $model->where(['key_number' => $text])->update(['kf_openId' => $openId , 'status' => 1]);
                            $weObj->text('恭喜您已绑定客服成功,客服账号为【'.$info['kf_account'].'】,昵称是'.$info['kf_nick'])->reply();
                        }
                    }
                } else {
                    $Member = model("Member");
                    $info = $Member->where(['openId' => $openId])->find();
                    $CustomerService = model("SelfCustomerService");
                    if($info['customerService'] == 0){
                        $service = $CustomerService->getRandomCustomerService($info['uid']);
                    } else {
                        $service = $CustomerService->getCustomerServiceById($info['customerService']);
                    }
                    $kf_message = model("KfMessage");
                    $kf_message_last = model("KfMessageLast");
                    $last = $kf_message_last->where(['openId' => $openId])->find();
                    if(empty($last)){
                        $arr = [];
                        $arr['m_uid'] = $info['uid'];
                        $arr['openId'] = $openId;
                        $arr['direction'] = '-->';
                        $arr['scs_id'] = $service['scs_id'];
                        $arr['is_read'] = 1;
                        $arr['last_time'] = time();
                        $arr['first_time'] = time();
                        $kf_message_last->allowField(true)->save($arr);
                    }else {
                        if($last['first_time'] == 0){
                            $arr = [];
                            $arr['direction'] = '-->';
                            $arr['is_read'] = $last['is_read']<99 ? $last['is_read']+1 : 99;
                            $arr['last_time'] = time();
                            $arr['first_time'] = time();
                            $kf_message_last->where(['openId' => $openId])->update($arr);
                        } else {
                            $arr = [];
                            $arr['direction'] = '-->';
                            $arr['is_read'] = $last['is_read']<99 ? $last['is_read']+1 : 99;
                            $arr['last_time'] = time();
                            $kf_message_last->where(['openId' => $openId])->update($arr);
                        }
                    }
                    $data = [];
                    $data['m_uid'] = $info['uid'];
                    $data['openId'] = $openId;
                    $data['scs_id'] = $service['scs_id'];
                    $data['content'] = $text;
                    $data['create_time'] = time();
                    $kf_message->allowField(true)->save($data);
                    $id = $kf_message->getLastInsID();
                    $kf_info = $kf_message->where(['km_id' => $id])->find();
                    sleep(3);
                    $last = $kf_message_last->where(['openId' => $openId])->find();
                    if($last['is_read']){
                        $data = [
                            'touser' => $service['kf_openId'],
                            'template_id' => 'rQ53Qg49ZU-9oOCINXeqTMydpBsu-VaIgGwJuF9Oz-U',
                            'url' => $_SERVER['SERVER_NAME'].'/'. url("InstantMessaging/index").'/uid/'.$info['uid'],
                            'topcolor' => '#FF0000',
                            'data' => [
                                'first' => ['value' => '你好，你有'.$last['is_read'].'条用户咨询待解决' , 'color' => '#173177'],
                                'keyword1' => ['value' => htmlspecialchars($info['userName'] , ENT_QUOTES) , 'color' => '#E51717'],
                                'keyword2' => ['value' => $this->getText($kf_info['content']) , 'color' => '#E51717'],
                                'remark' => ['value' => '点击处理咨询' , 'color' => '#3B55EA'],
                            ]
                        ];
                        $result = $weObj->sendTemplateMessage($data);
                        if(!$result){
//                            $weObj->text(htmlspecialchars)->reply();
                            $weObj->text('错误原因1：'.$weObj->errCode."-".$weObj->errMsg)->reply();
                        }
//                        else {
//                            $weObj->text('已发送给绑定客服,请耐心等待回复12345')->reply();
//                        }
                    }
//                    else {
//                        $weObj->text('信息已处理,不发送模板消息,不做任何处理12345')->reply();
//                    }
                }
                break;
            case Wechat::MSGTYPE_EVENT:
                $result = $weObj->getRevEvent();
                if($result['event'] == 'CLICK' && $result['key'] == '客服'){
                    $weObj->text('请直接在公众号中输入内容,将有专属客服为您解答')->reply();
                    break;
                }
                //关注发生的回调
                if($result['event'] == 'subscribe' || $result['event'] == 'SCAN'){
                    if($result['event'] == 'subscribe'){
                        $id = trim(strstr($result['key'], '_'), '_');
                        $data = ['touser'=>$openId , 'msgtype' => 'text' , 'text' => ['content' => '您好,欢迎关注此公众号!']];
                        $weObj->sendCustomMessage($data);
                    } else {
                        $id = $result['key'];
                    }
                    $Member = model("Member");
                    $info = $Member->where(['openId' => $openId])->find();
                    if(empty($info)){
                        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='. getAccessTokenFromFile().'&openid='.$openId.'&lang=zh_CN';
                        $result = httpGet($url);
                        $result = json_decode($result, true);
//                        $weObj->text(json_encode($result))->reply();
                        $result == isset($result['errcode']) ? array() : $result;
                        $data = array();
                        $data['userType'] = 0;
                        $data['superior'] = 0;
                        $data['userName'] = htmlspecialchars($result['nickname']);
                        $data['userSex'] = $result['sex']==0 ? 3 : $result['sex']==1 ? 1 : 2;
                        $data['userPhone'] = '';
                        $data['createTime'] = time();
                        $data['CREATE_TIME'] = date('Y-m-d H:i:s' , time());
                        $data['userPhoto'] = $result['headimgurl'];
                        $data['lastTime'] = time();
                        $data['lastIP'] = get_client_ip();
                        $data['openId'] = $result['openid'];
                        $data['status'] = 1;
                        $result = $Member->allowField(true)->save($data);
                    }
                    $info = $Member->where(['openId' => $openId])->find();
                    $CustomerService = model("SelfCustomerService");
                    if($info['customerService'] == 0){
                        $service = $CustomerService->getRandomCustomerService($info['uid']);
                    } else {
                        $service = $CustomerService->getCustomerServiceById($info['customerService']);
                    }
                    $model = model("project");
                    $info = $model->getProjectSimpleInfoById($id);
                    $title = '您的专属客服是【'.$service['kf_account'].'】,您可在公众号直接与您的客服聊天!';
                    $urll = 'http://'.$_SERVER['SERVER_NAME'].'/static/common/LOGO.png';
                    $model = model("SystemConfig");
                    $set = $model->getSystemConfigInfo();
                    if(empty($info)){
                        $info['pro_name'] = '金融合伙人';
                        $info['top_img'] = 'system/img/logo.jpg';
                        if(!empty($set) && $set['title'] != ''){
                            $info['pro_name'] = $set['title'];
                        }
                        if(!empty($set) && $set['logo_img'] != ''){
                            $info['top_img'] = $set['logo_img_url'];
                        }
                        $info['Url'] = $_SERVER['SERVER_NAME'].'/home/index/index';
                    } else {
                        $info['Url'] = $_SERVER['SERVER_NAME'].'/home/project_info/index/id/'.$id;
                    }
                    if(!empty($set) && $set['sign'] != ''){
                        $title = str_replace(['$kf_account','$kf_nick'], [$service['kf_account'],$service['kf_nick']], $set['sign']);
                    }
                    if(!empty($set) && $set['kf_img'] != ''){
                        $urll = 'http://'.$_SERVER['SERVER_NAME'].'/uploads/'.$set['kf_img_url'];
                    }
                    $data = [
                        [
                            "Title" => $info['pro_name'],
                            "Description" => '',
                            "Url" => $info['Url'],
                            "PicUrl" => 'http://'.$_SERVER['SERVER_NAME'].'/uploads/'.$info['top_img']
                        ],
                        [
                            "Title" => $title,
                            "Description" => '',
                            "Url" => $info['Url'],
                            "PicUrl" => $urll
                        ]
                    ];
                    $weObj->news($data)->reply();
                }
                break;
            case Wechat::MSGTYPE_VOICE:
                $result = $weObj->getRevVoice();
                $Member = model("Member");
                $info = $Member->where(['openId' => $openId])->find();
                $CustomerService = model("SelfCustomerService");
                if($info['customerService'] == 0){
                    $service = $CustomerService->getRandomCustomerService($info['uid']);
                } else {
                    $service = $CustomerService->getCustomerServiceById($info['customerService']);
                }
                $kf_message = model("KfMessage");
                $kf_message_last = model("KfMessageLast");
                $last = $kf_message_last->where(['openId' => $openId])->find();
                if(empty($last)){
                    $arr = [];
                    $arr['m_uid'] = $info['uid'];
                    $arr['openId'] = $openId;
                    $arr['direction'] = '-->';
                    $arr['scs_id'] = $service['scs_id'];
                    $arr['is_read'] = 1;
                    $arr['last_time'] = time();
                    $kf_message_last->allowField(true)->save($arr);
                }else {
                    if($last['first_time'] == 0){
                        $arr = [];
                        $arr['direction'] = '-->';
                        $arr['is_read'] = $last['is_read']<99 ? $last['is_read']+1 : 99;
                        $arr['last_time'] = time();
                        $arr['first_time'] = time();
                        $kf_message_last->where(['openId' => $openId])->update($arr);
                    } else {
                        $arr = [];
                        $arr['direction'] = '-->';
                        $arr['is_read'] = $last['is_read']<99 ? $last['is_read']+1 : 99;
                        $arr['last_time'] = time();
                        $kf_message_last->where(['openId' => $openId])->update($arr);
                    }
                }
//                $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.getAccessTokenFromFile().'&media_id='.$result['mediaid'];
//                $content = httpGet($url);
                $content = $weObj->getMedia($result['mediaid']);
                $filename = genRandomString(8).rand(1000, 9999);
                $dir = 'uploads' . DS . 'home/voice/'. date('Ymd' , time());
                if(!is_dir(ROOT_PATH . 'public' . DS . $dir)){
                    mkdir($dir , 0777, true);
                }
                $file = fopen(ROOT_PATH . 'public' . DS . $dir.'/'.$filename.'.amr', 'w');
                fwrite($file, $content);
                fclose($file);
                exec('ffmpeg -i '.ROOT_PATH.'public/'.$dir.'/'.$filename.'.amr -ab 96 -ac 2 '.ROOT_PATH.'public/'.$dir.'/'.$filename.'.mp3');
                $data = [];
                $data['m_uid'] = $info['uid'];
                $data['openId'] = $openId;
                $data['scs_id'] = $service['scs_id'];
                $data['type'] = 'voice';
                $data['content'] = $dir.'/'.$filename.'.mp3';
                $data['create_time'] = time();
                $kf_message->allowField(true)->save($data);
                sleep(3);
                $last = $kf_message_last->where(['openId' => $openId])->find();
                if($last['is_read']){
                    $data = [
                        'touser' => $service['kf_openId'],
                        'template_id' => 'rQ53Qg49ZU-9oOCINXeqTMydpBsu-VaIgGwJuF9Oz-U',
                        'url' => $_SERVER['SERVER_NAME'].'/'. url("InstantMessaging/index").'/uid/'.$info['uid'],
                        'topcolor' => '#FF0000',
                        'data' => [
                            'first' => ['value' => '你好，你有'.$last['is_read'].'条用户咨询待解决' , 'color' => '#173177'],
                            'keyword1' => ['value' => htmlspecialchars($info['userName'] , ENT_QUOTES) , 'color' => '#E51717'],
                            'keyword2' => ['value' => '【一条语音】' , 'color' => '#E51717'],
                            'remark' => ['value' => '点击处理咨询' , 'color' => '#3B55EA'],
                        ]
                    ];
                    $result = $weObj->sendTemplateMessage($data);
                    if(!$result){
                        $weObj->text('错误原因1：'.$weObj->errCode."-".$weObj->errMsg)->reply();
                    } 
//                    else {
//                        $weObj->text('已发送给绑定客服,请耐心等待回复')->reply();
//                    }
                }
//                else {
//                    $weObj->text('发送成功')->reply();
//                }
                break;
            case Wechat::MSGTYPE_IMAGE:
                $result = $weObj->getRevPic();
                $Member = model("Member");
                $info = $Member->where(['openId' => $openId])->find();
                $CustomerService = model("SelfCustomerService");
                if($info['customerService'] == 0){
                    $service = $CustomerService->getRandomCustomerService($info['uid']);
                } else {
                    $service = $CustomerService->getCustomerServiceById($info['customerService']);
                }
                $kf_message = model("KfMessage");
                $kf_message_last = model("KfMessageLast");
                $last = $kf_message_last->where(['openId' => $openId])->find();
                if(empty($last)){
                    $arr = [];
                    $arr['m_uid'] = $info['uid'];
                    $arr['openId'] = $openId;
                    $arr['direction'] = '-->';
                    $arr['scs_id'] = $service['scs_id'];
                    $arr['is_read'] = 1;
                    $arr['last_time'] = time();
                    $kf_message_last->allowField(true)->save($arr);
                }else {
                    if($last['first_time'] == 0){
                        $arr = [];
                        $arr['direction'] = '-->';
                        $arr['is_read'] = $last['is_read']<99 ? $last['is_read']+1 : 99;
                        $arr['last_time'] = time();
                        $arr['first_time'] = time();
                        $kf_message_last->where(['openId' => $openId])->update($arr);
                    } else {
                        $arr = [];
                        $arr['direction'] = '-->';
                        $arr['is_read'] = $last['is_read']<99 ? $last['is_read']+1 : 99;
                        $arr['last_time'] = time();
                        $kf_message_last->where(['openId' => $openId])->update($arr);
                    }
                }
                $content = $weObj->getMedia($result['mediaid']);
                $filename = genRandomString(8).rand(1000, 9999);
                $dir = 'uploads' . DS . 'home/img/'. date('Ymd' , time());
                if(!is_dir(ROOT_PATH . 'public' . DS . $dir)){
                    mkdir($dir , 0777, true);
                }
                $file = fopen(ROOT_PATH . 'public' . DS . $dir.'/'.$filename.'.jpg', 'w');
                fwrite($file, $content);
                fclose($file);
                $data = [];
                $data['m_uid'] = $info['uid'];
                $data['openId'] = $openId;
                $data['scs_id'] = $service['scs_id'];
                $data['type'] = 'image';
                $data['content'] = '/'.$dir.'/'.$filename.'.jpg';
                $data['create_time'] = time();
                $kf_message->allowField(true)->save($data);
                sleep(3);
                $last = $kf_message_last->where(['openId' => $openId])->find();
                if($last['is_read']){
                    $data = [
                        'touser' => $service['kf_openId'],
                        'template_id' => 'rQ53Qg49ZU-9oOCINXeqTMydpBsu-VaIgGwJuF9Oz-U',
                        'url' => $_SERVER['SERVER_NAME'].'/'. url("InstantMessaging/index").'/uid/'.$info['uid'],
                        'topcolor' => '#FF0000',
                        'data' => [
                            'first' => ['value' => '你好，你有'.$last['is_read'].'条用户咨询待解决' , 'color' => '#173177'],
                            'keyword1' => ['value' => htmlspecialchars($info['userName'] , ENT_QUOTES) , 'color' => '#E51717'],
                            'keyword2' => ['value' => '【一张图片】' , 'color' => '#E51717'],
                            'remark' => ['value' => '点击处理咨询' , 'color' => '#3B55EA'],
                        ]
                    ];
                    $result = $weObj->sendTemplateMessage($data);
                    if(!$result){
                        $weObj->text('错误原因1：'.$weObj->errCode."-".$weObj->errMsg)->reply();
                    }
//                    else {
//                        $weObj->text('已发送给绑定客服,请耐心等待回复')->reply();
//                    }
                } 
//                else {
//                    $weObj->text('信息已处理,不发送模板消息,不做任何处理')->reply();
//                }
                break;
            default:
                $weObj->text("如有疑问,请点击下方列表按钮查看")->reply();
        }
    }
    public function getText($text){
        $length = strlen($text);
        if($length > 25){
            $text = substr($text, 0, 25).'...';
        }
        $search = array(" ","　","\n","\r","\t");
        $replace = array("","","","","");
        $text = str_replace($search, $replace, $text);
        $text = htmlspecialchars($text , ENT_QUOTES);
        return $text;
    }
}