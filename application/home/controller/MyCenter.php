<?php

namespace app\home\controller;


class MyCenter extends Base{
    
    /**
     * 我的FA財钱包
     * @return unknown
     */
    public function index(){
        $info   = $this->jsGlobal;
        $model  = model("Member");
        $time   = date('Y-m' , time());
        $now    = $model->where('CREATE_TIME','like',$time.'%')->where(['superior' => $info['member']['uid'] , 'userType' => 2])->select();
        $this->assign('num' , count($now));
        $user   = $model->getMemberInfoById($info['member']['uid']);
        $this->assign('balance' , $user['balance']);
        $this->assign('last_dividend' , $user['last_dividend']);
        $this->assign('info' , $info);
        $model  = model("Bonus");
        $money  = $model->getBonusMoney();
        $this->assign('money' , $money);
        //未提现余额
        $list = model("PresentRecord")->where(['openid' => $info['member']['openId'] , 'status' => 0])->select();
        $yue = 0;
        foreach ($list as $key => $value) {
            $yue += intval($value['amount']);
        }
        $yue    = floatval($yue%100);
        $this->assign('yue' , $yue);
        $list1  = model("PresentRecord")->where(['openid' => $info['member']['openId']])->select();
        $total  = 0;
        foreach ($list1 as $key => $value) {
            $total += intval($value['amount']);
        }
        $total  = floatval($total%100)+ floatval($user['balance']);
        $this->assign('total' , $total);
        $this->assign('title' , '- 我的FA財钱包');
        $this->assign('img' , $info['member']['userPhoto']);
        $this->assign('des' , '我的FA財钱包!');
        //客服logo
        $model = model('Project');
        $project = $model->getProjectLimitOne();
        $this->assign('project',$project);
        return view();
    }
    //我提交的项目
    public function project_clue(){
        $info = $this->jsGlobal;
        $model = model("ProjectClue");
        $uid = session('FINANCE_USER.uid');
        $list = $model->getProjectClueList($uid);
        $this->assign('list' , $list);
        $this->assign('title' , '个人项目提交列表 -');
        $this->assign('img' , $info['member']['userPhoto']);
        $this->assign('des' , '个人项目提交列表');
        return view();
    }
    //我提交的项目详情
    public function project_clue_info(){
        $id = $this->request->param('id/d');
        $model = model("ProjectClue");
        $info = $model->getProjectClueInfoById($id);
        $this->assign('info' , $info);
        $info = $this->jsGlobal;
        $this->assign('title' , '个人项目提交列表 -');
        $this->assign('img' , $info['member']['userPhoto']);
        $this->assign('des' , '个人项目提交列表');
        return view();
    }
    
    
    //我收藏的项目
    public function project_collection(){
        $collection                     = session('FINANCE_USER.collection');
        $collection_project_require     = session('FINANCE_USER.collection_project_require');
        $collection_organize            = session('FINANCE_USER.collection_organize');
        $collection_organize_require    = session('FINANCE_USER.collection_organize_require');
        
        $model = model("Project");
        $list  = $model->getCollectionProjectList($collection);
        
        $model = model("Zfmxes");
        $list1 = $model->getCollectionZfmxesList($collection_zfmxes);
        
        if(empty($list1)){
            $list1 = [];
        }
        $model = model("ZryxesEffect");
        $list2 = $model->getCollectionZryxesList($collection_zryxes);
        
        if(empty($list2)){
            $list2 = [];
        }
        $info = $this->jsGlobal;
        
        $this->assign('title' , '个人项目收藏列表 -');
        $this->assign('img' , $info['member']['userPhoto']);
        $this->assign('des' , '个人项目收藏列表');
        $this->assign('list' , $list);
        $this->assign('list1' , $list1);
        $this->assign('list2' , $list2);
        return view();
    }
    //分红政策
    public function policy(){
        $model = model("SystemConfig");
        $info = $model->getSystemConfigInfo();
        $this->assign('info' , $info);
        $this->assign('title' , '分红政策 -');
        $this->assign('img' , '');
        $this->assign('des' , '金融合伙人分红政策');
        return view();
    }
    public function question(){
        $model = model("SystemConfig");
        $info = $model->getSystemConfigInfo();
        $this->assign('info' , $info);
        $this->assign('title' , '分红说明 -');
        $this->assign('img' , '');
        $this->assign('des' , '金融合伙人分红说明');
        return view();
    }
    //提现界面
    public function extract(){
        $info = $this->jsGlobal;
        $model = model("Member");
        $user = $model->getMemberInfoById($info['member']['uid']);
        $this->assign('balance' , $user['balance']);
        $this->assign('time' , date('m-d' , time()));
        $this->assign('title' , '个人中心 -');
        $this->assign('img' , $info['member']['userPhoto']);
        $this->assign('des' , '个人中心!');
        return view();
    }
    //申请提现
    public function createExtract(){
        $data = input();
        $model = model("PresentRecord");
        if($model->createPresentRecord($data)){
            $data['url'] = url('MyCenter/present_record');
            $this->result($data, 1, '申请提现成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '申请提现失败';
            $this->result('', 0, $error, 'json');
        }
    }
    //提现成功
    public function present_record(){
        $info = $this->jsGlobal;
        $this->assign('title' , '申请提现成功 -');
        $this->assign('img' , $info['member']['userPhoto']);
        $this->assign('des' , '个人申请提现界面');
        return view();
    }
    //提现记录表
    public function present_record_list(){
        $info = $this->jsGlobal;
        $model = model("PresentRecord");
        $list = $model->getPresentRecordList();
        $this->assign('list' , $list);
        $this->assign('title' , '提现记录表 -');
        $this->assign('img' , $info['member']['userPhoto']);
        $this->assign('des' , '个人提现记录表');
        return view();
    }
    public function collection_roadshow(){
        $road_show = session('FINANCE_USER.road_show');
        $model = model("RoadShow");
        $list = $model->getCollectionRoadShowList($road_show);
        if(!empty($list)){
            foreach ($list as $key => $value) {
                $list[$key]['duration'] = changeLongTime($value['duration']);
            }
        }
        $info = $this->jsGlobal;
        $this->assign('title' , '个人路演浏览列表 -');
        $this->assign('img' , $info['member']['userPhoto']);
        $this->assign('des' , '个人路演浏览列表');
        $this->assign('list' , $list);
        return view();
    }
    public function forward_project(){
        $forward_project = session('FINANCE_USER.forward_project');
        $model = model("Project");
        $list = $model->getCollectionProjectList($forward_project);
        $info = $this->jsGlobal;
        $this->assign('title' , '个人项目转发列表 -');
        $this->assign('img' , $info['member']['userPhoto']);
        $this->assign('des' , '个人项目转发列表');
        $this->assign('list' , $list);
        return view();
    }
    public function pay_record(){
        $info = $this->jsGlobal;
        $model = model("Order");
        $list = $model->getOrderListByUser();
        $config = ['type' => 'mysql','hostname' => 'rm-uf624yya653065csg.mysql.rds.aliyuncs.com' , 'database' => 'wei_read' , 'username' => 'wei_read' , 'password' => '1QAZ2wsx'];
        $list1 = db("ims_dg_article_payment", $config)->where(['openid' => $info['member']['openId'] , 'order_status' => 1])->select();
        if(!empty($list1)){
            foreach ($list1 as $key => $value) {
                $list1[$key]['name'] = $info['member']['userName'];
                $list1[$key]['phone'] = $info['member']['userPhone'];
            }
        }
        $list2 = db("ims_dg_article_recharge", $config)->where(['openid' => $info['member']['openId'] , 'rec_status' => 1])->select();
        if(!empty($list2)){
            foreach ($list2 as $key => $value) {
                $list2[$key]['name'] = $info['member']['userName'];
                $list2[$key]['phone'] = $info['member']['userPhone'];
            }
        }
        $list3 = db("ims_dg_article_shang", $config)->where(['openid' => $info['member']['openId'] , 'shang_status' => 1])->select();
        if(!empty($list3)){
            foreach ($list3 as $key => $value) {
                $list3[$key]['name'] = $info['member']['userName'];
                $list3[$key]['phone'] = $info['member']['userPhone'];
            }
        }
        $this->assign('list' , $list);
        $this->assign('list1' , $list1);
        $this->assign('list2' , $list2);
        $this->assign('list3' , $list3);
        $this->assign('title' , '个人支付记录 -');
        $this->assign('img' , $info['member']['userPhoto']);
        $this->assign('des' , '个人支付记录');
        return view();
    }
    public function my_study(){
        $info = $this->jsGlobal;
//		$data = input("id");
		//$data = "1";
		//$config = ['type' => 'mysql','hostname' => '47.96.172.250' , 'database' => 'wei_read' , 'username' => 'shulong' , 'password' => '123456'];
        $config = ['type' => 'mysql','hostname' => 'rm-uf624yya653065csg.mysql.rds.aliyuncs.com' , 'database' => 'wei_read' , 'username' => 'wei_read' , 'password' => '1QAZ2wsx'];
		//$this->assign('title' , '个人学习课程 -');
        //$this->assign('img' , $info['member']['userPhoto']);
        //$this->assign('des' , '个人学习课程');
		//查询的个人唯一标识
		$user = $info['member']['openId'];
		$member = model('Member');
		//阅读的文章
//		if($data == "" || $data == "1"){
			$result = $member->where('openId = "'.$user.'"')->find();
			$num = explode(',',$result['collection_study']);
			$result1 = db("ims_dg_article", $config)->where(['id' => array('in',$num) ])->select();
//		}
		//收藏文章
//		if($data == "2"){
			$result = db("ims_dg_article_collect", $config)->where(['openId' => $user ])->select();
			$arr = array();
			for($i=0;$i<count($result);$i++){
				array_push($arr,$result[$i]['article_id']);
			}
			$result2 = db("ims_dg_article", $config)->where(['id' => array('in',$arr) ])->select();
		
//		}
		//转发文章
//		if($data == "3"){
			$result = $member->where('openId = "'.$user.'"')->find();
			$num = explode(',',$result['collzhuan_study']);
			$result3 = db("ims_dg_article", $config)->where(['id' => array('in',$num) ])->select();
			
//		}
		
		$this->assign('data1' , $result1);
		$this->assign('data2' , $result2);
		$this->assign('data3' , $result3);
		$this->assign('openid',$user);
        return view();
    }
    public function signup_activity(){
        $info = $this->jsGlobal;
        $model = model("Activity");
        $list = [];$list1 = [];
        if($info['member']['collection_activity'] != ''){
            $list = $model->getActivityListByUser($info['member']['collection_activity']);
        }
        if($info['member']['pay_activity'] != ''){
            $list1 = $model->getActivityListByUser($info['member']['pay_activity']);
        }
        $this->assign('list' ,$list);
        $this->assign('list1' ,$list1);
        $this->assign('title' , '个人报名活动 -');
        $this->assign('img' , $info['member']['userPhoto']);
        $this->assign('des' , '个人报名活动');
        return view();
    }
    public function collection_activity(){
        $info = $this->jsGlobal;
        $model = model("Activity");
        $list = [];$list1 = [];
        if($info['member']['collection_activity'] != ''){
            $list = $model->getActivityListByUser($info['member']['collection_activity']);
        }
        if($info['member']['pay_activity'] != ''){
            $list1 = $model->getActivityListByUser($info['member']['pay_activity']);
        }
        $this->assign('list' ,$list);
        $this->assign('list1' ,$list1);
        $this->assign('title' , '个人收藏活动 -');
        $this->assign('img' , $info['member']['userPhoto']);
        $this->assign('des' , '个人收藏活动');
        return view();
    }
}
