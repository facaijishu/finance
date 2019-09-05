<?php
namespace app\home\controller;
use think\Image;

class InstantMessaging extends Base
{
    public function __construct() {
        parent::__construct();
        $this->judgeCustomerService();
    }
    //判断是否为客服
    public function judgeCustomerService(){
        $user = session('FINANCE_USER');
        $model = model("SelfCustomerService");
        $info = $model->getCustomerServiceByOpenid($user['openId']);
        if(empty($info) || $info['kf_openId'] == '' || $info['status'] != 1){
            //当前登录用户不是客服
            session('SelfCustomerService' , null);
        } else {
            session('SelfCustomerService' , $info);
        }
    }

    //未读清理
    public function index(){
        if(session('SelfCustomerService') == null){
            $this->assign('title' , '客服中心 -');
            $this->assign('img' , '');
            $this->assign('des' , '有事请询问相关客服哦!');
            return view("fail");
        } else {
            $uid = $this->request->param('uid/d');
            $model = model("KfMessageLast");
            $info = $model->getNewInfoByUid($uid);
            $model = model("KfMessage");
            $list = $model->getDefaultAllInfoByUid($uid , $info['is_read'] , true);
            $this->assign('list' , array_reverse($list));
            $this->assign('max' , $list[0]['km_id']);
            $this->assign('min' , $list[count($list)-1]['km_id']);
//            $this->assign('sum' , count($list));
            $this->assign('uid' , $uid);
            $this->assign('title' , '客服中心 -');
            $this->assign('img' , '');
            $this->assign('des' , '有事请询问相关客服哦!');
            return view();
        }
    }
    public function add(){
        $data    = input();
        $result1 = model("KfMessageLast")->where(['m_uid' => $data['uid']])->update(['direction' => '<--' , 'last_time' => time() , 'first_time' => 0]);
        $model   = model("KfMessage");
        if($result1 && $result2 = $model->addKfMessage($data , 'text')){
            $this->result($result2, 1, '保存成功', 'json');
        } else {
            $error = $model->getError() ? $model->getError() : '保存失败';
            $this->result('', 0, $error, 'json');
        }
    }
    public function selectNewInfo(){
        $data = input();
        $model = model("KfMessageLast");
        $info = $model->getNewInfoByUid($data['uid']);
        $model = model("KfMessage");
        $list = $model->getAllInfoByUid($data['uid'] , 0 , $data['max']);
        if($list){
            $this->result($list, 1, '搜索成功', 'json');
        } else {
            $this->result('', 0, '搜素失败', 'json');
        }
    }
    public function selectOldInfo(){
        $data = input();
        $model = model("KfMessage");
        if($list = $model->getAllInfoByUid($data['uid'] , $data['min'] , 0 , config("ins_num"))){
            $list = array_reverse($list);
            $this->result($list, 1, '搜索成功', 'json');
        } else {
            if(empty($list)){
                $error = '已无数据';
            } else {
                $error = '搜素失败';
            }
            $this->result('', 0, $error, 'json');
        }
    }
    public function upload(){
        $file = $_FILES;
        $data = input();
        if($file){
            $name = explode('.', $file['file']['name']);
            $type = $name[count($name)-1];
            $filename = date('ymd').rand(1000, 9999).'.'.$type;
            if(in_array($type, ['jpg' , 'jpeg' , 'gif' , 'png','JPG' , 'JPEG' , 'GIF' , 'PNG'])){
                $path = ROOT_PATH . 'public' . DS . 'uploads' . DS . 'home' . DS . 'kf' . DS;
                if(!is_dir($path)){
                    mkdir($path , 0777 , true);
                }
                $result = move_uploaded_file($file['file']['tmp_name'], $path.$filename);
                if($result){
                    if($file['file']['size'] > 2097152){
                        $image = Image::open($path.$filename);
                        $aa = $image->thumb(1500, 1500)->save($path.'th-'.$filename , $type , 80);
                        $data['content'] = $path.'th-'.$filename;
                    } else {
                        $data['content'] = $path.$filename;
                    }
                    $result1 = model("KfMessageLast")->where(['m_uid' => $data['uid']])->update(['direction' => '<--' , 'last_time' => time() , 'first_time' => 0]);
                    $model = model("KfMessage");
                    if($result1 && $result2 = $model->addKfMessage($data , 'image')){
                        $this->result($result2, 1, '上传并保存成功', 'json');
                    } else {
                        $error = $model->getError() ? $model->getError() : '上传但保存失败';
                        $this->result('', 0, $error, 'json');
                    }
                }else{
                    $this->result('', 0, '上传失败', 'json');
                }
            } else {
                $this->result('' ,0 , '上传文件格式不正确'.$type , 'json');
            }
        } else {
            $this->result('' ,0 , '图片不存在' , 'json');
        }
    }
}