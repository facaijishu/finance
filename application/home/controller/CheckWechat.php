<?php
namespace app\home\controller;
use sys\Wechat;
class CheckWechat {
    //放在微信拦截器之外
    public function checkSignature() {
        $config = config("WeiXinCg");
        $options = array(
            'token'             => config("token"), //填写你设定的key
            'encodingaeskey'    => config("encodingaeskey"), //填写加密用的EncodingAESKey，如接口为明文模式可忽略
            'appid'             => $config['appid'],
            'appsecret'         => $config['appsecret']
        );
        $weObj  = new Wechat($options);
        
        $weObj->valid(); //明文或兼容模式可以在接口验证通过后注释此句，但加密模式一定不能注释，否则会验证失败
        
        $type   = $weObj->getRev()->getRevType();
        $openId = $weObj->getRevFrom();
        
        $weObj->checkAuth('', '', getAccessTokenFromFile());
        
        $back   = $this->pullback($openId);
        if(!$back){
            $weObj->text('您已经被客服管理人员屏蔽!')->reply();
            break;
        }
        switch ($type) {
            case Wechat::MSGTYPE_TEXT://客户人员设定
                $text   = $weObj->getRevContent();
                $text   = trim($text, "\n ");
                $length = strlen($text);
                if($length == 16 && $text[6] == '#'){
                    $model  = model("SelfCustomerService");
                    $info   = $model->where(['key_number' => $text])->find();
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
                    $info   = $Member->where(['openId' => $openId])->find();
                    
                    $CustomerService = model("SelfCustomerService");
                    if($info['customerService'] == 0){
                        $service = $CustomerService->getRandomCustomerService($info['uid']);
                    } else {
                        $service = $CustomerService->getCustomerServiceById($info['customerService']);
                    }
                    //用户发送文本消息时，判断有没有先扫机构中心二维码
                    $memberOrganize     = model("MemberOrganize");
                    $memberOrganizeInfo = $memberOrganize->getMemberOrganizeInfoByMid($info['uid']);
                    //如果有扫过机构码
                    if(!empty($memberOrganizeInfo)) {
                        //判断startTime+1800>time()?
                        $startTime = $memberOrganizeInfo['startTime'];
                        if ($startTime + 1800 > time()) {
                            //在5分钟有效期内,当前客服替换为机构客服
                            $org_customer_service = $memberOrganizeInfo['org_customer_service'];
                            $CustomerService = model("SelfCustomerService");
                            $service = $CustomerService->getCustomerServiceById($org_customer_service);
                            //同时更新startTime为当前时间
                            $newTime = ['startTime' => time()];
                            $memberOrganize->where(['mid' => $info['uid']])->update($newTime);
                        }else{
                            
                        }
                    }
                    
                    $kf_message         = model("KfMessage");
                    $kf_message_last    = model("KfMessageLast");
                    $last = $kf_message_last->where(['openId' => $openId])->find();
                    if(empty($last)){
                        $arr = [];
                        $arr['m_uid']       = $info['uid'];
                        $arr['openId']      = $openId;
                        $arr['direction']   = '-->';
                        $arr['scs_id']      = $service['scs_id'];
                        $arr['is_read']     = 1;
                        $arr['last_time']   = time();
                        $arr['first_time']  = time();
                        $kf_message_last->allowField(true)->save($arr);
                    }else {
                        if($last['first_time'] == 0){
                            $arr = [];
                            $arr['direction']   = '-->';
                            $arr['is_read']     = $last['is_read']<99 ? $last['is_read']+1 : 99;
                            $arr['last_time']   = time();
                            $arr['first_time']  = time();
                            $kf_message_last->where(['openId' => $openId])->update($arr);
                        } else {
                            $arr = [];
                            $arr['direction']   = '-->';
                            $arr['is_read']     = $last['is_read']<99 ? $last['is_read']+1 : 99;
                            $arr['last_time']   = time();
                            $kf_message_last->where(['openId' => $openId])->update($arr);
                        }
                    }
                    $data = [];
                    $data['m_uid']       = $info['uid'];
                    $data['openId']      = $openId;
                    $data['scs_id']      = $service['scs_id'];
                    $data['content']     = $text;
                    $data['create_time'] = time();
                    $kf_message->allowField(true)->save($data);
                    
                    $id         = $kf_message->getLastInsID();
                    $kf_info    = $kf_message->where(['km_id' => $id])->find();
                    sleep(3);
                    $last       = $kf_message_last->where(['openId' => $openId])->find();
                    if($last['is_read']){
                        $data = [
                                'touser'        => $service['kf_openId'],
                                'template_id'   => config('chat_content'),
                                'url'           => $_SERVER['SERVER_NAME'].'/'. url("InstantMessaging/index").'/uid/'.$info['uid'],
                                'topcolor'      => '#FF0000',
                                'data'          => [
                                                    'first'     => ['value' => '你好，你有'.$last['is_read'].'条用户咨询待解决' , 'color' => '#173177'],
                                                    'keyword1'  => ['value' => htmlspecialchars($info['userName'] , ENT_QUOTES) , 'color' => '#E51717'],
                                                    'keyword2'  => ['value' => $this->getText($kf_info['content']) , 'color' => '#E51717'],
                                                    'remark'    => ['value' => '点击处理咨询' , 'color' => '#3B55EA'],
                                                   ]
                                ];
                        $result = $weObj->sendTemplateMessage($data);
                        
                        if(!$result){
                            $weObj->text('错误原因1：'.$weObj->errCode."-".$weObj->errMsg)->reply();
                        }
                    }
                }
                break;
            case Wechat::MSGTYPE_EVENT:
                $result = $weObj->getRevEvent();
                
                //获取用户信息
                $Member = model("Member");
                $info   = $Member->where(['openId' => $openId])->find();
                
                //公众号内客服对话
                if($result['event'] == 'CLICK' && $result['key'] == '客服'){
                    $weObj->text('请直接在公众号中输入内容,将有专属客服为您解答')->reply();
                    break;
                }
                
                //取关公众号
                if($result['event'] == 'unsubscribe'){
                    $unsubscribe         = model("Unsubscribe");
                    $data = [];
                    $data['openId']      = $openId;
                    $data['addtime']     = time();
                    $data['type']        = 2;
                    $unsubscribe->allowField(true)->save($data);
                    if(!empty($info)){
                        $modelM = model("Member");
                        $modelM->updateFollow($openId,0);
                    }
                    break;
                }
                
                //关注公众号
                if($result['event'] == 'subscribe'){
                    $unsubscribe         = model("Unsubscribe");
                    $data = [];
                    $data['openId']      = $openId;
                    $data['addtime']     = time();
                    $data['type']        = 1;
                    $unsubscribe->allowField(true)->save($data);
                    if(!empty($info)){
                        $modelM = model("Member");
                        $modelM->updateFollow($openId,1);
                    }

                    //$media_id = "Nsa7kVlRtXICvXlqfrUUpS2A1QcE-VzdPr7cCoVg_1A";
                    $media_id = "sIzlRivrNW-37oNWPU2W60ekoA3bQa-hjde1IOOKLAo";
                    $weObj->voice($media_id)->reply();
                    break;
                }
                
                //扫描短链接二维码
                if($result['event'] == 'SCAN'){
                    $id = $result['key'];
                    //$media_id = "Nsa7kVlRtXICvXlqfrUUpS2A1QcE-VzdPr7cCoVg_1A";
                    //$media_id = "sIzlRivrNW-37oNWPU2W60ekoA3bQa-hjde1IOOKLAo";
                    //$weObj->voice($media_id)->reply();
                    
                    //获取二维码信息
                    $erweima = db("qrcode_direction")->where(['id' => $id])->find();
                    $biao	            = $erweima['type'];
                    $id		            = $erweima['data_id'];
                    $neeq	            = $erweima['data_neeq'];
                    $plannoticeddate    = $erweima['plannoticeddate'];
                    //$length = explode('&', $id);
                    
                    //没有用户注册
                    if(empty($info)){
                        $url    = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='. getAccessTokenFromFile().'&openid='.$openId.'&lang=zh_CN';
                        $result = httpGet($url);
                        $result = json_decode($result, true);
                        $result == isset($result['errcode']) ? array() : $result;
                        
                        $data = array();
                        $data['userType']		= 0;
                        $data['superior']		= 0;
                        $data['userName']		= htmlspecialchars($result['nickname']);
                        $data['userSex']		= $result['sex']==0 ? 3 : $result['sex']==1 ? 1 : 2;
                        $data['userPhone']		= '';
                        $data['createTime']		= time();
                        $data['CREATE_TIME']	= date('Y-m-d H:i:s' , time());
                        $data['userPhoto']		= $result['headimgurl'];
                        $data['lastTime']		= time();
                        $data['lastIP']			= get_client_ip();
                        $data['openId']			= $result['openid'];
                        $data['status']			= 1;
                        $result = $Member->allowField(true)->save($data);
                    }
                    
                    $info = $Member->where(['openId' => $openId])->find();
                    $CustomerService = model("SelfCustomerService");
                    if($info['customerService'] == 0){
                        $service = $CustomerService->getRandomCustomerService($info['uid']);
                    } else {
                        $service = $CustomerService->getCustomerServiceById($info['customerService']);
                    } 
                    
                    if($biao == 'project'){//项目
                        $model   = model("project");
                        $info    = $model->getProjectSimpleInfoById($id);
                    }elseif ($biao == 'zfmxes') {
                        $model   = model("Zfmxes");
                        $info    = $model->getZfmxesSimpleInfo($neeq , $plannoticeddate);
                    }elseif ($biao == 'organize'){//机构
                        $model   = model("organize");
                        //$orgInfo = $model->getOrganizeSimpleInfoById($id);
                        $info    = $model->getOrganizeSimpleInfoById($id);
                        //根据二维码附带的参数获取机构客服id
                        //insert/update 用户扫机构码的信息到member_organize表;
                        /*$memberInfo         = $Member->where(['openId' => $openId])->find();
                        $memberOrganize     = model("MemberOrganize");
                        $memberOrganizeInfo = $memberOrganize->getMemberOrganizeInfoByMid($memberInfo['uid']);
                        if(!is_null($memberOrganizeInfo['mid'])){
                            $data = [
                                'orgId'=>$orgInfo['org_id'],
                                'org_customer_service'=>$orgInfo['scs_id'],
                                'startTime'=>time()
                            ];
                            
                            $memberOrganize->allowField(true)->save($data,['mid'=>$memberInfo['uid']]);
                            
                        }else{
                            $data = [
                                'mid'                   =>$memberInfo['uid'],
                                'orgId'                 =>$orgInfo['org_id'],
                                'org_customer_service'  =>$orgInfo['scs_id'],
                                'startTime'=>time()
                            ];
                            
                            $memberOrganize->allowField(true)->save($data);
                        }
                        
                        $service['kf_account'] = $orgInfo['kf_account'];
                        $service['kf_nick']    = $orgInfo['kf_nick'];
                        */
                        
                        //$info = $model->getOrganizeSimpleInfoById($id);
                        //$service['kf_account'] = $info['kf_account'];
                        //$service['kf_nick'] = $info['kf_nick'];
                        
                    }elseif ($biao == 'Member'){//会员ID
                        $model = model("Member");
                        $info  = $model->getMemberInfoById($id);
                        
                    }else{
                        $model = model("ZryxesEffect");
                        $info  = $model->getZryxesEffectSimpleInfo($id);
                    }
                    
                    $title = '您的专属客服是【'.$service['kf_account'].'】,您可在公众号直接与您的客服聊天!';
                    $urll  = 'http://'.$_SERVER['SERVER_NAME'].'/static/common/LOGO.png';
                    $model = model("SystemConfig");
                    $set   = $model->getSystemConfigInfo();
                    
                    $send  = [];
                    $send['pro_name'] = '金融合伙人';
                    $send['top_img']  = 'system/img/logo.jpg';
                    if(!empty($set) && $set['title'] != ''){
                        $send['pro_name'] = $set['title'];
                    }
                    if(!empty($set) && $set['logo_img'] != ''){
                        $send['top_img']  = $set['logo_img_url'];
                    }
                    
                    $send['Url'] = $_SERVER['SERVER_NAME'].'/home/index/index';
                    $send['des'] = '';
                    
                    if(!empty($info)){
                        if($biao == 'project'){
                            $send['pro_name']   = $info['pro_name'];
                            $send['top_img']    = $_SERVER['SERVER_NAME']."/static/home/images/wechat_pro.jpg";
                            $send['Url']        = $_SERVER['SERVER_NAME'].'/home/project_info/index/id/'.$id;
							$send['des']        = $info['introduction'];
                        }elseif ($biao == 'zfmxes') {
                            $send['pro_name']   = $info['sec_uri_tyshortname'];
                            $send['Url']        = $_SERVER['SERVER_NAME'].'/home/zfmxes_info/index/neeq/'.$neeq.'/plannoticeddate/'.$plannoticeddate;
                        }elseif ($biao == 'organize') {
                            $send['pro_name']   = $info['org_name'];
                            $send['top_img']    = $_SERVER['SERVER_NAME']."/static/home/images/wechat_pro.jpg";
                            $send['Url']        = $_SERVER['SERVER_NAME'].'/static/frontend/detailsShowSelectedFunds.html?id='.$id.'&type=organize&show=0';
                            $send['des']        = '您有新的机构信息等待着您确认，请点击进入';
                            
                            //$send['pro_name'] = $orgInfo['org_name'];
                            //$send['top_img']  = $orgInfo['top_img'];
                            //if($orgInfo['status'] == 1 && $orgInfo['flag'] == 2){
                             //   $send['Url'] = $_SERVER['SERVER_NAME'].'/home/organize_info/index/id/'.$id;
                            //}else{
                            //$send['Url'] = $_SERVER['SERVER_NAME'].'/static/frontend/detailsShowSelectedFunds.html?id='.$id.'&type=organize&show=0';
                                
                            //}
                            
                        }elseif ($biao == 'Member') {
                            $send['pro_name']   = $info['realName'];
                            $send['top_img']    = $info['userPhoto'];
                            $send['Url']        = $_SERVER['SERVER_NAME'].'/static/frontend/othersBusiness.html?uid='.$id.'&type=0';
                        }else{
                            $send['pro_name']   = $info['sec_uri_tyshortname'];
                            $send['Url']        = $_SERVER['SERVER_NAME'].'/home/zryxes_effect_info/index/id/'.$id;
                        }
                    }
                    if(!empty($set) && $set['sign'] != ''){
                        $title = str_replace(['$kf_account','$kf_nick'], [$service['kf_account'],$service['kf_nick']], $set['sign']);
                    }
                    if(!empty($set) && $set['kf_img'] != ''){
                        $urll  = 'http://'.$_SERVER['SERVER_NAME'].'/uploads/'.$set['kf_img_url'];
                    }
                    $data = [
                        [
                            "Title"			=> $send['pro_name'],
                            "Description"	=> $send['des'],
                            "Url"			=> $send['Url'],
                            "PicUrl"		=> 'http://'.$_SERVER['SERVER_NAME']."/static/home/images/wechat_pro.jpg"
                        ],
                        /*[
                             "Title"	    => $title,
                             "Description"	=> '',
                             "Url"			=> $send['Url'],
                             "PicUrl"		=> $urll
                         ],
                         [
                             'media_id'		=> $media_id,
                             "Title"		=> '',
                             "Description"	=> '',
                             "Url"			=> '',
                             "PicUrl"		=> ''
                         ]*/
                    ];
                    $weObj->news($data)->reply();
                }
                break;
            case Wechat::MSGTYPE_VOICE:
                $result = $weObj->getRevVoice();
                $Member = model("Member");
                $info   = $Member->where(['openId' => $openId])->find();
                $CustomerService = model("SelfCustomerService");
                if($info['customerService'] == 0){
                    $service = $CustomerService->getRandomCustomerService($info['uid']);
                } else {
                    $service = $CustomerService->getCustomerServiceById($info['customerService']);
                }
                $kf_message         = model("KfMessage");
                $kf_message_last    = model("KfMessageLast");
                
                $last = $kf_message_last->where(['openId' => $openId])->find();
                if(empty($last)){
                    $arr = [];
                    $arr['m_uid']       = $info['uid'];
                    $arr['openId']      = $openId;
                    $arr['direction']   = '-->';
                    $arr['scs_id']      = $service['scs_id'];
                    $arr['is_read']     = 1;
                    $arr['last_time']   = time();
                    $kf_message_last->allowField(true)->save($arr);
                }else {
                    if($last['first_time'] == 0){
                        $arr = [];
                        $arr['direction']   = '-->';
                        $arr['is_read']     = $last['is_read']<99 ? $last['is_read']+1 : 99;
                        $arr['last_time']   = time();
                        $arr['first_time']  = time();
                        $kf_message_last->where(['openId' => $openId])->update($arr);
                    } else {
                        $arr = [];
                        $arr['direction']   = '-->';
                        $arr['is_read']     = $last['is_read']<99 ? $last['is_read']+1 : 99;
                        $arr['last_time']   = time();
                        $kf_message_last->where(['openId' => $openId])->update($arr);
                    }
                }
                //                $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.getAccessTokenFromFile().'&media_id='.$result['mediaid'];
                //                $content = httpGet($url);
                $content    = $weObj->getMedia($result['mediaid']);
                $filename   = genRandomString(8).rand(1000, 9999);
                $dir        = 'uploads' . DS . 'home/voice/'. date('Ymd' , time());
                if(!is_dir(ROOT_PATH . 'public' . DS . $dir)){
                    mkdir($dir , 0777, true);
                }
                $file = fopen(ROOT_PATH . 'public' . DS . $dir.'/'.$filename.'.amr', 'w');
                fwrite($file, $content);
                fclose($file);
                exec('ffmpeg -i '.ROOT_PATH.'public/'.$dir.'/'.$filename.'.amr -ab 96 -ac 2 '.ROOT_PATH.'public/'.$dir.'/'.$filename.'.mp3');
                $data = [];
                $data['m_uid']          = $info['uid'];
                $data['openId']         = $openId;
                $data['scs_id']         = $service['scs_id'];
                $data['type']           = 'voice';
                $data['content']        = $dir.'/'.$filename.'.mp3';
                $data['create_time']    = time();
                $kf_message->allowField(true)->save($data);
                sleep(3);
                $last = $kf_message_last->where(['openId' => $openId])->find();
                if($last['is_read']){
                    $data = [
                                'touser'        => $service['kf_openId'],
                                'template_id'   => config('chat_content'),
                                'url'           => $_SERVER['SERVER_NAME'].'/'. url("InstantMessaging/index").'/uid/'.$info['uid'],
                                'topcolor'      => '#FF0000',
                                'data'          => [
                                                    'first'     => ['value' => '你好，你有'.$last['is_read'].'条用户咨询待解决' , 'color' => '#173177'],
                                                    'keyword1'  => ['value' => htmlspecialchars($info['userName'] , ENT_QUOTES) , 'color' => '#E51717'],
                                                    'keyword2'  => ['value' => '【一条语音】' , 'color' => '#E51717'],
                                                    'remark'    => ['value' => '点击处理咨询' , 'color' => '#3B55EA'],
                                                    ]
                            ];
                    $result = $weObj->sendTemplateMessage($data);
                    if(!$result){
                        $weObj->text('错误原因1：'.$weObj->errCode."-".$weObj->errMsg)->reply();
                    }
                }
                break;
            case Wechat::MSGTYPE_IMAGE:
                $result = $weObj->getRevPic();
                $Member = model("Member");
                $info   = $Member->where(['openId' => $openId])->find();
                
                $CustomerService = model("SelfCustomerService");
                if($info['customerService'] == 0){
                    $service = $CustomerService->getRandomCustomerService($info['uid']);
                } else {
                    $service = $CustomerService->getCustomerServiceById($info['customerService']);
                }
                $kf_message         = model("KfMessage");
                $kf_message_last    = model("KfMessageLast");
                
                $last = $kf_message_last->where(['openId' => $openId])->find();
                if(empty($last)){
                    $arr = [];
                    $arr['m_uid']       = $info['uid'];
                    $arr['openId']      = $openId;
                    $arr['direction']   = '-->';
                    $arr['scs_id']      = $service['scs_id'];
                    $arr['is_read']     = 1;
                    $arr['last_time']   = time();
                    $kf_message_last->allowField(true)->save($arr);
                }else {
                    if($last['first_time'] == 0){
                        $arr = [];
                        $arr['direction']   = '-->';
                        $arr['is_read']     = $last['is_read']<99 ? $last['is_read']+1 : 99;
                        $arr['last_time']   = time();
                        $arr['first_time']  = time();
                        $kf_message_last->where(['openId' => $openId])->update($arr);
                    } else {
                        $arr = [];
                        $arr['direction']   = '-->';
                        $arr['is_read']     = $last['is_read']<99 ? $last['is_read']+1 : 99;
                        $arr['last_time']   = time();
                        $kf_message_last->where(['openId' => $openId])->update($arr);
                    }
                }
                $content  = $weObj->getMedia($result['mediaid']);
                $filename = genRandomString(8).rand(1000, 9999);
                $dir = 'uploads' . DS . 'home/img/'. date('Ymd' , time());
                if(!is_dir(ROOT_PATH . 'public' . DS . $dir)){
                    mkdir($dir , 0777, true);
                }
                $file = fopen(ROOT_PATH . 'public' . DS . $dir.'/'.$filename.'.jpg', 'w');
                fwrite($file, $content);
                fclose($file);
                $data = [];
                $data['m_uid']       = $info['uid'];
                $data['openId']      = $openId;
                $data['scs_id']      = $service['scs_id'];
                $data['type']        = 'image';
                $data['content']     = '/'.$dir.'/'.$filename.'.jpg';
                $data['create_time'] = time();
                $kf_message->allowField(true)->save($data);
                sleep(3);
                $last = $kf_message_last->where(['openId' => $openId])->find();
                if($last['is_read']){
                    $data = [
                                'touser'        => $service['kf_openId'],
                                'template_id'   => config('chat_content'),
                                'url'           => $_SERVER['SERVER_NAME'].'/'. url("InstantMessaging/index").'/uid/'.$info['uid'],
                                'topcolor'      => '#FF0000',
                                'data'          => [
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
                }
                break;
            default:
                $weObj->text("如有疑问,请点击下方列表按钮查看")->reply();
        }
    }
    
    /***
     @param:判断用户是否被拉黑
     num   string    用户openid
     @time:2018/5/16
     @author:allen
     **/
    public function pullback($num){
        if(!empty($num)){
            $member = model("Member");
            if($member){
                $open['openId'] = $num;
                $result = $member -> where($open) -> find();
                if($result){
                    if($result['pullback'] == 1){
                        //$data['msg'] = "您发送的消息无法送达!";
                        return false;
                    }else{
                        return true;
                    }
                }else{
                    return true;
                }
            }
        }
    }
    
    
    public function getText($text){
        $length     = strlen($text);
        if($length > 25){
            $text = substr($text, 0, 25).'...';
        }
        $search     = array(" ","　","\n","\r","\t");
        $replace    = array("","","","","");
        $text       = str_replace($search, $replace, $text);
        $text       = htmlspecialchars($text , ENT_QUOTES);
        return $text;
    }
}
