<?php
namespace app\home\controller;
use think\Request;
/**
 *
 */
class Chat extends Base
{
    
    //点对点聊天页面展示
    public function index()
    {
        //发送方uid;
        $from_uid = session('FINANCE_USER.uid');
        
        //接收方uid;
        $to_uid   = input('to_uid')?input('to_uid'):0;
        
        //获取发送方头像/姓名
        $fromHeadUrl  = session('FINANCE_USER.userPhoto');
        $fromUserName = session('FINANCE_USER.realName');
        
        //获取接收方头像/姓名
        $user = model('Member')->where(['uid'=>$to_uid])->find();
        if ($user) {
            $toHeadUrl = $user['userPhoto'];
            $toUserName = $user['realName'];
        } else {
            $toHeadUrl = '';
            $toUserName = '';
        }
        $this->assign('from_uid',$from_uid);
        $this->assign('to_uid',$to_uid);
        $this->assign('fromheadurl',$fromHeadUrl);
        $this->assign('toheadurl',$toHeadUrl);
        $this->assign('fromusername',$fromUserName);
        $this->assign('tousername',$toUserName);
        $this->assign('title' , '- '.$toUserName);
        $this->assign('img' , '');
        $this->assign('des' , '和TA聊天中');
        
        $page       = input('page')?input('page'):1;
        $num        = 6;
        $offset     = ($page-1) * $num;
        $map        = "( from_uid =". $from_uid." and to_uid = ". $to_uid .") or ( from_uid = ". $to_uid ." and to_uid =". $from_uid." )";
        $list       = model('Chat')->where($map)
                                   ->order('create_time desc')
                                   ->limit($offset,$num)
                                   ->select()
                                   ->toArray();
        if (!empty($list)) {
            foreach ($list as $key => $each_list) {
                $time = explode(' ', $each_list['create_time']);
                $list[$key]['day'] = $time[0];
                $list[$key]['time'] = substr($time[1], 0, 5);
            }
            $this->assign('max' , $list[0]['chat_id']);
            $this->assign('min' , $list[count($list)-1]['chat_id']);
        }else{
            $this->assign('max' , 0);
            $this->assign('min' , 0);
        }
        
        $list = array_reverse($list);
        $this->assign('list' , $list);
        return view();
    }
    
    /**
     * 获取最新聊天记录
     */
    public function selectNewInfo(){
        $data   = input();
        $model  = model("chat");
        $list   = $model->getAllById($data['from_uid'] ,$data['to_uid'], 0 , $data['max'],8);
        if($list){
            $this->result($list, 1, '搜索成功', 'json');
        } else {
            $this->result('', 0, '搜素失败', 'json');
        }
    }
    
    /**
     * 获取当下聊天记录
     */
    public function selectOldInfo(){
        $data   = input();
        $model  = model("chat");
        $list   = $model->getAllById($data['from_uid'] ,$data['to_uid'], $data['min'] , 0,8);
        if($list){
            $this->result($list, 1, '搜索成功', 'json');
        } else {
            $this->result('', 0, '搜素失败', 'json');
        }
    }
    
    //消息发送
    public function add()
    {
        $param = input("post.");
        $data['from_uid']       = $param['from_uid'];
        $data['to_uid']         = $param['to_uid'];
        $data['content']        = $param['content'];
        $data['type']           = $param['type'];
        $data['create_time']    = time();
        $data['is_read']        = 0;
        $model = model("Chat");
        $res   = $model->add($data);
        if($res){
            $this->sendToWxTemplate($data);
            $this->result($res, 1, '上传并保存成功', 'json');
        }else{
            $this->result('', 0, '上传但保存失败', 'json');
        }
    }
    
    
    /**
     * 发送咨询项目链接
     */
    public function sendUrl(){
        $datas    = input();
        $from_uid = session('FINANCE_USER.uid');
        
        $data['from_uid']       = $from_uid;
        $data['to_uid']         = $datas['uid'];
        $data['content']        = "我对项目<a href='".$datas['link']."' style='font-size:16px;color:#ff0000;'>".$datas['send_name']."</a>很感兴趣";
        $data['type']           = 1;
        $data['create_time']    = time();
        $data['is_read']        = 0;
        
        
        $model = model("Chat");
        $res   = $model->add($data);
        $data['content']        = "对项目".$datas['send_name']."很感兴趣";
        if($res){
            $this->sendToWxTemplate($data);
        }
        $this->result('', 1, "发送成功", 'json');
    }
    
    //消息持久化
    public function saveMessage()
    {
        $data = [];
        $msg  = '';
        $code = 200;
        
        $param = input("post.");
        $data['from_uid']       = $param['from_uid'];
        $data['to_uid']         = $param['to_uid'];
        $data['content']        = $param['content'];
        $data['type']           = $param['type'];
        $data['create_time']    = time();
        $data['is_read']        = 0;
        
        try {
            model('Chat')->allowField(true)->save($data);
        } catch (Exception $e) {
            $msg  = $e->getMessage();
            $code = 201;
        }
        echo json_encode(['code'=>$code,'msg'=>$msg]);
    }
    
    //初始化聊天对话内容列表展示
    public function showChat()
    {
        $from_uid   = input('from_uid')?input('from_uid'):0;
        $to_uid     = input('to_uid')?input('to_uid'):0;
        $page       = input('page')?input('page'):1;
        $num        = 4;
        $offset     = ($page-1) * $num;
        $map        = "( from_uid =". $from_uid." and to_uid = ". $to_uid .") or ( from_uid = ". $to_uid ." and to_uid =". $from_uid." )";
        $list       = model('Chat')->where($map)
                                   ->order('chat_id desc')
                                   ->limit($offset,$num)
                                   ->select()
                                   ->toArray();
        if (!empty($list)) {
            //每隔5分钟展示最新的消息发布时间
            foreach ($list as $key => $each_list) {
                if ($key == 0) {
                    $list[$key]['is_show_time'] = friend_date(strtotime($each_list['create_time']));
                } else {
                    $last_msg = strtotime($list[$key-1]['create_time']);
                    $new_msg = strtotime($list[$key]['create_time']);
                    if ($new_msg - $last_msg >= 300) {
                        $list[$key]['is_show_time'] = friend_date(strtotime($each_list['create_time']));
                    } else {
                        $list[$key]['is_show_time'] = '';
                    }
                }
            }
        }
        echo json_encode(['data' => $list]);
    }
    
    //聊天图片上传,返回图片地址
    public function upload()
    {
        
        $file       = $_FILES['file'];
        $from_uid   = input('from_uid')?input('from_uid'):0;
        $to_uid     = input('to_uid')?input('to_uid'):0;
        
        $suffix     = strtolower(strrchr($file['name'],'.'));
        //判断上传图片是否是允许的后缀名
        $allow_suffix = ['.jpeg','.jpg','.png','.gif','.bmp','JPEG' , 'JPG' , 'PNG' , 'GIF'];
        if (!in_array($suffix, $allow_suffix)) {
            $this->result('' ,0 , '仅支持发送图片' , 'json');
        }else{
            if($file["size"]>2097152){
                $this->result('' ,0 , '图片不能超过2M' , 'json');
            }else{
                $file_name = uniqid('chat_img_',false);
                $file_path = ROOT_PATH.'public/uploads/chat/';
                $final_path = $file_path.$file_name.$suffix;
                if (is_uploaded_file($file['tmp_name'])) {
                    $re = move_uploaded_file($file['tmp_name'], $final_path);
                    if ($re) {
                        //数据持久化
                        $data['from_uid']      = $from_uid;
                        $data['to_uid']        = $to_uid;
                        $data['content']       = config('view_replace_str')['__DOMAIN__'].'chat/'.$file_name.$suffix;
                        $data['type']          = 2;
                        $data['create_time']   = time();
                        $data['is_read']       = 0;
                        $model = model("Chat");
                        $res   = $model->add($data);
                        if ($res) {
                            $this->result($res, 1, '上传并保存成功', 'json');
                            
                        } else {
                            $this->result('', 0, '上传保存失败', 'json');
                        }
                        
                    } else {
                        $this->result('', 0, '上传失败', 'json');
                    }
                    
                } else {
                    $this->result('' ,0 , '图片来源不合法' , 'json');
                }
            }
        }
        
    }
    
    //聊天消息列表页
    public function lists()
    {
        $from_uid = session('FINANCE_USER.uid');
        $this->assign('from_uid',$from_uid);
        $this->assign('title' , '');
        $this->assign('img' , '');
        $this->assign('des' , '聊天消息列表');
        
        $userFollow = model('Member')->where(['uid'=>$from_uid])->find();
        $this->assign('follow',$userFollow['is_follow']);
        $this->assign('usertype',$userFollow['userType']);
        return view();
    }
    
    //获取当前登录用户未读聊天消息列表页数据
    public function getList()
    {
        $from_uid = input('from_uid')?input('from_uid'):0;
        //查询哪些用户发送过消息给当前登录用户
        $list     = model('Chat')->getReplyMsg($from_uid);
        echo json_encode($list);
    }
    
    //更新未读消息为已读
    public function changeNoread()
    {
        $data = [];
        $from_uid   = input('from_uid')?input('from_uid'):0;
        $to_uid     = input('to_uid')?input('to_uid'):0;
        try {
            model('Chat')->where(['from_uid'=>$to_uid,'to_uid'=>$from_uid])->update(['is_read'=>1]);
            $code = 200;
            $msg  = 'success';
        } catch (Exception $e) {
            $code = 201;
            $msg  = $e->getMessage();
        }
        $data['code']   = $code;
        $data['msg']    = $msg;
        echo json_encode($data);
    }
    
    //更新登录用户接收的所有未读消息总和
    public function getAllNoreadApi()
    {
        $from_uid = session('FINANCE_USER.uid');
        $all_noread = model('Chat')->getAllNoread($from_uid);
        echo json_encode(['msg_num' => $all_noread]);
    }
    
    
    //发送到微信服务器,通知微信用户
    public function sendToWxTemplate($data)
    {
        
        //template_id
        $template_id    = config('chat_content');
        
        $from_uid       = $data['from_uid'];
        $realName       = model('Member')->getRealName($from_uid);
        
        //to_user
        $to_uid         = $data['to_uid'];
        $openid         = model('Member')->getOpenid($to_uid);
        
        //url
        $url            = $_SERVER['SERVER_NAME'].'/home/chat/lists';
        
        $content        = $data['content'];
        $array = [
            'first'     => ['value' => '您有一条新的消息' , 'color' => '#173177'],
            'keyword1'  => ['value' => $realName , 'color' => '#0D0D0D'],
            'keyword2'  => ['value' => $content , 'color' => '#0D0D0D'],
            'remark'    => ['value' => '请点击详情查看!' , 'color' => '#173177'],
        ];
        $re = sendTemplateMessage($template_id,$openid,$url,$array);
    }
}
