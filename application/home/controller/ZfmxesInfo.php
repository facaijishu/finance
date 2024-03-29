<?php
namespace app\home\controller;

class ZfmxesInfo extends Base
{
    public function index(){
        $neeq               = $this->request->param('neeq/s');
        $plannoticeddate    = $this->request->param('plannoticeddate/s');
        $user = $this->jsGlobal;
        if(in_array($neeq.'_'.$plannoticeddate, explode(',', $user['member']['collection_zfmxes']))){
            $this->assign('sign' , 'true');
        } else {
            $this->assign('sign' , 'false');
        }
        
        $model      = model("ZfmxesIntention");
        $intent     = $model->getById($neeq,$user['member']['uid']);
        
        if($intent['id']>0){
            $this->assign('intention' , 'true');
        }else{
            $this->assign('intention' , 'false');
        }
        
        
        $model = model("Zfmxes");
        $info  = $model->getZfmxesCompleteInfo($neeq , $plannoticeddate);
        
        //圆盘Pie
        $data   = [];
        $color  = [];
        $name   = [];
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
		//var_dump($info['lirun']);
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
            //array_push($data, $value['xmsz']);
        }
        array_push($color, '#E53434');
        $this->assign('lirun_bi' , json_encode($info['lirun_bi']));
        $this->assign('lirun_bi_data' , json_encode($data));
		$this->assign('lirun_bi_zhong' , json_encode($lirun_bi_zhong));
        $this->assign('lirun_bi_color' , json_encode($color));
        
        //树状图 - 营业收入
        $data               = [];
        $color              = [];
        $name               = [];
        $yingye_zhong       = [];
        $yingye_time_zhong  = [];
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
        $data       = [];
        $color      = [];
        $name       = [];
        $jing_zhong = [];
        $jing_time  = [];
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
            array_push($color, $colors[2]);
        }
        $this->assign('zichan' , json_encode($info['zichan']));
        $this->assign('zichan_data' , json_encode($data));
        $this->assign('zichan_name' , json_encode($name));
		$this->assign('jing_zhong' , json_encode($jing_zhong));
        $this->assign('jing_time' , json_encode($jing_time));
        $this->assign('zichan_color' , json_encode($color));
        
        //树状图 - 每股净资产比
        $data           = [];
        $color          = [];
        $jing_bi_zhong  = [];
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
        $data               = [];
        $color              = [];
        $name               = [];
        $xianjin_zhong      = [];
        $xianjin_time_zhong = [];
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
        $data               = [];
        $color              = [];
        $xianjin_bi_zhong   = [];
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
        $data            = [];
        $color           = [];
        $name            = [];
        $lilv_zhong      = [];
        $lilv_time_zhong = [];
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
        $data           = [];
        $color          = [];
        $lilv_bi_zhong  = [];
        foreach ($info['maolilv_bi'] as $key => $value) {
			$t = explode("-",$value['bbrq']);
			if($t[1] != "12"){
				//说明是中报
				array_push($lilv_bi_zhong, $value['xmsz']);
			}else{
				//说明是年报
				array_push($data, $value['xmsz']);
			}
            //array_push($data, $value['xmsz']);
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
            //array_push($data, $value['xmsz']);
            array_push($color, $colors[2]);
            //array_push($name, $value['bbrq']);
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
            //array_push($data, $value['xmsz']);
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
            //array_push($data, $value['xmsz']);
            array_push($color, $colors[2]);
            //array_push($name, $value['bbrq']);
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
            //array_push($data, $value['xmsz']);
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
            //array_push($data, $value['xmsz']);
            array_push($color, $colors[2]);
            //array_push($name, $value['bbrq']);
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
            //array_push($data, $value['xmsz']);
        }
        array_push($color, '#E53434');
        $this->assign('weifenpei_bi' , json_encode($info['weifenpei_bi']));
        $this->assign('weifenpei_bi_data' , json_encode($data));
		
		$this->assign('weifenpei_bi_zhong' , json_encode($weifenpei_bi_zhong));
		
        $this->assign('weifenpei_bi_color' , json_encode($color));
        
        $this->assign('info' , $info);
        $this->assign('title' , '- '.$info['sec_uri_tyshortname']);
        $this->assign('img' , '');
        $this->assign('des' , '这里有最详细的增发明细!');
        return view();
    }
    
    /**
     * 收藏项目
     */
    public function collection(){
        $sign = $this->request->param('sign/s');
        $neeq = $this->request->param('neeq/s');
        $plannoticeddate = $this->request->param('plannoticeddate/s');
        $model = model("Zfmxes");
        if($model->setCollection($neeq , $plannoticeddate , $sign)){
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
    
    
    public function service(){
        $neeq = $this->request->param('neeq/s');
        $plannoticeddate = $this->request->param('plannoticeddate/s');
        $model = model("Zfmxes");
        $info = $model->getZfmxesSimpleInfo($neeq , $plannoticeddate);
        $this->assign('info' , $info);
        $this->assign('title' , '联系客服 -');
        $this->assign('img' , '');
        $this->assign('des' , '金融合伙人客服很高兴为您服务!');
        return view();
    }
    
    /**
     * 申请意向提交
     */
    public function purpose(){
        $neeq               = $this->request->param('neeq/s');
        $plannoticeddate    = $this->request->param('plannoticeddate/s');
        $user               = $this->jsGlobal;
        $model              = model("ZfmxesIntention");
        $intent             = $model->getById($neeq,$user['member']['uid']);
        if(empty($intent)){
            $data   = [];
            $data['company']         = $user['member']['company'];
            $data['phone']           = $user['member']['userPhone'];
            $data['create_uid']      = $user['member']['uid'];
            $data['neeq']            = $neeq;
            $data['plannoticeddate'] = $plannoticeddate;
            $data['num']             = 1;
            $data['money']           = 1;
            if($model->createZfmxesIntention($data)){
                $this->sendWechat($user['member']['uid'],$user['member']['userName'],$user['member']['userPhone'],$user['member']['company'],$neeq);
                $this->result($data, 1, '意向登记成功', 'json');
            }else{
                $error = $model->getError() ? $model->getError() : '意向登记失败';
                $this->result('', 0, $error, 'json');
            }
        }else{
            $error  = '你已经登记过了';
            $this->result('', 0, $error, 'json');
        }
    }
    
    /**
     * 发送定增项目咨询的微信推动消息
     * @param 微信昵称 $name
     * @param 手机号码 $phone
     * @param 股票代码 $neeq
     */
    public function  sendWechat($uid,$name,$phone,$company,$neeq){
        $template_id = "rQ53Qg49ZU-9oOCINXeqTMydpBsu-VaIgGwJuF9Oz-U";
        $openid      = "osmRL1gkuFXpLndZtwRhqzZv5PWo";
        $array = [
            'first'    => ['value' => '你有一条来自定增项目【'.$neeq.'】的意向咨询' , 'color' => '#173177'],
            'keyword1' => ['value' => $name."(".$phone.")" , 'color' => '#777777'],
            'keyword2' => ['value' => $company.'的人员对定增项目【'.$neeq.'】有意向' , 'color' => '#777777'],
            'remark'   => ['value' => '点击立即沟通吧!' , 'color' => '#173177'],
        ];
        wechatLog("定增咨询的微信消息推送开始");
        //$re = sendTemplateMessage($template_id, $openid, $_SERVER['SERVER_NAME'].'/console/index/index#/console/intention/index/menu_id/209/menu_type/1', $array);
        $re = sendTemplateMessage($template_id, $openid, $_SERVER['SERVER_NAME'].'/home/chat/index/to_uid/'.$uid, $array);
        wechatLog("定增咨询的推送结果为：".json_encode($re));
        wechatLog("定增咨询的微信消息推送结束");
    }
}
