<?php

namespace app\home\model;

use think\Model;
use sys\Wechat;

class KfMessage extends Model
{
    public function getDefaultAllInfoByUid($uid , $num = 0 , $flag = true){
        if($flag){
            if($num < config("ins_num")){
                $num = config("ins_num");
            } else {
                $num = $num;
            }
        } else {
            $num = $num;
        }
        $result = $this->alias("km")
                ->join("member m" , "m.uid=km.m_uid")
                ->join("self_customer_service scs" , "scs.scs_id=km.scs_id")
                ->join("material_library ml" , "scs.kf_headimgurl=ml.ml_id")
                ->field("km.*,m.userPhoto,m.userName,scs.real_name,ml.url_thumb")
                ->where(['m_uid' => $uid])
                ->order("create_time desc")
                ->limit($num)
                ->select();
        if(!empty($result)){
            foreach ($result as $key => $value) {
                $time = explode(' ', $value['create_time']);
                $result[$key]['day'] = $time[0];
                $result[$key]['time'] = substr($time[1], 0, 5);
            }
        }
        return $result;
    }
//  $min 当前页面最小的信息的id
//  $max 当前页面最大的信息的id
//  $num 需要搜索的数据条数,0没限制
    public function getAllInfoByUid($uid , $min = 0 , $max = 0 , $num = 0){
        $service = session('SelfCustomerService');
        if($min && $max){
            $this->error = '暂无次需求';
            return false;
        }elseif ($min) {
            $result = $this->alias("km")
                ->join("member m" , "m.uid=km.m_uid")
                ->join("self_customer_service scs" , "scs.scs_id=km.scs_id")
                ->join("material_library ml" , "scs.kf_headimgurl=ml.ml_id")
                ->field("km.*,m.userPhoto,m.userName,scs.real_name,ml.url_thumb")
                ->where('km.m_uid='.$uid.' and km.km_id < '.$min)
                ->order("create_time desc")
                ->limit($num)
                ->select();
        }elseif ($max) {
            $result = $this->alias("km")
                ->join("member m" , "m.uid=km.m_uid")
                ->join("self_customer_service scs" , "scs.scs_id=km.scs_id")
                ->join("material_library ml" , "scs.kf_headimgurl=ml.ml_id")
                ->field("km.*,m.userPhoto,m.userName,scs.real_name,ml.url_thumb")
                ->where('km.m_uid='.$uid.' and km.km_id > '.$max.' and ((km.direction = "-->" and km.scs_id = '.$service['scs_id'].') or (km.direction = "<--" and km.scs_id <> '.$service['scs_id'].'))')
                ->order("create_time desc")
                ->select();
        }
        if(!empty($result)){
            foreach ($result as $key => $value) {
                $time = explode(' ', $value['create_time']);
                $result[$key]['day'] = $time[0];
                $result[$key]['time'] = substr($time[1], 0, 5);
            }
        }
        return $result;
    }
    
    public function getMessageOnce($id){
        $result = $this->alias("km")
            ->join("member m" , "m.uid=km.m_uid")
            ->join("self_customer_service scs" , "scs.scs_id=km.scs_id")
            ->join("material_library ml" , "scs.kf_headimgurl=ml.ml_id")
            ->field("km.*,m.userPhoto,m.userName,scs.real_name,ml.url_thumb")
            ->where(['km.km_id' => $id])
            ->order("create_time desc")
            ->find();
        $time = explode(' ', $result['create_time']);
        $result['day'] = $time[0];
        $result['time'] = substr($time[1], 0, 5);
        return $result;
    }

    public function addKfMessage($data , $type = 'text'){
        $service = session('SelfCustomerService');
        if(empty($data)){
            $this->error = '客服内容为空';
            return false;
        }
        
        $model      = model("Member");
        $info       = $model->getMemberInfoById($data['uid']);
        $isFollow   = isFollow($info['openId'],getAccessTokenFromFile());
        if($isFollow){
            $weObj  = new Wechat(config("WeiXinCg"));
            $weObj->checkAuth('', '', getAccessTokenFromFile());
            if($type == 'text'){
                $ww = [
                    'touser'    => $info['openId'],
                    'msgtype'   => 'text',
                    'text'      => [
                        'content'   => $data['content']
                    ]
                ];
                
                $w_result = $weObj->sendCustomMessage($ww);
            }else{
                $url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token='. getAccessTokenFromFile().'&type=image';
                $post = ['media' => new \CURLFile(realpath($data['content']))];
                $result = httpPost($url, $post);
                $result = (array)json_decode($result);
                if(isset($result['errcode'])){
                    $this->error = $result['errcode'].":".$result['errmsg'];
                    $w_result = false;
                } else {
                    $ww = [
                        'touser' => $info['openId'],
                        'msgtype' => 'image',
                        'image' => [
                            'media_id' => $result['media_id']
                        ]
                    ];
                    $w_result = $weObj->sendCustomMessage($ww);
                    if(!$w_result){
                        $this->error = $weObj->errCode.":".$weObj->errMsg;
                    }
                }
            }
            if($w_result){
                $arr = [];
                $arr['m_uid'] = $data['uid'];
                $arr['openId'] = $info['openId'];
                $arr['direction'] = '<--';
                $arr['scs_id'] = $service['scs_id'];
                if($type == 'image'){
                    $arr['type'] = 'image';
                    $arr['content'] = DS . 'uploads' . DS . 'home' . DS . 'kf' . strrchr($data['content'], '/');
                } else {
                    $arr['content'] = $data['content'];
                }
                $arr['create_time'] = time();
                $result = $this->allowField(true)->save($arr);
            }
            $id = $this->getLastInsID();
            if($result && $w_result){
                $info = $this->getMessageOnce($id);
                return $info;
            } else {
                return false;
            }
            
        }else{
            $this->error = '该用户已经取消关注公众号';
            return false;
        }
        
        
    }
}