<?php

namespace app\console\model;

use think\Model;
use sys\Wechat;

class KfMessage extends Model
{
    public function getMessageList($uid , $start = 0 , $length){
        $result = $this->alias("km")
                ->join("self_customer_service scs" , "scs.scs_id=km.scs_id")
                ->join("material_library ml" , "scs.kf_headimgurl=ml.ml_id")
                ->field("km.*,scs.real_name,ml.url_thumb")
                ->where(['m_uid' => $uid])
                ->order("create_time desc")
                ->limit($start , $length)
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
    public function getAllInfoByUid($uid , $min = 0 , $max = 0 , $num = 0){
        $info = \app\console\service\User::getInstance()->getInfo();
        $model = model("SelfCustomerService");
        $info = $model->getSelfCustomerServiceInfoByUid($info['uid']);
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
                ->where('km.m_uid='.$uid.' and km.km_id > '.$max.' and ((km.direction = "-->" and km.scs_id = '.$info['scs_id'].') or (km.direction = "<--" and km.scs_id <> '.$info['scs_id'].'))')
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
        $info = \app\console\service\User::getInstance()->getInfo();
        $model = model("SelfCustomerService");
        $service = $model->getSelfCustomerServiceInfoByUid($info['uid']);
        if(empty($data)){
            $this->error = '客服内容为空';
            return false;
        }
        $model = model("Member");
        $info = $model->getMemberInfoById($data['uid']);
        $weObj = new Wechat(config("WeiXinCg"));
        $weObj->checkAuth('', '', getAccessTokenFromFile());
        if($type == 'text'){
            $ww = [
                'touser' => $info['openId'],
                'msgtype' => 'text',
                'text' => [
                    'content' => $data['content']
                ]
            ];
            $w_result = $weObj->sendCustomMessage($ww);
        }else{
            $url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token='. getAccessTokenFromFile().'&type=image';
            $post = ['media' => new \CURLFile(realpath(ROOT_PATH . 'public' . DS . 'uploads' . DS . $data['url']))];
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
                $arr['content'] = DS . 'uploads' . DS . $data['url'];
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
    }
}