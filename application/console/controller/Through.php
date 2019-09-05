<?php
namespace app\console\controller;
use sys\Wechat;

class Through extends ConsoleBase{
    public function index(){
        return view();
    }
    public function audited(){
        return view();
    }
    public function repulse(){
        return view();
    }
    public function termination(){
        return view();
    }
    public function indexRead(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));

        $realName = $this->request->get('realName', '');
        $phone = $this->request->get('phone', '');

        $Through = model('Through')
                ->alias("T")
                ->join("Member M" , "T.uid=M.uid")
                ->field("SQL_CALC_FOUND_ROWS T.*,M.superior");
        //搜索条件
        if('' !== $realName) {
            $Through->where('T.realName','like','%'.$realName.'%');
        }
        if('' !== $phone) {
            $Through->where('T.phone','like','%'.$phone.'%');
        }
        $Through->where('T.status','eq',0);
        $Through->where('T.flag','eq',1);
        
        $admin = \app\console\service\User::getInstance()->isAdmin();
        if(!$admin){
            $admin = \app\console\service\User::getInstance()->getInfo();
            $customer = model("SelfCustomerService")->getSelfCustomerServiceInfoByUid($admin['uid']);
            if(!empty($customer) && $customer['status'] == 1){
                $member = model("Member")->where(['openId' => $customer['kf_openId']])->find();
                $Through->where('M.superior','eq',$member['uid']);
            }
        }
        
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $Through->order($_order);
        }
//        $Through->field('SQL_CALC_FOUND_ROWS *');
        $list = $Through->limit($start, $length)->select();
        $result = $Through->query('SELECT FOUND_ROWS() as count');
        $total = $result[0]['count'];
        $data = [];
        foreach ($list as $key => $item) {
            if($item['role_type'] == 1){
               $list[$key]['role_type'] = '投资人'; 
            }elseif($item['role_type'] == 2){
               $list[$key]['role_type'] = '项目方';
            }else{
               $list[$key]['role_type'] = '未选择'; 
            } 
            $list[$key]['createTime'] = date('Y-m-d H:i:s' , $item['createTime']);
            $list[$key]['updateTime'] = date('Y-m-d H:i:s' , $item['updateTime']);
            if(stripos($item['card'],',')){
                $list[$key]['card'] = explode(',',$item['card']);
            }
            $data[] = $item->toArray();
        }
        $return['data'] = $data;
        $return['recordsFiltered'] = $total;
        $return['recordsTotal'] = $total;

        echo json_encode($return);
    }
    public function auditedRead(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));

        $realName = $this->request->get('realName', '');
        $phone = $this->request->get('phone', '');

        $Through = model('Through')
                ->alias("T")
                ->join("Member M" , "T.uid=M.uid")
                ->field("SQL_CALC_FOUND_ROWS T.*,M.superior");
        //搜索条件
        if('' !== $realName) {
            $Through->where("T.realName like '%".$realName."%'");
        }
        if('' !== $phone) {
            $Through->where('T.phone like "%'.$phone.'%"');
        }
        $Through->where('T.status','eq',1);
        $Through->where('T.flag','eq',1);
        
        
        $admin = \app\console\service\User::getInstance()->isAdmin();
        if(!$admin){
            $admin = \app\console\service\User::getInstance()->getInfo();
            $customer = model("SelfCustomerService")->getSelfCustomerServiceInfoByUid($admin['uid']);
            if(!empty($customer) && $customer['status'] == 1){
                $member = model("Member")->where(['openId' => $customer['kf_openId']])->find();
                $Through->where('M.superior','eq',$member['uid']);
            }
        }
        
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $Through->order($_order);
        }
//        $Through->field('SQL_CALC_FOUND_ROWS *');
        $list = $Through->limit($start, $length)->select();
        $result = $Through->query('SELECT FOUND_ROWS() as count');
        $total = $result[0]['count'];
        $data = [];
        foreach ($list as $key => $item) {
            if($item['role_type'] == 1){
               $list[$key]['role_type'] = '投资人';
            }elseif($item['role_type'] == 2){
               $list[$key]['role_type'] = '项目方';
            }else{
               $list[$key]['role_type'] = '未选择';
            }
            $list[$key]['createTime'] = date('Y-m-d H:i:s' , $item['createTime']);
            $list[$key]['updateTime'] = date('Y-m-d H:i:s' , $item['updateTime']);
            if(stripos($item['card'],',')){
                $list[$key]['card'] = explode(',',$item['card']);
            }
            $data[] = $item->toArray();
        }
        $return['data'] = $data;
        $return['recordsFiltered'] = $total;
        $return['recordsTotal'] = $total;

        echo json_encode($return);
    }
    public function repulseRead(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));

        $realName = $this->request->get('realName', '');
        $phone = $this->request->get('phone', '');

        $Through = model('Through')
                ->alias("T")
                ->join("Member M" , "T.uid=M.uid")
                ->field("SQL_CALC_FOUND_ROWS T.*,M.superior");
        //搜索条件
        if('' !== $realName) {
            $Through->where('T.realName','like','%'.$realName.'%');
        }
        if('' !== $phone) {
            $Through->where('T.phone','like','%'.$phone.'%');
        }
        $Through->where('T.status','eq',-1);
        $Through->where('T.flag','eq',1);
        
        $admin = \app\console\service\User::getInstance()->isAdmin();
        if(!$admin){
            $admin = \app\console\service\User::getInstance()->getInfo();
            $customer = model("SelfCustomerService")->getSelfCustomerServiceInfoByUid($admin['uid']);
            if(!empty($customer) && $customer['status'] == 1){
                $member = model("Member")->where(['openId' => $customer['kf_openId']])->find();
                $Through->where('M.superior','eq',$member['uid']);
            }
        }
        
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $Through->order($_order);
        }
//        $Through->field('SQL_CALC_FOUND_ROWS *');
        $list = $Through->limit($start, $length)->select();
        $result = $Through->query('SELECT FOUND_ROWS() as count');
        $total = $result[0]['count'];
        $data = [];
        foreach ($list as $key => $item) {
            if($item['role_type'] == 1){
               $list[$key]['role_type'] = '投资人';
            }elseif($item['role_type'] == 2){
               $list[$key]['role_type'] = '项目方';
            }else{
               $list[$key]['role_type'] = '未选择';
            }
            $list[$key]['createTime'] = date('Y-m-d H:i:s' , $item['createTime']);
            $list[$key]['updateTime'] = date('Y-m-d H:i:s' , $item['updateTime']);
            if(stripos($item['card'],',')){
                $list[$key]['card'] = explode(',',$item['card']);
            }
            $data[] = $item->toArray();
        }
        $return['data'] = $data;
        $return['recordsFiltered'] = $total;
        $return['recordsTotal'] = $total;

        echo json_encode($return);
    }
    public function terminationRead(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));

        $realName = $this->request->get('realName', '');
        $phone = $this->request->get('phone', '');

        $Through = model('Through')
                ->alias("T")
                ->join("Member M" , "T.uid=M.uid")
                ->field("SQL_CALC_FOUND_ROWS T.*,M.superior");
        //搜索条件
        if('' !== $realName) {
            $Through->where('T.realName','like','%'.$realName.'%');
        }
        if('' !== $phone) {
            $Through->where('T.phone','like','%'.$phone.'%');
        }
        $Through->where('T.flag','eq',-1);
        
        $admin = \app\console\service\User::getInstance()->isAdmin();
        if(!$admin){
            $admin = \app\console\service\User::getInstance()->getInfo();
            $customer = model("SelfCustomerService")->getSelfCustomerServiceInfoByUid($admin['uid']);
            if(!empty($customer) && $customer['status'] == 1){
                $member = model("Member")->where(['openId' => $customer['kf_openId']])->find();
                $Through->where('M.superior','eq',$member['uid']);
            }
        }
        
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $Through->order($_order);
        }
//        $Through->field('SQL_CALC_FOUND_ROWS *');
        $list = $Through->limit($start, $length)->select();
        $result = $Through->query('SELECT FOUND_ROWS() as count');
        $total = $result[0]['count'];
        $data = [];
        foreach ($list as $key => $item) {
            if($item['role_type'] == 1){
               $list[$key]['role_type'] = '投资人';
            }elseif($item['role_type'] == 2){
               $list[$key]['role_type'] = '项目方';
            }else{
               $list[$key]['role_type'] = '未选择';
            }
            $list[$key]['createTime'] = date('Y-m-d H:i:s' , $item['createTime']);
            $list[$key]['updateTime'] = date('Y-m-d H:i:s' , $item['updateTime']);
            if(stripos($item['card'],',')){
                $list[$key]['card'] = explode(',',$item['card']);
            }
            $data[] = $item->toArray();
        }
        $return['data'] = $data;
        $return['recordsFiltered'] = $total;
        $return['recordsTotal'] = $total;

        echo json_encode($return);
    }
    
    public function detail(){
        $id     = $this->request->param('id/d');
        $model  = model("Through");
        $info   = $model->getThroughInfoById($id);
        $this->assign("info" , $info);
        return view();
    }
    
    public function throughFalse(){
        $model = model("ThroughDefaultInfo");
        if($info = $model->createThroughDefaultInfo(input())){
            $result = $this->sendWeixinMessage($info, false, $info['content']);
            $result1 = $this->sendMobileMessage($info, $info['content']);
            $result1 = (array)$result1;
            if($result && $result1['code'] == 0){
                $data['url'] = url('Through/repulse');
                $this->result($data, 1, '认证失败操作成功', 'json');
            } else {
                $data['url'] = url('Through/repulse');
                $this->result($data, 1, '认证失败操作成功!微信:'.$result['errcode'].':'.$result['errmsg'].';短信:'.$result1['msg'], 'json');
            }
        }else{
            $error = $model->getError() ? $model->getError() : '认证失败操作失败';
            $this->result('', 0, $error, 'json');
        }
    }
    
    public function throughSuccess(){
        $id = $this->request->param('id/d');
        $model = model("Through");
        if($info = $model->setThroughSuccess($id)){
            $result1 = $this->sendMobileMessage($info, '认证成功');
            $result1 = (array)$result1;
            if($result1['code'] == 0){
                $data['url'] = url('Through/audited');
                $this->result($data, 1, '认证成功', 'json');
            } else {
                $data['url'] = url('Through/audited');
                $this->result($data, 1, '认证成功!'.$result1['code'].':'.$result1['msg'], 'json');
            }
        }else{
            $error = $model->getError() ? $model->getError() : '认证失败';
            $this->result('', 0, $error, 'json');
        }
    }
    
    public function stop(){
        $id = $this->request->param('id/d');
        $model = model("Through");
        if($model->setThroughStop($id)){
            $data['url'] = url('Through/termination');
            $this->result($data, 1, '终止成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '终止失败';
            $this->result('', 0, $error, 'json');
        }
    }
    
    
    public function start(){
        $id = $this->request->param('id/d');
        $model = model("Through");
        if($model->setThroughStart($id)){
            $data['url'] = url('Through/index');
            $this->result($data, 1, '还原成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '还原失败';
            $this->result('', 0, $error, 'json');
        }
    }
    
    
    public function sendWeixinMessage($data , $flag = false , $msg = ''){
        $config = config("WeiXinCg");
        $options = array(
            'token' => config("token"), //填写你设定的key
            'encodingaeskey' => config("encodingaeskey"), //填写加密用的EncodingAESKey，如接口为明文模式可忽略
            'appid' => $config['appid'],
            'appsecret' => $config['appsecret']
        );
        $weObj = new Wechat($options);
        $weObj->checkAuth('', '', getAccessTokenFromFile());
        if($flag == false){
            $title = '您好，你的审核请求暂未通过，请查看详情。';
        } else {
            $title = '您好，你的审核请求已通过，欢迎使用。';
        }
        $data = [
            'touser' => $data['openId'],
            'template_id' => '4MaCIlw6GfI49d-dTMiAltPGsOGTX5shqwLCg0x3waE',
            'url' => $_SERVER['SERVER_NAME'].'/home/my_center/index',
            'topcolor' => '#FF0000',
            'data' => [
                'first'    => ['value' => $title , 'color' => '#173177'],
                'keyword1' => ['value' => $data['realName'] , 'color' => '#E51717'],
                'keyword2' => ['value' => $data['userPhone'] , 'color' => '#E51717'],
                'keyword3' => ['value' => date('Y-m-d' , time()) , 'color' => '#E51717'],
                'remark'   => ['value' => htmlspecialchars($msg) , 'color' => '#3B55EA'],
            ]
        ];
        $result = $weObj->sendTemplateMessage($data);
        return $result;
    }
    
    public function sendMobileMessage($data , $msg = ''){
        $mobile = $data['userPhone'];
//        $apikey = "b4d4e18d335a960c008deca958c37326"; 
        $text="【FA財】您的审核情况已更新:".$msg;
//        $ch = curl_init();
//        // 设置验证方式
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept:text/plain;charset=utf-8', 'Content-Type:application/x-www-form-urlencoded','charset=utf-8'));
//        // 设置返回结果为流
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        // 设置超时时间
//        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
//        // 设置通信方式
//        curl_setopt($ch, CURLOPT_POST, 1);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//        $data=array('text'=>$text,'apikey'=>$apikey,'mobile'=>$mobile);
//        $json_data = $this->send($ch,$data);
//        $array = json_decode($json_data,true);
//        curl_close($ch);
        $model   = model("SendSms");
        
        vendor("Passport.Sms");
        $sms = new \Sms('ac67816e0f384393545628fc31dc3b17');
        $array = $sms->send($mobile, $text, null);
        
        return $array;
    }
    
    function send($ch,$data){
        curl_setopt ($ch, CURLOPT_URL, 'https://sms.yunpian.com/v2/sms/single_send.json');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $result = curl_exec($ch);
        $error = curl_error($ch);
        if(empty($error)){
            $result = $result;
        } else {
            $result = $error;
        }
        return $result;
    }
    
    
    //导出机构中心Excel表格
    public function through_audited_client()
        {
            $real_name  = $this->request->get('real_name', '');
            $phone      = $this->request->get('phone', '');
            $model      = model('Through');
            $list       = $model->getThroughAuditedInfo($real_name,$phone);
            if(through_audited_show($list)){
                return "ok";
            }else{
                return "no";
            }
        } 
}
