<?php
namespace app\home\controller;

class ProjectInfo extends Base
{   

    public function index()
    {
        $id     = $this->request->param('id/d');
        
        //会员收藏项目
        $user   = $this->jsGlobal;
        $uid    = $user['member']['uid'];
        
        $white  = model("whiteUid");
        $model  = model("project");
        if($white->getWhite($uid)){
            //查看人数+1
            $model->where(['pro_id'=>$id])->setInc('view_num');
        }
        
        
        $org_id      = 0;
        $org_flg     = 1;
        
        $memModel    = model("Member");
        $mem = $memModel->getMemberInfoById($uid);
        if($mem['userPhone']===''){
            $org_flg     = 0;
        }else{
            $org_model   = model("Organize");
            $org         = $org_model->getOrganizeInfoByTel($mem['userPhone']);
            if($org==false){
                $org_flg = 0;
            }else{
                if($org['is_confirm']==1){
                    $org_flg = 0;
                }else{
                    $org_id = $org['org_id'];
                }
            }
        }
        
        $this->assign('org_flg' , $org_flg);
        $this->assign('org_id' , $org_id);
        
        //项目详细信息
        $info1  = $model->getProjectInfoById($id);
        $this->assign('info1' , $info1);
        
        $userFollow = model('Member')->where(['uid'=>$uid])->find();
        $this->assign('follow',$userFollow['is_follow']);
        
        if(in_array($id, explode(',', $user['member']['collection']))){
            $this->assign('sign' , 'true');
        } else {
            $this->assign('sign' , 'false');
        }
        
       
        if(isset($user['through']) && $user['through']['status'] == 1){
            $this->assign('show' , 'false');
        } else {
            if(cookie('sign') != null && cookie('superior') != $user['member']['uid']){
                $this->assign('show' , 'true');
                $superior = cookie('superior');
                $user     = model("Member")->where(['uid' => $superior])->find();
                $this->assign('user' , $user);
            } else {
                $this->assign('show' , 'false');
            }
        }
        
		//功能: 企业的简介，财报，研报，新闻公告
		//项目企业财务指标
		$model    = model("project");
        $info     = $model->getProjectInfo($id);
        
        //圆盘Pie
        $data = [];$color = [];$name = [];
        $colors = ['#2e84cf','#308f8e','#f5a362','#3347c1','#74d8d8','#e4d03a','#9dcd7d','#39f0f0','#ee3130','#f05d80','#902c8e'];
        foreach ($info['gdyjs'] as $key => $value) {
            array_push($data, $value['cgsl']);
            array_push($name, $value['gdmc']);
            array_push($color, $colors[$key]);
        }
        
        
        if($info['gdyjs_num'] == 10){
            $other = intval(implode('',explode(',', $info['gszls']['zgbwg'])))*10000 - intval($info['cgsl_num']);
            if($other > 0){
                array_push($data, $other);
                array_push($name, '其它');
                array_push($color, $colors[10]);
            }
        }
        $this->assign('pie_data' , json_encode($data));
        $this->assign('pie_name' , json_encode($name));
        $this->assign('pie_color' , json_encode($color));
		//占比总数
		$t = 0;
		foreach($info['gdyjs'] as $key=>$value){
			$t = $t+$value['fen'];
		}
		$this->assign("t",$t);
        //树状图 - 利润(年报，中报)
        $data = [];$color = [];$name = [];$zhong = [];$name_zhong=[];
        foreach ($info['lirun'] as $key => $value) {
			$t = explode("-",$value['bbrq']);
			if($t[1] != "12"){
				//说明是中报
				array_push($zhong, round($value['xmsz']/10000));
				array_push($name_zhong, $value['bbrq']);
			}else{
				//说明是年报
				array_push($data, round($value['xmsz']/10000));
				array_push($name, $value['bbrq']);
			}
            array_push($color, $colors[2]);
        }
        $this->assign('lirun' , json_encode($info['lirun']));
        $this->assign('lirun_data' , json_encode($data));
		//中报数据
		$this->assign('lirun_zhong',json_encode($zhong));
        $this->assign('lirun_name' , json_encode($name));
		//中报时间
		$this->assign('lirun_name_zhong' , json_encode($name_zhong));
        $this->assign('lirun_color' , json_encode($color));
        //树状图 - 利润比
        $data = [];$color = [];$lirun_bi_zhong = [];
        foreach ($info['lirun_bi'] as $key => $value) {
			$t = explode("-",$value['bbrq']);
			if($t[1] != "12"){
				//说明是中报
				array_push($lirun_bi_zhong, $value['xmsz']);
			}else{
				//说明是年报
				array_push($data, $value['xmsz']);
			}
        }
        array_push($color, '#E53434');
        $this->assign('lirun_bi' , json_encode($info['lirun_bi']));
        $this->assign('lirun_bi_data' , json_encode($data));
		$this->assign('lirun_bi_zhong' , json_encode($lirun_bi_zhong));
        $this->assign('lirun_bi_color' , json_encode($color));
        //树状图 - 营业收入
        $data = [];$color = [];$name = [];$yingye_zhong = [];$yingye_time_zhong = [];
        foreach ($info['shouru'] as $key => $value) {
			$t = explode("-",$value['bbrq']);
			if($t[1] != "12"){
				//说明是中报
				array_push($yingye_zhong, round($value['xmsz']/10000));
				array_push($yingye_time_zhong, $value['bbrq']);
			}else{
				//说明是年报
				array_push($data, round($value['xmsz']/10000));
				array_push($name, $value['bbrq']);
			}
            array_push($color, $colors[2]);
        }
        $this->assign('shouru' , json_encode($info['shouru']));
        $this->assign('shouru_data' , json_encode($data));
        $this->assign('shouru_name' , json_encode($name));
		$this->assign('yingye_zhong' , json_encode($yingye_zhong));
        $this->assign('yingye_time_zhong' , json_encode($yingye_time_zhong));
        $this->assign('shouru_color' , json_encode($color));
        //树状图 - 营业收入比
        $data = [];$color = [];$ying_bi_zhong = [];
        foreach ($info['shouru_bi'] as $key => $value) {
			$t = explode("-",$value['bbrq']);
			if($t[1] != "12"){
				//说明是中报
				array_push($ying_bi_zhong, $value['xmsz']);
			}else{
				//说明是年报
				array_push($data, $value['xmsz']);
			}
        }
        array_push($color, '#E53434');
        $this->assign('shouru_bi' , json_encode($info['shouru_bi']));
        $this->assign('shouru_bi_data' , json_encode($data));
		$this->assign('ying_bi_zhong' , json_encode($ying_bi_zhong));
        $this->assign('shouru_bi_color' , json_encode($color));
        //树状图 - 每股净资产
        $data = [];$color = [];$name = [];$jing_zhong = [];$jing_time = [];
        foreach ($info['zichan'] as $key => $value) {
			$t = explode("-",$value['bbrq']);
			if($t[1] != "12"){
				//说明是中报
				array_push($jing_zhong, $value['xmsz']);
				array_push($jing_time, $value['bbrq']);
			}else{
				//说明是年报
				array_push($data, $value['xmsz']);
				array_push($name, $value['bbrq']);
			}
			
            //array_push($data, $value['xmsz']);
            array_push($color, $colors[2]);
            //array_push($name, $value['bbrq']);
        }
        $this->assign('zichan' , json_encode($info['zichan']));
        $this->assign('zichan_data' , json_encode($data));
        $this->assign('zichan_name' , json_encode($name));
		
		$this->assign('jing_zhong' , json_encode($jing_zhong));
        $this->assign('jing_time' , json_encode($jing_time));
		
        $this->assign('zichan_color' , json_encode($color));
        //树状图 - 每股净资产比
        $data = [];$color = [];$jing_bi_zhong = [];
        foreach ($info['zichan_bi'] as $key => $value) {
			$t = explode("-",$value['bbrq']);
			if($t[1] != "12"){
				//说明是中报
				array_push($jing_bi_zhong, $value['xmsz']);
			}else{
				//说明是年报
				array_push($data, $value['xmsz']);
			}
        }
        array_push($color, '#E53434');
        $this->assign('zichan_bi' , json_encode($info['zichan_bi']));
        $this->assign('zichan_bi_data' , json_encode($data));
		
		$this->assign('jing_bi_zhong' , json_encode($jing_bi_zhong));
		
        $this->assign('zichan_bi_color' , json_encode($color));
        //树状图 - 每股现金流
        $data = [];$color = [];$name = [];$xianjin_zhong=[];$xianjin_time_zhong = [];
        foreach ($info['xianjinliu'] as $key => $value) {
			$t = explode("-",$value['bbrq']);
			if($t[1] != "12"){
				//说明是中报
				array_push($xianjin_zhong, $value['xmsz']);
				array_push($xianjin_time_zhong, $value['bbrq']);
			}else{
				//说明是年报
				array_push($data, $value['xmsz']);
				array_push($name, $value['bbrq']);
			}
            array_push($color, $colors[2]);
        }
        $this->assign('xianjinliu' , json_encode($info['xianjinliu']));
        $this->assign('xianjinliu_data' , json_encode($data));
        $this->assign('xianjinliu_name' , json_encode($name));
		
		$this->assign('xianjin_zhong' , json_encode($xianjin_zhong));
        $this->assign('xianjin_time_zhong' , json_encode($xianjin_time_zhong));
		
        $this->assign('xianjinliu_color' , json_encode($color));
        //树状图 - 每股现金流比
        $data = [];$color = [];$xianjin_bi_zhong = [];
        foreach ($info['xianjinliu_bi'] as $key => $value) {
			$t = explode("-",$value['bbrq']);
			if($t[1] != "12"){
				//说明是中报
				array_push($xianjin_bi_zhong, $value['xmsz']);
			}else{
				//说明是年报
				array_push($data, $value['xmsz']);
			}
        }
        array_push($color, '#E53434');
        $this->assign('xianjinliu_bi' , json_encode($info['xianjinliu_bi']));
        $this->assign('xianjinliu_bi_data' , json_encode($data));
		
		$this->assign('xianjin_bi_zhong' , json_encode($xianjin_bi_zhong));
		
        $this->assign('xianjinliu_bi_color' , json_encode($color));
        //树状图 - 毛利率
        $data = [];$color = [];$name = [];$lilv_zhong=[];$lilv_time_zhong = [];
        foreach ($info['maolilv'] as $key => $value) {
			$t = explode("-",$value['bbrq']);
			if($t[1] != "12"){
				//说明是中报
				array_push($lilv_zhong, $value['xmsz']);
				array_push($lilv_time_zhong, $value['bbrq']);
			}else{
				//说明是年报
				array_push($data, $value['xmsz']);
				array_push($name, $value['bbrq']);
			}
			
            array_push($color, $colors[2]);
        }
        $this->assign('maolilv' , json_encode($info['maolilv']));
        $this->assign('maolilv_data' , json_encode($data));
        $this->assign('maolilv_name' , json_encode($name));
		
		$this->assign('lilv_zhong' , json_encode($lilv_zhong));
        $this->assign('lilv_time_zhong' , json_encode($lilv_time_zhong));
		
        $this->assign('maolilv_color' , json_encode($color));
        //树状图 - 毛利率比
        $data = [];$color = [];$lilv_bi_zhong = [];
        foreach ($info['maolilv_bi'] as $key => $value) {
			$t = explode("-",$value['bbrq']);
			if($t[1] != "12"){
				//说明是中报
				array_push($lilv_bi_zhong, $value['xmsz']);
			}else{
				//说明是年报
				array_push($data, $value['xmsz']);
			}
        }
        array_push($color, '#E53434');
        $this->assign('maolilv_bi' , json_encode($info['maolilv_bi']));
        $this->assign('maolilv_bi_data' , json_encode($data));
		
		$this->assign('lilv_bi_zhong' , json_encode($lilv_bi_zhong));
		
        $this->assign('maolilv_bi_color' , json_encode($color));
        //树状图 - 每股收益
        $data = [];$color = [];$name = [];$meigu_zhong=[];$meigu_time_zhong = [];
        foreach ($info['shouyi'] as $key => $value) {
			$t = explode("-",$value['bbrq']);
			if($t[1] != "12"){
				//说明是中报
				array_push($meigu_zhong, $value['xmsz']);
				array_push($meigu_time_zhong, $value['bbrq']);
			}else{
				//说明是年报
				array_push($data, $value['xmsz']);
				array_push($name, $value['bbrq']);
			}
            array_push($color, $colors[2]);
        }
        $this->assign('shouyi' , json_encode($info['shouyi']));
        $this->assign('shouyi_data' , json_encode($data));
        $this->assign('shouyi_name' , json_encode($name));
		
		$this->assign('meigu_zhong' , json_encode($meigu_zhong));
        $this->assign('meigu_time_zhong' , json_encode($meigu_time_zhong));
		
        $this->assign('shouyi_color' , json_encode($color));
        //树状图 - 每股收益比
        $data = [];$color = [];$meigu_bi_zhong = [];
        foreach ($info['shouyi_bi'] as $key => $value) {
			$t = explode("-",$value['bbrq']);
			if($t[1] != "12"){
				//说明是中报
				array_push($meigu_bi_zhong, $value['xmsz']);
			}else{
				//说明是年报
				array_push($data, $value['xmsz']);
			}
        }
        array_push($color, '#E53434');
        $this->assign('shouyi_bi' , json_encode($info['shouyi_bi']));
        $this->assign('shouyi_bi_data' , json_encode($data));
		
		$this->assign('meigu_bi_zhong' , json_encode($meigu_bi_zhong));
		
        $this->assign('shouyi_bi_color' , json_encode($color));
        //树状图 - 每股公积金
        $data = [];$color = [];$name = [];$gong_zhong = [];$gong_time_zhong = [];
        foreach ($info['gongjijin'] as $key => $value) {
			$t = explode("-",$value['bbrq']);
			if($t[1] != "12"){
				//说明是中报
				array_push($gong_zhong, $value['xmsz']);
				array_push($gong_time_zhong, $value['bbrq']);
			}else{
				//说明是年报
				array_push($data, $value['xmsz']);
				array_push($name, $value['bbrq']);
			}
            array_push($color, $colors[2]);
        }
        $this->assign('gongjijin' , json_encode($info['gongjijin']));
        $this->assign('gongjijin_data' , json_encode($data));
        $this->assign('gongjijin_name' , json_encode($name));
		
		$this->assign('gong_zhong' , json_encode($gong_zhong));
        $this->assign('gong_time_zhong' , json_encode($gong_time_zhong));
		
        $this->assign('gongjijin_color' , json_encode($color));
        //树状图 - 每股公积金比
        $data = [];$color = [];$gong_bi_zhong = [];
        foreach ($info['gongjijin_bi'] as $key => $value) {
			$t = explode("-",$value['bbrq']);
			if($t[1] != "12"){
				//说明是中报
				array_push($gong_bi_zhong, $value['xmsz']);
			}else{
				//说明是年报
				array_push($data, $value['xmsz']);
			}
        }
        array_push($color, '#E53434');
        $this->assign('gongjijin_bi' , json_encode($info['gongjijin_bi']));
        $this->assign('gongjijin_bi_data' , json_encode($data));
		
		$this->assign('gong_bi_zhong' , json_encode($gong_bi_zhong));
		
        $this->assign('gongjijin_bi_color' , json_encode($color));
        //树状图 - 每股未分配
        $data = [];$color = [];$name = [];$weifenpei_zhong = [];$weifenpei_time_zhong = [];
        foreach ($info['weifenpei'] as $key => $value) {
			$t = explode("-",$value['bbrq']);
			if($t[1] != "12"){
				//说明是中报
				array_push($weifenpei_zhong, $value['xmsz']);
				array_push($weifenpei_time_zhong, $value['bbrq']);
			}else{
				//说明是年报
				array_push($data, $value['xmsz']);
				array_push($name, $value['bbrq']);
			}
            array_push($color, $colors[2]);
        }
        $this->assign('weifenpei' , json_encode($info['weifenpei']));
        $this->assign('weifenpei_data' , json_encode($data));
        $this->assign('weifenpei_name' , json_encode($name));
		
		$this->assign('weifenpei_zhong' , json_encode($weifenpei_zhong));
        $this->assign('weifenpei_time_zhong' , json_encode($weifenpei_time_zhong));
		
        $this->assign('weifenpei_color' , json_encode($color));
        //树状图 - 每股未分配比
        $data = [];$color = [];$weifenpei_bi_zhong = [];
        foreach ($info['weifenpei_bi'] as $key => $value) {
			$t = explode("-",$value['bbrq']);
			if($t[1] != "12"){
				//说明是中报
				array_push($weifenpei_bi_zhong, $value['xmsz']);
			}else{
				//说明是年报
				array_push($data, $value['xmsz']);
			}
        }
        array_push($color, '#E53434');
        $this->assign('weifenpei_bi' , json_encode($info['weifenpei_bi']));
        $this->assign('weifenpei_bi_data' , json_encode($data));
		
		$this->assign('weifenpei_bi_zhong' , json_encode($weifenpei_bi_zhong));
		
        $this->assign('weifenpei_bi_color' , json_encode($color));
        $this->assign('info' , $info);
        
		
        $this->assign('title' , '- '.$info1['pro_name']);
        $this->assign('img' , $info1['top_img']);
        $this->assign('des' , $info1['introduction']);
        return view();
    }
    
    
    public function collection(){
        $sign = $this->request->param('sign/s');
        $id = $this->request->param('id/d');
        $model = model("ProjectInfo");
        if($model->setCollection($id , $sign)){
            if($sign == 'true'){
                $this->result('', 1, '收藏成功', 'json');
            } else {
                $this->result('', 1, '取消收藏成功', 'json');
            }
        } else {
            $error = $model->getError() ? $model->getError() : '系统出错,请联系管理员';
            $this->result('', 0, $error, 'json');
        }
    }
    
    public function forward_project(){
        $data = input();
        $member = $this->jsGlobal['member'];
        $forward_project = explode(',', $member['forward_project']);
        $result = true;
        $rr = '';
        if(!in_array($data['id'], $forward_project)){
            array_push($forward_project, $data['id']);
            $rr = trim(implode(',', $forward_project), ',');
            $result = model("Member")->where(['uid' => $member['uid']])->update(['forward_project' => $rr]);
            $member['forward_project'] = $rr;
            session('FINANCE_USER' , $member);
        }
        if($result){
            $this->result($rr, 1, '收藏成功', 'json');
        } else {
            $this->result($result, 0, '收藏失败', 'json');
        }
    }
    
    
    public function prolist(){
        $id          = $this->request->param('id/d');
        $show        = model("ProjectDictShow")->getInfo($id);
        if(!empty($show)){
            $model   = model("Project");
            $project = $model->getProjectShowDs($show['pro_ids']);
        }else{
            $project = array();
        }
        
        $user   = $this->jsGlobal;
        $news   = $user['member']['news'];
        
        $this->assign('title' , '-'.$show['title']);
        $this->assign('img' , 'http://fin.jrfacai.com/static/frontend/images/share-img.png');
        $this->assign('des' , $show['des']);
        $this->assign('project',$project);
        $this->assign('news' , $news);
        
        return view();
    }
	
}
