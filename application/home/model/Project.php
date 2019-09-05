<?php

namespace app\home\model;
use think\Db;

class Project extends BaseModel
{   
    
    public function getProjectLimitList($length){
        $result = $this->where(['status' => 2 , 'flag' => 1])->order("list_order desc")->limit($length)->select();
        foreach ($result as $key => $value) {
            $inc_industry = explode(',', $value['inc_industry']);
            $model = model("dict");
            $transfer_type = $model->getDictInfoById($value['transfer_type']);
            $result[$key]['transfer_type'] = $transfer_type['value'];
            $capital_plan = $model->getDictInfoById($value['capital_plan']);
            $result[$key]['capital_plan'] = $capital_plan['value'];
            $hierarchy = $model->getDictInfoById($value['hierarchy']);
            $result[$key]['hierarchy'] = $hierarchy['value'];
            foreach ($inc_industry as $key1 => $value1) {
                $info = $model->getDictInfoById($value1);
                $inc_industry[$key1] = $info['value'];
            }
            $result[$key]['inc_industry'] = $inc_industry;
            $model = model("material_library");
            //列表页头图
            $top_img = $model->getMaterialInfoById($value['top_img']);
            $result[$key]['top_img'] = $top_img['url'];
            $result[$key]['top_img_compress'] = $top_img['url_compress'];
            //首页头图
            $index_img = $model->getMaterialInfoById($value['index_img']);
            $result[$key]['index_img'] = $index_img['url'];
            $result[$key]['index_img_compress'] = $index_img['url_compress'];
            $company_video = $model->getMaterialInfoById($value['company_video']);
            $result[$key]['company_video'] = $company_video['url'];
        }
        return $result;
    }

    //获取简单的项目信息
    public function getProjectSimpleInfoById($id){
        if(empty($id)){
            $this->error = '该项目ID不存在';
            return false;
        }
        $info    = $this->where(['pro_id' => $id])->find();
        $erweima = db("qrcode_direction")->where(['id' => $info['qr_code']])->find();
        $info['qr_code'] = $erweima['qrcode_path'];
        
        $inc_industry = explode(',', $info['inc_industry']);
        
        $model = model("material_library");
        $top_img = $model->getMaterialInfoById($info['top_img']);
        $info['top_img'] = $top_img['url'];
        
        $index_img = $model->getMaterialInfoById($info['index_img']);
        $info['index_img'] = $index_img['url'];
        
        $model = model("dict");
        $transfer_type = $model->getDictInfoById($info['transfer_type']);
        $info['transfer_type'] = $transfer_type['value'];
        
        $hierarchy = $model->getDictInfoById($info['hierarchy']);
        $info['hierarchy'] = $hierarchy['value'];
        
        foreach ($inc_industry as $key1 => $value1) {
            $result = $model->getDictInfoById($value1);
            $inc_industry[$key1] = $result['value'];
        }
        $info['inc_industry'] = $inc_industry;
        return $info;
    }
    
    
    //获取完整的项目信息
    public function getProjectInfoById($id){
        if(empty($id)){
            $this->error = '该项目ID不存在';
            return false;
        }
        
        
        $info                           = $this->where(['pro_id' => $id])->find();
        
        //项目问答类型
        $model                          = model("dict");
        $qa_type                        = $model->getDictByType("qa_type");
        $info['qa_type']                = $qa_type;
        
        //交易方式
        $transfer_type                  = $model->getDictInfoById($info['transfer_type']);
        $info['transfer_type']          = $transfer_type['value'];
        
        //所属层级
        $hierarchy                      = $model->getDictInfoById($info['hierarchy']);
        $info['hierarchy']              = $hierarchy['value'];
        
        //所属行业
        $inc_industry                   = explode(',', $info['inc_industry']);
        foreach ($inc_industry as $key1 => $value1) {
            $result                     = $model->getDictInfoById($value1);
            $inc_industry[$key1]        = $result['value'];
        }
        $info['inc_industry']           = $inc_industry;
        
        //投资亮点
        if($info['investment_lights'] != ''){
            $str                        = preg_replace('/\n/', '<br>', $info['investment_lights']);
            $investment_lights          = explode('<br>', $str);
            $info['investment_lights']  = $investment_lights;
        } else {
            $info['investment_lights']  = [];
        }
        
        //企业亮点描述
        if($info['enterprise_des'] != ''){
            $str = preg_replace('/\n/', '<br>', $info['enterprise_des']);
            $enterprise_des             = explode('<br>', $str);
            $info['enterprise_des']     = $enterprise_des;
        } else {
            $info['enterprise_des']     = [];
        }
        
        //公司简介描述
        if($info['company_des'] != ''){
            $str = preg_replace('/\n/', '<br>', $info['company_des']);
            $company_des                = explode('<br>', $str);
            $info['company_des']        = $company_des;
        } else {
            $info['company_des']        = [];
        }
        
        //核心产品描述
        if($info['product_des'] != ''){
            $str                        = preg_replace('/\n/', '<br>', $info['product_des']);
            $product_des                = explode('<br>', $str);
            $info['product_des']        = $product_des;
        } else {
            $info['product_des']        = [];
        }
        
        //行业分析描述
        if($info['analysis_des'] != ''){
            $str                        = preg_replace('/\n/', '<br>', $info['analysis_des']);
            $analysis_des               = explode('<br>', $str);
            $info['analysis_des']       = $analysis_des;
        } else {
            $info['analysis_des']       = [];
        }
        
        //项目新闻公告
        $model                          = model("ProjectNews");
        $project_news                   = $model->getProjectNewsList($id);
        
        //项目新闻公告图片获取
        $model                          = model("ProjectQa");
        $project_qa                     = $model->getProjectQa($id);
        $info['project_qa']             = $project_qa;
        
        $model                          = model("material_library");
        foreach ($project_news as $key => $value) {
            $top_img                        = $model->getMaterialInfoById($value['top_img']);
            $project_news[$key]['top_img']  = $top_img['url'];
            $project_news[$key]['time']     = substr($value['create_time'], 0, 10);
            $index_img                      = $model->getMaterialInfoById($value['index_img']);
            $project_news[$key]['index_img']= $index_img['url'];
            
            
        }
        $info['project_news']           = $project_news;
        $top_img                        = $model->getMaterialInfoById($info['top_img']);
        $info['top_img']                = $top_img['url'];
        
        $index_img                      = $model->getMaterialInfoById($info['index_img']);
        $info['index_img']              = $index_img['url'];
        
        //企业亮点
        $arr = [];
        if($info['enterprise_url'] != ''){
            $enterprise_url     = explode(',', $info['enterprise_url']);
            foreach ($enterprise_url as $key => $value) {
                $img            = $model->getMaterialInfoById($value);
                array_push($arr, $img['url']);
            }
        }
        $info['enterprise_url'] = $arr;
        
        //公司简介
        $arr = [];
        if($info['company_url'] != ''){
            $company_url        = explode(',', $info['company_url']);
            foreach ($company_url as $key => $value) {
                $img = $model->getMaterialInfoById($value);
                array_push($arr, $img['url']);
            }
        }
        $info['company_url']    = $arr;
        
        //核心产品
        $arr = [];
        if($info['product_url'] != ''){
            $product_url        = explode(',', $info['product_url']);
            foreach ($product_url as $key => $value) {
                $img            = $model->getMaterialInfoById($value);
                array_push($arr, $img['url']);
            }
        }
        $info['product_url']    = $arr;
        
        //行业分析
        $arr = [];
        if($info['analysis_url'] != ''){
            $analysis_url       = explode(',', $info['analysis_url']);
            foreach ($analysis_url as $key => $value) {
                $img            = $model->getMaterialInfoById($value);
                array_push($arr, $img['url']);
            }
        }
        $info['analysis_url']   = $arr;
        
        //商业计划书
        $business_plan              = $model->getMaterialInfoById($info['business_plan']);
        $info['business_plan']      = $business_plan['name'];
        $info['business_plan_url']  = $business_plan['url'];
        
        //项目要素
        $factor_table               = $model->getMaterialInfoById($info['factor_table']);
        $info['factor_table']       = $factor_table['name'];
        $info['factor_table_url']   = $factor_table['url'];
        
        //企业视频
        $company_video              = $model->getMaterialInfoById($info['company_video']);
        $info['company_video']      = $company_video['name'];
        $info['company_video_url']  = $company_video['url'];
        
        return $info;
    }
    
    //获取项目列表
    public function getProjectList($type = 2 , $sign = ''){
        if($sign != ''){
            $where = "`status` = 2 and `flag` = 1 and `inc_sign` like '%".$sign."%'";
        } else {
            $where = '`status` = 2 and `flag` = 1';
        }
        $result = $this->where($where)->order("list_order desc")->select();
        foreach ($result as $key => $value) {
            $inc_industry = explode(',', $value['inc_industry']);
            $model = model("dict");
            $transfer_type = $model->getDictInfoById($value['transfer_type']);
            $result[$key]['transfer_type'] = $transfer_type['value'];
            $hierarchy = $model->getDictInfoById($value['hierarchy']);
            $result[$key]['hierarchy'] = $hierarchy['value'];
            foreach ($inc_industry as $key1 => $value1) {
                $info = $model->getDictInfoById($value1);
                $inc_industry[$key1] = $info['value'];
            }
            $result[$key]['inc_industry'] = $inc_industry;
            $model = model("material_library");
            $top_img = $model->getMaterialInfoById($value['top_img']);
            $result[$key]['top_img'] = $top_img['url'];
            $company_video = $model->getMaterialInfoById($value['company_video']);
            $result[$key]['company_video'] = $company_video['url'];
        }
        return $result;
    }
    
    public function getProjectListByName($text){
        if(empty($text)){
            $result = $this->where('status = 2 and flag = 1')->select();
        } else {
            $result = $this->where('pro_name like "%'.$text.'%" and status = 2 and flag = 1')->select();
        }
        foreach ($result as $key => $value) {
            $inc_industry = explode(',', $value['inc_industry']);
            $model = model("dict");
            $transfer_type = $model->getDictInfoById($value['transfer_type']);
            $result[$key]['transfer_type'] = $transfer_type['value'];
            $hierarchy = $model->getDictInfoById($value['hierarchy']);
            $result[$key]['hierarchy'] = $hierarchy['value'];
            foreach ($inc_industry as $key1 => $value1) {
                $info = $model->getDictInfoById($value1);
                $inc_industry[$key1] = $info['value'];
            }
            $result[$key]['inc_industry'] = $inc_industry;
            $model = model("material_library");
            $top_img = $model->getMaterialInfoById($value['top_img']);
            $result[$key]['top_img'] = $top_img['url'];
            $company_video = $model->getMaterialInfoById($value['company_video']);
            $result[$key]['company_video'] = $company_video['url'];
        }
        return $result;
    }

    //获取收藏的项目列表
    public function getCollectionProjectList($collection){
        if(!empty($collection)){
            $collection = explode(',', $collection);
            $result = [];
            foreach ($collection as $key => $value) {
                $project = $this->where(['pro_id' => $value])->find();
                
                $model          = model("dict");
                $transfer_type  = $model->getDictInfoById($project['transfer_type']);
                $project['transfer_type'] = $transfer_type['value'];
                
                $hierarchy              = $model->getDictInfoById($project['hierarchy']);
                $project['hierarchy']   = $hierarchy['value'];
                
                $inc_industry           = explode(',', $project['inc_industry']);
                
                foreach ($inc_industry as $key1 => $value1) {
                    $info = $model->getDictInfoById($value1);
                    $inc_industry[$key1] = $info['value'];
                }
                $project['inc_industry'] = $inc_industry;
                
                $model   = model("material_library");
                $top_img = $model->getMaterialInfoById($project['top_img']);
                $project['top_img'] = $top_img['url'];
                array_push($result, $project);
            }
        } else {
            $result = '';
        }
        return $result;
    }
    
    
	//获取企业的财务指标
	public function getProjectInfo($id){
        if(empty($id)){
            $this->error = '该项目ID不存在';
            return false;
        }
        
        $info               = $this->where(['pro_id'=>$id])->find();
		$info['neeq']       = $info['stock_code'];
        $gszls              = db("gszls")->where(['neeq' => $info['neeq']])->find();
        $info['gszls']      = $gszls;
        
        //截止日期
        $gdyjs_date         = db("gdyjs")->where(['neeq' => $info['neeq']])->group("jzrq desc")->field("jzrq")->select();
        
        //前10名股东
        if(!empty($gdyjs_date)){
            $gdyjs          = db("gdyjs")->where(['neeq' => $info['neeq'] , 'jzrq' => $gdyjs_date[0]['jzrq']])->limit(10)->select();
        } else {
            $gdyjs          = [];
        }
		
        $cgsl_num = 0;
        $cgbl_num = 0;
        if(!empty($gdyjs)){
            foreach ($gdyjs as $key => $value) {
                $cgsl_num = intval($cgsl_num) + intval($value['cgsl']);
                $cgbl_num = floatval($cgbl_num) + floatval($value['cgbl']);
            }
        }
        
		//占比
		if(!empty($gszls['zgbwg'])){
			foreach($gdyjs as $key=>$value){
				$gdyjs[$key]['fen'] = round(floatval($gdyjs[$key]['cgsl']/(preg_replace('/,*/','',$gszls['zgbwg'])*10000)),4)*100;
			}
		}else{
			foreach($gdyjs as $key=>$value){
				$gdyjs[$key]['fen'] = '';
			}
		}
        $info['gdyjs']      = $gdyjs;
        $info['gdyjs_num']  = count($gdyjs);
        $info['cgsl_num']   = $cgsl_num;
        $info['cgbl_num']   = $cgbl_num;

        $ggzls              = db("ggzls")->where(['neeq' => $info['neeq']])->where("deleted_at is null")->select();
        $info['ggzls']      = $ggzls;
        
        $gonggaos           = db("st_gonggaos")->alias("gg")
                                               ->join("StSceus s" , "gg.ggid=s.ggid")
                                               ->where("s.secu_link_code","eq",$info['neeq'])
                                               ->field("gg.*,s.secu_s_name")
                                               ->select();
        $info['gonggaos']   = $gonggaos;
        
        
        $yanbaos            = db("st_yanbaos_effect")->where("relation","like","%".$info['neeq']."%")->select();
        $info['yanbaos']    = $yanbaos;
        
        //利润树状图
        $lirun              = db("cwbbsjs")->where(['neeq' => $info['neeq'] , 'type' => 1 , 'bbxm' => 'NETPROFIT'])->field("bbrq,xmsz")->order("bbrq asc")->select();
        $lirun_bi           = db("cwbbsjs")->where(['neeq' => $info['neeq'] , 'type' => 3 , 'bbxm' => 'PROFIT_YOY'])->field("bbrq,xmsz")->order("bbrq asc")->select();
        if(!empty($lirun) && !empty($lirun_bi)){
            foreach ($lirun as $key => $value) {
                foreach ($lirun_bi as $key1 => $value1) {
                    if($value1['bbrq'] == $value['bbrq']){
                        $lirun[$key]['lirun_bi'] = $value1['xmsz'];
                    }
                }
            }
        } else {
            foreach ($lirun as $key => $value) {
                $lirun[$key]['lirun_bi'] = '-';
            }
        }
		
        $arr                = $this->getDataAndBi($lirun, $lirun_bi);
        $info['lirun']      = $arr[0];
        $info['lirun_bi']   = $arr[1];
        
        
        //营业收入树状图
        $shouru             = db("cwbbsjs")->where(['neeq' => $info['neeq'] , 'type' => 1 , 'bbxm' => 'OPERATEREVE'])->field("bbrq,xmsz")->order("bbrq asc")->select();
        $shouru_bi          = db("cwbbsjs")->where(['neeq' => $info['neeq'] , 'type' => 3 , 'bbxm' => 'revenue_YOY'])->field("bbrq,xmsz")->order("bbrq asc")->select();
        if(!empty($shouru) && !empty($shouru_bi)){
            foreach ($shouru as $key => $value) {
                foreach ($shouru_bi as $key1 => $value1) {
                    if($value1['bbrq'] == $value['bbrq']){
                        $shouru[$key]['shouru_bi'] = $value1['xmsz'];
                    }
                }
            }
        } else {
            foreach ($shouru as $key => $value) {
                $shouru[$key]['shouru_bi'] = '-';
            }
        }
        
        $arr                = $this->getDataAndBi($shouru, $shouru_bi);
        $info['shouru']     = $arr[0];
        $info['shouru_bi']  = $arr[1];
        
        
        //每股净资产树状图
        $zichan             = db("cwbbsjs")->where(['neeq' => $info['neeq'] , 'type' => 3 , 'bbxm' => 'BPS'])->field("bbrq,xmsz")->order("bbrq asc")->select();
        $zichan_bi          = [];
        foreach ($zichan as $key => $value) {
            if($key == 0){
                array_push($zichan_bi, ['bbrq' => $value['bbrq'] , 'xmsz' => 0]);
                $zichan[$key]['zichan_bi'] = 0;
            } else {
                if(floatval($zichan[$key-1]['xmsz'] == 0)){
                    $val = 0;
                } else {
                    $val = (floatval($value['xmsz'])-floatval($zichan[$key-1]['xmsz']))/floatval($zichan[$key-1]['xmsz']);
                    $val = round(floatval($val), 2);
                }
                array_push($zichan_bi, ['bbrq' => $value['bbrq'] , 'xmsz' => $val]);
                $zichan[$key]['zichan_bi'] = $val;
            }
        }
        
        
        $arr = $this->getDataAndBi($zichan, $zichan_bi);
        $info['zichan']     = $arr[0];
        $info['zichan_bi']  = $arr[1];
        //每股现金流树状图
        $xianjinliu         = db("cwbbsjs")->where(['neeq' => $info['neeq'] , 'type' => 3 , 'bbxm' => 'CFPS'])->field("bbrq,xmsz")->order("bbrq asc")->select();
        $xianjinliu_bi      = [];
        foreach ($xianjinliu as $key => $value) {
            if($key == 0){
                array_push($xianjinliu_bi, ['bbrq' => $value['bbrq'] , 'xmsz' => 0]);
                $xianjinliu[$key]['xianjinliu_bi'] = 0;
            } else {
                if(floatval($xianjinliu[$key-1]['xmsz'] == 0)){
                    $val = 0;
                } else {
                    $val = (floatval($value['xmsz'])-floatval($xianjinliu[$key-1]['xmsz']))/floatval($xianjinliu[$key-1]['xmsz']);
                    $val = round(floatval($val), 2);
                }
                array_push($xianjinliu_bi, ['bbrq' => $value['bbrq'] , 'xmsz' => $val]);
                $xianjinliu[$key]['xianjinliu_bi'] = $val;
            }
        }
        $arr = $this->getDataAndBi($xianjinliu, $xianjinliu_bi);
        $info['xianjinliu']     = $arr[0];
        $info['xianjinliu_bi']  = $arr[1];
        
        //毛利率树状图
        $maolilv    = db("cwbbsjs")->where(['neeq' => $info['neeq'] , 'type' => 3 , 'bbxm' => 'GROSSPROFITMARGIN'])->field("bbrq,xmsz")->order("bbrq asc")->select();
        $maolilv_bi = [];
        foreach ($maolilv as $key => $value) {
            if($key == 0){
                array_push($maolilv_bi, ['bbrq' => $value['bbrq'] , 'xmsz' => 0]);
                $maolilv[$key]['maolilv_bi'] = 0;
            } else {
                if(floatval($maolilv[$key-1]['xmsz'] == 0)){
                    $val = 0;
                } else {
                    $val = (floatval($value['xmsz'])-floatval($maolilv[$key-1]['xmsz']))/floatval($maolilv[$key-1]['xmsz']);
                    $val = round(floatval($val), 2);
                }
                array_push($maolilv_bi, ['bbrq' => $value['bbrq'] , 'xmsz' => $val]);
                $maolilv[$key]['maolilv_bi'] = $val;
            }
        }
        $arr = $this->getDataAndBi($maolilv, $maolilv_bi);
        $info['maolilv']    = $arr[0];
        $info['maolilv_bi'] = $arr[1];
        
        //每股收益树状图
        $shouyi = db("cwbbsjs")->where(['neeq' => $info['neeq'] , 'type' => 1 , 'bbxm' => 'BASICEPS'])->field("bbrq,xmsz")->order("bbrq asc")->select();
        $shouyi_bi = [];
        foreach ($shouyi as $key => $value) {
            if($key == 0){
                array_push($shouyi_bi, ['bbrq' => $value['bbrq'] , 'xmsz' => 0]);
                $shouyi[$key]['shouyi_bi'] = 0;
            } else {
                if(floatval($shouyi[$key-1]['xmsz'] == 0)){
                    $val = 0;
                } else {
                    $val = (floatval($value['xmsz'])-floatval($shouyi[$key-1]['xmsz']))/floatval($shouyi[$key-1]['xmsz']);
                    $val = round(floatval($val), 2);
                }
                array_push($shouyi_bi, ['bbrq' => $value['bbrq'] , 'xmsz' => $val]);
                $shouyi[$key]['shouyi_bi'] = $val;
            }
        }
        $arr = $this->getDataAndBi($shouyi, $shouyi_bi);
        $info['shouyi']     = $arr[0];
        $info['shouyi_bi']  = $arr[1];
        
        //每股公积金树状图
        $gongjijin = db("cwbbsjs")->where(['neeq' => $info['neeq'] , 'type' => 3 , 'bbxm' => 'SURPLUSCAPITALPS'])->field("bbrq,xmsz")->order("bbrq asc")->select();
        $gongjijin_bi = [];
        foreach ($gongjijin as $key => $value) {
            if($key == 0){
                array_push($gongjijin_bi, ['bbrq' => $value['bbrq'] , 'xmsz' => 0]);
                $gongjijin[$key]['gongjijin_bi']  = 0;
            } else {
                if(floatval($gongjijin[$key-1]['xmsz'] == 0)){
                    $val = 0;
                } else {
                    $val = (floatval($value['xmsz'])-floatval($gongjijin[$key-1]['xmsz']))/floatval($gongjijin[$key-1]['xmsz']);
                    $val = round(floatval($val), 2);
                }
                array_push($gongjijin_bi, ['bbrq' => $value['bbrq'] , 'xmsz' => $val]);
                $gongjijin[$key]['gongjijin_bi']  = $val;
            }
        }
        $arr = $this->getDataAndBi($gongjijin, $gongjijin_bi);
        $info['gongjijin']      = $arr[0];
        $info['gongjijin_bi']   = $arr[1];
        //每股未分配树状图
        $weifenpei      = db("cwbbsjs")->where(['neeq' => $info['neeq'] , 'type' => 3 , 'bbxm' => 'UNDISTRIBUTEDPS'])->field("bbrq,xmsz")->order("bbrq asc")->select();
        $weifenpei_bi   = [];
        foreach ($weifenpei as $key => $value) {
            if($key == 0){
                array_push($weifenpei_bi, ['bbrq' => $value['bbrq'] , 'xmsz' => 0]);
                $weifenpei[$key]['weifenpei_bi']  = 0;
            } else {
                if(floatval($weifenpei[$key-1]['xmsz'] == 0)){
                    $val = 0;
                } else {
                    $val = (floatval($value['xmsz'])-floatval($weifenpei[$key-1]['xmsz']))/floatval($weifenpei[$key-1]['xmsz']);
                    $val = round(floatval($val), 2);
                }
                array_push($weifenpei_bi, ['bbrq' => $value['bbrq'] , 'xmsz' => $val]);
                $weifenpei[$key]['weifenpei_bi']  = $val;
            }
        }
        $arr = $this->getDataAndBi($weifenpei, $weifenpei_bi);
        $info['weifenpei']      = $arr[0];
        $info['weifenpei_bi']   = $arr[1];
        //利润表
        $cwbbs1 = db("cwbbs")->where(['neeq' => $info['neeq'] , 'type' => 1])->order("reportdate desc")->select();
        if(!empty($cwbbs1)){
            foreach ($cwbbs1 as $key => $value) {
                if(substr($value['reportdate'], 5, 2) == '12'){
                    $sign = '年';
                } else {
                    $sign = '中';
                }
                $cwbbs1[$key]['reportdate'] = substr($value['reportdate'], 0, 4).$sign.'报';
                $values     = [];
                $cwbbs_info = db("cwbbsjs")->where(['neeq' => $info['neeq'] , 'type' => 1 , 'bbrq' => $value['reportdate']])->select();
                foreach ($cwbbs_info as $key1 => $value1) {
                    $xmsz       = round(trim($value1['xmsz'], '-'), 2);
                    if($xmsz > 100000000){
                        $zhi    = round($value1['xmsz']/100000000 , 2).'亿';
                    }elseif ($xmsz > 10000) {
                        $zhi    = round($value1['xmsz']/10000 , 2).'万';
                    } else {
                        $zhi    = round($value1['xmsz'] , 2).'元';
                    }
                    $values[$value1['bbxm']] = $zhi;
                }
                $cwbbs1[$key]['value']       = $values;
            }
        }
        $info['cwbbs1'] = $cwbbs1;
        //资产负债表
        $cwbbs2 = db("cwbbs")->where(['neeq' => $info['neeq'] , 'type' => 0])->order("reportdate desc")->select();
        if(!empty($cwbbs2)){
            foreach ($cwbbs2 as $key => $value) {
                if(substr($value['reportdate'], 5, 2) == '12'){
                    $sign = '年';
                } else {
                    $sign = '中';
                }
                $cwbbs2[$key]['reportdate'] = substr($value['reportdate'], 0, 4).$sign.'报';
                $values     = [];
                $cwbbs_info = db("cwbbsjs")->where(['neeq' => $info['neeq'] , 'type' => 0 , 'bbrq' => $value['reportdate']])->select();
                foreach ($cwbbs_info as $key1 => $value1) {
                    $xmsz       = round(trim($value1['xmsz'], '-'), 2);
                    if($xmsz > 100000000){
                        $zhi = round($value1['xmsz']/100000000 , 2).'亿';
                    }elseif ($xmsz > 10000) {
                        $zhi    = round($value1['xmsz']/10000 , 2).'万';
                    } else {
                        $zhi    = round($value1['xmsz'] , 2).'元';
                    }
                    $values[$value1['bbxm']]    = $zhi;
                }
                $cwbbs2[$key]['value']          = $values;
            }
        }
        $info['cwbbs2'] = $cwbbs2;
        
        
        //现金流量表
        $cwbbs3 = db("cwbbs")->where(['neeq' => $info['neeq'] , 'type' => 2])->order("reportdate desc")->select();
        if(!empty($cwbbs3)){
            foreach ($cwbbs3 as $key => $value) {
                if(substr($value['reportdate'], 5, 2) == '12'){
                    $sign = '年';
                } else {
                    $sign = '中';
                }
                $cwbbs3[$key]['reportdate'] = substr($value['reportdate'], 0, 4).$sign.'报';
                $values = [];
                $cwbbs_info = db("cwbbsjs")->where(['neeq' => $info['neeq'] , 'type' => 2 , 'bbrq' => $value['reportdate']])->select();
                foreach ($cwbbs_info as $key1 => $value1) {
                    $xmsz       = round(trim($value1['xmsz'], '-'), 2);
                    if($xmsz > 100000000){
                        $zhi    = round($value1['xmsz']/100000000 , 2).'亿';
                    }elseif ($xmsz > 10000) {
                        $zhi    = round($value1['xmsz']/10000 , 2).'万';
                    } else {
                        $zhi    = round($value1['xmsz'] , 2).'元';
                    }
                    $values[$value1['bbxm']] = $zhi;
                }
                $cwbbs3[$key]['value']       = $values;
            }
        }
        $info['cwbbs3'] = $cwbbs3;
        return $info;
    }
    
    
	public function getDataAndBi($arr , $arr1){
        $a = [];$b = [];
        if(count($arr) <= count($arr1)){
            foreach ($arr as $key => $value) {
                foreach ($arr1 as $key1 => $value1) {
                    if($value['bbrq'] == $value1['bbrq']){
                        array_push($a, $value);
                        array_push($b, $value1);
                    }
                }
            }
        } else {
            foreach ($arr1 as $key1 => $value1) {
                foreach ($arr as $key => $value) {
                    if($value['bbrq'] == $value1['bbrq']){
                        array_push($a, $value);
                        array_push($b, $value1);
                    }
                }
            }
        }
        return [$a , $b];
    }
    
    
    
	public function getProjectLimitOne()
    {
        $re = $this->order('pro_id desc')->limit(1)->select();
        if(!$re){
            return false;
        }
        return $re;
    }
    /**
     * FA財4.0
     */
    //获取首页精选项目前几条
    public function getProjectLimitListApi($role_type = 0,$uid = 0,$length = 3)
    {   
        $result_info = [];

        if(!$uid){

           $result_info['code'] = 208;
           $result_info['msg'] = '缺少用户id参数';
           $result_info['data'] = '';
           return $result_info;
        }
        
        if(!is_numeric($length)){

            $result_info['code'] = 207;
            $result_info['msg'] = '数据展示条数参数不正确';
            $result_info['data'] = '';
            return $result_info;
        }

        if ($role_type == 1){
           //资金方
           $dl = model('DictLabel');
           $dl = $dl->getDictLabelApi($uid);
           if(empty($dl)){
              $data = $this->alias('p')
                       ->join('Dict d','p.inc_top_sign=d.id')
                       ->join('Dict d2','p.inc_sign=d2.id')
                       ->join('Dict d3','p.inc_top_industry=d3.id')
                       ->join('MaterialLibrary ml','p.top_img=ml.ml_id')
                       ->field([
                        'p.pro_id'=>'id',
                        'ml.url',
                        'p.pro_name'=>'name',
                        'p.company_name'=>'top',
                        'concat(d.value,",",d2.value,",",d3.value)'=>'label',
                        'p.introduction'=>'bottom',
                        'p.view_num',
                        'p.type',
                        ])
                       ->where(['p.status' => 2 , 'p.flag' => 1])
                       ->order("p.list_order desc,p.pro_id desc")
                       ->limit($length)
                       ->select();
                if(empty($data)){

                    $result_info['code'] = 201;
                    $result_info['msg'] = '精选项目不存在,请先登录后台添加';
                    $result_info['data'] = '';
                    return $result_info;
                }
               //添加类型标签
               foreach ($data as &$v) {
                   $v['type_label'] = '精选项目';
                   $v['bottom'] = '简介:'.$v['bottom'];
                   $v['label'] = explode(',', $v['label']);
                   $v['url'] = config('view_replace_str')['__DOMAIN__'].$v['url'];
               }
               // return $this->result($data);
               $result_info['code'] = 200;
               $result_info['msg'] = '';
               $result_info['data'] = $data; 
               return $result_info;
           }
            $dt = model('DictType');
            $service_type_all = [];//当前用户选择的业务类型所有一二级标签
            $service_type_son_par = [];//当前用户选中的业务类型的二级标签所属的一级标签
            $service_type_son = [];//当前用户选择的业务类型所有的2级标签(用来匹配的)
            
            $industry_type_all = [];//当前用户选择的行业的所有一二级标签
            $industry_type_son_par = [];//当前用户选中行业的二级标签所属的一级标签
            $industry_type_son = [];//当前用户选择的行业所有的2级标签(用来匹配的)

            $size = [];//当前用户选择的投融资规模(用来匹配的)
            $to_province = [];//当前用户选择的所在省份(用来匹配的)
            foreach ($dl as $k => $v) {
               $sign = $dt->field('sign')->where(['dt_id'=>$v['dict_type_id']])->find();
               //业务类型下的标签
               if($sign['sign'] == 'service_type'){
                      $service_type_all[] = $v['dict_id'];
                    if($v['dict_fid'] > 0){
                      $service_type_son[] = $v['dict_id'];
                      $service_type_son_par[] = $v['dict_fid'];
                    }                
               }
               //所属行业下的标签
               if($sign['sign'] == 'industry'){
                   // $industry[] = $v['dict_id'];
                   $industry_type_all[] = $v['dict_id'];
                    if($v['dict_fid'] > 0){
                      $industry_type_son[] = $v['dict_id'];
                      $industry_type_son_par[] = $v['dict_fid'];
                    }    
               }
               //投融资规模
               if($sign['sign'] == 'size'){
                   $size[] = $v['dict_id'];
               }
               //所在省份
               if($sign['sign'] == 'to_province'){
                   $to_province[] = $v['dict_id'];
               }
            }

            //得到当前用户选中二级标签所属的一级标签和未选中二级标签的一级标签(业务类型)
            $service_type_all_par = array_diff($service_type_all, $service_type_son);
            //得到当前用户未选中二级标签的一级标签(用来匹配的条件)(业务类型)
            $service_type_null_par = array_diff($service_type_all_par,$service_type_son_par);
            
            //得到当前用户选中二级标签所属的一级标签和未选中二级标签的一级标签(所属行业)
            $industry_type_all_par = array_diff($industry_type_all, $industry_type_son);
            //得到当前用户未选中二级标签的一级标签(用来匹配的条件)(所属行业)
            $industry_type_null_par = array_diff($industry_type_all_par,$industry_type_son_par);

            //开始匹配project表融资中且展示在前台的精选项目
            $match_str = '';
            $match_pro = $this->alias('p')
                       ->join('Dict d','p.inc_top_sign=d.id')
                       ->join('Dict d2','p.inc_sign=d2.id')
                       ->join('Dict d3','p.inc_top_industry=d3.id')
                       ->join('MaterialLibrary ml','p.top_img=ml.ml_id')
                       ->field([
                        'p.pro_id'=>'id',
                        'ml.url',
                        'p.pro_name'=>'name',
                        'p.company_name'=>'top',
                        'concat(d.value,",",d2.value,",",d3.value)'=>'label',
                        'p.introduction'=>'bottom',
                        'p.view_num',
                        'p.type',
                        ])
                       ->where(['p.status' => 2 , 'p.flag' => 1]);
            
            //匹配业务类型二级标签
            if(!empty($service_type_son)){
                $match_str .= 'p.inc_sign in ('.implode(',', $service_type_son).') or ';
            }
            //匹配业务类型未选中二级的一级标签
            if(!empty($service_type_null_par)){
                $match_str .= 'p.inc_top_sign in ('.implode(',', $service_type_null_par).') or ';
            }

            //匹配所属行业二级标签
            if(!empty($industry_type_son)){
                $match_str .= 'p.inc_industry in ('.implode(',', $industry_type_son).') or ';
            }
            
            //匹配所属行业未选中二级的一级标签
            if(!empty($industry_type_null_par)){
                $match_str .= 'p.inc_top_industry in ('.implode(',', $industry_type_null_par).') or ';
            }
            
            //匹配所在省份
            if(!empty($to_province)){
                $match_str .= 'p.inc_area in ('.implode(',', $to_province).') or ';
            } 
            //匹配投融资规模(根据选中的标签id匹配每一条的数值(万元))
            //标签描述:500万以下          不等式: <500
            //        500-1999万                >=500 && <=1999  
            //        2000-3999万               >=2000 && <=3999
            //        4000-6999万               >=4000 && <=6999
            //        7000-9999万               >=7000 && <=9999
            //        1亿以上                    >=10000
            $model = model('Dict');
            if(!empty($size)){
                   foreach ($size as $va) {
                       $value = $model->field('value')->where(['id'=>$va])->find();
                       if ($value['value'] == '500万以下') {
                           $match_str .= 'p.financing_amount<500 or '; 
                       }
                       if ($value['value'] == '500-1999万') {
                           $match_str .= '(p.financing_amount>=500 && p.financing_amount<=1999) or ';
                       }
                       if ($value['value'] == '2000-3999万') {
                           $match_str .= '(p.financing_amount>=2000 && p.financing_amount<=3999) or ';
                       }
                       if ($value['value'] == '4000-6999万') {
                           $match_str .= '(p.financing_amount>=4000 && p.financing_amount<=6999) or ';
                       }
                       if ($value['value'] == '7000-9999万') {
                           $match_str .= '(p.financing_amount>=7000 && p.financing_amount<=9999) or ';
                       }
                       if ($value['value'] == '1亿以上 ') {
                           $match_str .= 'p.financing_amount>=10000 or';
                       }
                   }
            }
            
            $match_str =rtrim($match_str,' or ');
            $list = $match_pro->where($match_str)
                              ->order("p.list_order desc,p.pro_id desc")
                              ->limit($length)
                              ->select()
                              ->toArray();
            if(empty($list)){

                    $result_info['code'] = 201;
                    $result_info['msg'] = '未匹配到符合条件的精选项目';
                    $result_info['data'] = '';
                    return $result_info;
            }
                
            //添加类型标签
           foreach ($list as &$v) {
               $v['type_label'] = '精选项目';
               $v['bottom'] = '简介:'.$v['bottom'];
               $v['label'] = explode(',', $v['label']);
               $v['url'] = config('view_replace_str')['__DOMAIN__'].$v['url'];
           }

            $result_info['code'] = 200;
            $result_info['msg'] = '';
            $result_info['data'] = $list; 
            return $result_info;      
        } else {
            //游客及其他未知情况
           $list = $this->alias('p')
                       ->join('Dict d','p.inc_top_sign=d.id')
                       ->join('Dict d2','p.inc_sign=d2.id')
                       ->join('Dict d3','p.inc_top_industry=d3.id')
                       ->join('MaterialLibrary ml','p.top_img=ml.ml_id')
                       ->field([
                        'p.pro_id'=>'id',
                        'ml.url',
                        'p.pro_name'=>'name',
                        'p.company_name'=>'top',
                        'concat(d.value,",",d2.value,",",d3.value)'=>'label',
                        'p.introduction'=>'bottom',
                        'p.view_num',
                        'p.type',
                        ])
                       ->where(['p.status' => 2 , 'p.flag' => 1])
                       ->order("p.list_order desc,p.pro_id desc")
                       ->limit($length)
                       ->select();
            
            if(empty($list)){
               //  $this->code = 201;
               //  $this->msg = '精选项目不存在,请先登录后台添加';
               // return  $this->result('',$this->code,$this->msg);
                $result_info['code'] = 201;
                $result_info['msg'] = '精选项目不存在,请先登录后台添加';
                $result_info['data'] = ''; 
                return $result_info;
            }
            //添加类型标签
            foreach ($list as &$v) {
               $v['type_label'] = '精选项目';
               $v['bottom'] = '简介:'.$v['bottom'];
               $v['label'] = explode(',', $v['label']);
               $v['url'] = config('view_replace_str')['__DOMAIN__'].$v['url'];
           }
          // dump($list);die;
           $result_info['code'] = 200;
           $result_info['msg'] = '';
           $result_info['data'] = $list; 
           return $result_info;
        }
    }

    //查看精选项目详情
    public function getProjectDetail($id=0)
    {
       if (empty($id)) {
           $this->code = 255;
           $this->msg = '缺少项目或资金的id';
           return $this->result('',$this->code,$this->msg);
       }

       $detail = $this->alias('p')
            ->join('Dict d','p.inc_top_sign=d.id')
            ->join('Dict d2','p.inc_sign=d2.id')
            ->join('Dict d3','p.inc_top_industry=d3.id')
            ->field([
              'p.pro_id',
              'p.roadshow_url',
              'p.top_img',
              'p.pro_name',
              'p.stock_code',
              'p.hierarchy',
              'p.transfer_type',
              'd.value'=>'par_business',
              'd2.value'=>'son_business',
              'd3.value'=>'par_industry',
              'p.introduction',
              'p.company_url',
              'p.enterprise_url',
              'p.product_url',
              'p.analysis_url',
              'p.business_plan',
              'p.factor_table',
              'p.company_video'
            ])
            ->where(['p.status'=>2,'p.flag'=>1])
            ->find()
            ->toArray();
        if (empty($detail)) {
            $this->code = 256;
            $this->msg = '详情不存在';
            return $this->result('',$this->code,$this->msg);
        }
        
        //(1)投资亮点
        //投资亮点-公司简介
        if ($detail['company_url']) {
            $map['ml_id'] = array('in',$detail['company_url']);
            $company_url_result = model('MaterialLibrary')->field('url')->where($map)->select()->toArray();
            if (!empty($company_url_result)) {
                $company_url_arr = [];
                foreach ($company_url_result as  &$value) {
                    $company_url_arr[] = $value['url'];
                }
                $detail['company_url'] = $company_url_arr; 
            } else {
                $this->code = 264;
                $this->msg = '公司简介图片地址不存在';
                return $this->result('',$this->code,$this->msg);                
            }
            
        }
        
        //投资亮点-企业亮点
         if ($detail['enterprise_url']) {
            $map2['ml_id'] = array('in',$detail['enterprise_url']);
            $enterprise_url_result = model('MaterialLibrary')->field('url')->where($map2)->select()->toArray();
            if (!empty($enterprise_url_result)) {
                $enterprise_url_arr = [];
                foreach ($enterprise_url_result as  &$value) {
                    $enterprise_url_arr[] = $value['url'];
                }
                $detail['enterprise_url'] = $enterprise_url_arr; 
            } else {
                $this->code = 265;
                $this->msg = '企业亮点图片地址不存在';
                return $this->result('',$this->code,$this->msg);                
            }
            
        }
        //投资亮点-核心产品
        if ($detail['product_url']) {
            $map3['ml_id'] = array('in',$detail['product_url']);
            $product_url_result = model('MaterialLibrary')->field('url')->where($map3)->select()->toArray();
            if (!empty($product_url_result)) {
                $product_url_arr = [];
                foreach ($product_url_result as  &$value) {
                    $product_url_arr[] = $value['url'];
                }
                $detail['product_url'] = $product_url_arr; 
            } else {
                $this->code = 266;
                $this->msg = '核心产品图片地址不存在';
                return $this->result('',$this->code,$this->msg);                
            }
            
        }
        
        //投资亮点-行业分析
        if ($detail['analysis_url']) {
            $map4['ml_id'] = array('in',$detail['analysis_url']);
            $analysis_url_result = model('MaterialLibrary')->field('url')->where($map4)->select()->toArray();
            if (!empty($analysis_url_result)) {
                $analysis_url_arr = [];
                foreach ($analysis_url_result as  &$value) {
                    $analysis_url_arr[] = $value['url'];
                }
                $detail['analysis_url'] = $analysis_url_arr; 
            } else {
                $this->code = 267;
                $this->msg = '行业分析图片地址不存在';
                return $this->result('',$this->code,$this->msg);                
            }
            
        }

        //(2)公司概况-基本信息&&公司简介

        $gszls = db('Gszls')->field('gsqc,gswz,zcdz,zyyw,xyfl,frdb,gsdm,gsdh,gscz,zgbwg,gprq,tjgpqs,gsjj')
                            ->where(['neeq'=> $detail['stock_code']])
                            ->find();
        if (empty($gszls)) {
            $this->code = 257;
            $this->msg = '公司概况不存在';
            return $this->result('',$this->code,$this->msg);
        }
        $detail['company_profile'] = $gszls;

        //(3)财务指标
        
        //净利润-净利润树状图&&报告期,净利润,同比上涨(年报)
        $lirun = db("cwbbsjs")->where(['neeq' => $detail['stock_code'] , 'type' => 1 , 'bbxm' => 'NETPROFIT'])
                              ->field("bbrq,xmsz")
                              ->order("bbrq asc")
                              ->select();
        $lirun_bi = db("cwbbsjs")->where(['neeq' => $detail['stock_code'] , 'type' => 3 , 'bbxm' => 'PROFIT_YOY'])
                                 ->field("bbrq,xmsz")
                                 ->order("bbrq asc")
                                 ->select();
        if(!empty($lirun) && !empty($lirun_bi)){
            foreach ($lirun as $key => $value) {
                foreach ($lirun_bi as $key1 => $value1) {
                    if($value1['bbrq'] == $value['bbrq']){
                        $lirun[$key]['lirun_bi'] = $value1['xmsz'];
                    }
                }
            }
        } else {
            foreach ($lirun as $key => $value) {
                $lirun[$key]['lirun_bi'] = '-';
            }
        }
        
        $net_profit_final = [];
        $net_profit_middle = [];
        foreach ($lirun as $key=>$value) {
            if (substr($value['bbrq'],5,2) !== '12') {
                
               $net_profit_middle[] = $value;
            }else{
               $net_profit_final[] = $value; 
            }
        }
        $detail['net_profit_middle'] = $net_profit_middle;
        $detail['net_profit_final'] = $net_profit_final;

        //营业收入-营业收入树状图&&报告期,净利润,同比上涨(年报)
        $shouru = db("cwbbsjs")->where(['neeq' => $detail['stock_code'] , 'type' => 1 , 'bbxm' => 'OPERATEREVE'])->field("bbrq,xmsz")->order("bbrq asc")->select();
        $shouru_bi = db("cwbbsjs")->where(['neeq' => $detail['stock_code'] , 'type' => 3 , 'bbxm' => 'revenue_YOY'])->field("bbrq,xmsz")->order("bbrq asc")->select();
        if(!empty($shouru) && !empty($shouru_bi)){
            foreach ($shouru as $key => $value) {
                foreach ($shouru_bi as $key1 => $value1) {
                    if($value1['bbrq'] == $value['bbrq']){
                        $shouru[$key]['shouru_bi'] = $value1['xmsz'];
                    }
                }
            }
        } else {
            foreach ($shouru as $key => $value) {
                $shouru[$key]['shouru_bi'] = '-';
            }
        }
        
        $shouru_final = [];
        $shouru_middle = [];
        foreach ($shouru as $key=>$value) {
            if (substr($value['bbrq'],5,2) !== '12') {
                
               $shouru_middle[] = $value;
            }else{
               $shouru_final[] = $value; 
            }
        }
        $detail['shouru_middle'] = $shouru_middle;
        $detail['shouru_final'] = $shouru_final;

        //每股净资产-每股净资产树状图&&报告期,净利润,同比上涨(年报)
        $zichan = db("cwbbsjs")->where(['neeq' => $detail['stock_code'] , 'type' => 3 , 'bbxm' => 'BPS'])->field("bbrq,xmsz")->order("bbrq asc")->select();
        $zichan_bi = [];
        foreach ($zichan as $key => $value) {
            if($key == 0){
                array_push($zichan_bi, ['bbrq' => $value['bbrq'] , 'xmsz' => 0]);
                $zichan[$key]['zichan_bi'] = 0;
            } else {
                if(floatval($zichan[$key-1]['xmsz'] == 0)){
                    $val = 0;
                } else {
                    $val = (floatval($value['xmsz'])-floatval($zichan[$key-1]['xmsz']))/floatval($zichan[$key-1]['xmsz']);
                    $val = round(floatval($val), 2);
                }
                array_push($zichan_bi, ['bbrq' => $value['bbrq'] , 'xmsz' => $val]);
                $zichan[$key]['zichan_bi'] = $val;
            }
        }
        
        $zichan_final = [];
        $zichan_middle = [];
        foreach ($zichan as $key=>$value) {
            if (substr($value['bbrq'],5,2) !== '12') {
                
               $zichan_middle[] = $value;
            }else{
               $zichan_final[] = $value; 
            }
        }

        $detail['zichan_middle'] = $zichan_middle;
        $detail['zichan_final'] = $zichan_final;

        //每股现金流-每股现金流树状图&&报告期,净利润,同比上涨(年报)
        $xianjinliu = db("cwbbsjs")->where(['neeq' => $detail['stock_code'] , 'type' => 3 , 'bbxm' => 'CFPS'])->field("bbrq,xmsz")->order("bbrq asc")->select();
        $xianjinliu_bi = [];
        foreach ($xianjinliu as $key => $value) {
            if($key == 0){
                array_push($xianjinliu_bi, ['bbrq' => $value['bbrq'] , 'xmsz' => 0]);
                $xianjinliu[$key]['xianjinliu_bi'] = 0;
            } else {
                if(floatval($xianjinliu[$key-1]['xmsz'] == 0)){
                    $val = 0;
                } else {
                    $val = (floatval($value['xmsz'])-floatval($xianjinliu[$key-1]['xmsz']))/floatval($xianjinliu[$key-1]['xmsz']);
                    $val = round(floatval($val), 2);
                }
                array_push($xianjinliu_bi, ['bbrq' => $value['bbrq'] , 'xmsz' => $val]);
                $xianjinliu[$key]['xianjinliu_bi'] = $val;
            }
        }
        
        $xianjinliu_final = [];
        $xianjinliu_middle = [];
        foreach ($xianjinliu as $key=>$value) {
            if (substr($value['bbrq'],5,2) !== '12') {
                
               $xianjinliu_middle[] = $value;
            }else{
               $xianjinliu_final[] = $value; 
            }
        }

        $detail['xianjinliu_middle'] = $xianjinliu_middle;
        $detail['xianjinliu_final'] = $xianjinliu_final;

        //毛利率树状图
        $maolilv = db("cwbbsjs")->where(['neeq' => $detail['stock_code'] , 'type' => 3 , 'bbxm' => 'GROSSPROFITMARGIN'])->field("bbrq,xmsz")->order("bbrq asc")->select();
        $maolilv_bi = [];
        foreach ($maolilv as $key => $value) {
            if($key == 0){
                array_push($maolilv_bi, ['bbrq' => $value['bbrq'] , 'xmsz' => 0]);
                $maolilv[$key]['maolilv_bi'] = 0;
            } else {
                if(floatval($maolilv[$key-1]['xmsz'] == 0)){
                    $val = 0;
                } else {
                    $val = (floatval($value['xmsz'])-floatval($maolilv[$key-1]['xmsz']))/floatval($maolilv[$key-1]['xmsz']);
                    $val = round(floatval($val), 2);
                }
                array_push($maolilv_bi, ['bbrq' => $value['bbrq'] , 'xmsz' => $val]);
                $maolilv[$key]['maolilv_bi'] = $val;
            }
        }
        
        $maolilv_final = [];
        $maolilv_middle = [];
        foreach ($maolilv as $key=>$value) {
            if (substr($value['bbrq'],5,2) !== '12') {
                
               $maolilv_middle[] = $value;
            }else{
               $maolilv_final[] = $value; 
            }
        }

        $detail['maolilv_middle'] = $maolilv_middle;
        $detail['maolilv_final'] = $maolilv_final;        
        

        //每股收益树状图
        $shouyi = db("cwbbsjs")->where(['neeq' => $detail['stock_code'] , 'type' => 1 , 'bbxm' => 'BASICEPS'])->field("bbrq,xmsz")->order("bbrq asc")->select();
        $shouyi_bi = [];
        foreach ($shouyi as $key => $value) {
            if($key == 0){
                array_push($shouyi_bi, ['bbrq' => $value['bbrq'] , 'xmsz' => 0]);
                $shouyi[$key]['shouyi_bi'] = 0;
            } else {
                if(floatval($shouyi[$key-1]['xmsz'] == 0)){
                    $val = 0;
                } else {
                    $val = (floatval($value['xmsz'])-floatval($shouyi[$key-1]['xmsz']))/floatval($shouyi[$key-1]['xmsz']);
                    $val = round(floatval($val), 2);
                }
                array_push($shouyi_bi, ['bbrq' => $value['bbrq'] , 'xmsz' => $val]);
                $shouyi[$key]['shouyi_bi'] = $val;
            }
        }
        
        $shouyi_final = [];
        $shouyi_middle = [];
        foreach ($shouyi as $key=>$value) {
            if (substr($value['bbrq'],5,2) !== '12') {
                
               $shouyi_middle[] = $value;
            }else{
               $shouyi_final[] = $value; 
            }
        }

        $detail['shouyi_middle'] = $shouyi_middle;
        $detail['shouyi_final'] = $shouyi_final;


        //每股公积金树状图
        $gongjijin = db("cwbbsjs")->where(['neeq' => $detail['stock_code'] , 'type' => 3 , 'bbxm' => 'SURPLUSCAPITALPS'])->field("bbrq,xmsz")->order("bbrq asc")->select();
        $gongjijin_bi = [];
        foreach ($gongjijin as $key => $value) {
            if($key == 0){
                array_push($gongjijin_bi, ['bbrq' => $value['bbrq'] , 'xmsz' => 0]);
                $gongjijin[$key]['gongjijin_bi'] = 0;
            } else {
                if(floatval($gongjijin[$key-1]['xmsz'] == 0)){
                    $val = 0;
                } else {
                    $val = (floatval($value['xmsz'])-floatval($gongjijin[$key-1]['xmsz']))/floatval($gongjijin[$key-1]['xmsz']);
                    $val = round(floatval($val), 2);
                }
                array_push($gongjijin_bi, ['bbrq' => $value['bbrq'] , 'xmsz' => $val]);
                $gongjijin[$key]['gongjijin_bi'] = $val;
            }
        }
        
        $gongjijin_final = [];
        $gongjijin_middle = [];
        foreach ($gongjijin as $key=>$value) {
            if (substr($value['bbrq'],5,2) !== '12') {
                
               $gongjijin_middle[] = $value;
            }else{
               $gongjijin_final[] = $value; 
            }
        }

        $detail['gongjijin_middle'] = $gongjijin_middle;
        $detail['gongjijin_final'] = $gongjijin_final;

        
        //每股未分配树状图
        $weifenpei = db("cwbbsjs")->where(['neeq' => $detail['stock_code'] , 'type' => 3 , 'bbxm' => 'UNDISTRIBUTEDPS'])->field("bbrq,xmsz")->order("bbrq asc")->select();
        $weifenpei_bi = [];
        foreach ($weifenpei as $key => $value) {
            if($key == 0){
                array_push($weifenpei_bi, ['bbrq' => $value['bbrq'] , 'xmsz' => 0]);
                $weifenpei[$key]['weifenpei_bi'] = 0;
            } else {
                if(floatval($weifenpei[$key-1]['xmsz'] == 0)){
                    $val = 0;
                } else {
                    $val = (floatval($value['xmsz'])-floatval($weifenpei[$key-1]['xmsz']))/floatval($weifenpei[$key-1]['xmsz']);
                    $val = round(floatval($val), 2);
                }
                array_push($weifenpei_bi, ['bbrq' => $value['bbrq'] , 'xmsz' => $val]);
                $weifenpei[$key]['weifenpei_bi'] = $val;
            }
        }
        
        $weifenpei_final = [];
        $weifenpei_middle = [];
        foreach ($weifenpei as $key=>$value) {
            if (substr($value['bbrq'],5,2) !== '12') {
                
               $weifenpei_middle[] = $value;
            }else{
               $weifenpei_final[] = $value; 
            }
        }

        $detail['weifenpei_middle'] = $weifenpei_middle;
        $detail['weifenpei_final'] = $weifenpei_final;
        
        //利润表
        $cwbbs1 = db("cwbbs")->field('reportdate')
                             ->where(['neeq' => $detail['stock_code'] , 'type' => 1])
                             ->where("reportdate like '%-06-30%'")
                             ->order("reportdate desc")
                             ->limit(1)
                             ->select();

        if(!empty($cwbbs1)){
            foreach ($cwbbs1 as $key => $value) {

                $cwbbs1[$key]['reportdate'] = substr($value['reportdate'], 0, 4).'中报';
                $values = [];
                $cwbbs_info = db("cwbbsjs")->where(['neeq' => $detail['stock_code'], 'type' => 1 , 'bbrq' => $value['reportdate']])->select();
                foreach ($cwbbs_info as $key1 => $value1) {
                    $xmsz = round(trim($value1['xmsz'], '-'), 2);
                    if($xmsz > 100000000){
                        $zhi = round($value1['xmsz']/100000000 , 2).'亿';
                    }elseif ($xmsz > 10000) {
                        $zhi = round($value1['xmsz']/10000 , 2).'万';
                    } else {
                        $zhi  = round($value1['xmsz'] , 2).'元';
                    }
                    if($value1['bbxm'] == 'OPERATEREVE' || $value1['bbxm'] == 'NETPROFIT' || $value1['bbxm'] == 'KCFJCXSYJLR'){
                      $values[$value1['bbxm']] = $zhi;  
                    }
                    
                }
                $cwbbs1[$key]['value'] = $values;
            }
        }
        

        $detail['profit_table'] = $cwbbs1;
        
        //资产负债表
        $cwbbs2 = db("cwbbs")->field('reportdate')
                             ->where(['neeq' => $detail['stock_code'], 'type' => 0])
                             ->where("reportdate like '%-06-30%'")
                             ->order("reportdate desc")
                             ->limit(1)
                             ->select();
                             
        if(!empty($cwbbs2)){
            foreach ($cwbbs2 as $key => $value) {
                $cwbbs2[$key]['reportdate'] = substr($value['reportdate'], 0, 4).'中报';
                $values = [];
                $cwbbs_info = db("cwbbsjs")->where(['neeq' => $detail['stock_code'] , 'type' => 0 , 'bbrq' => $value['reportdate']])->select();
                foreach ($cwbbs_info as $key1 => $value1) {
                    $xmsz = round(trim($value1['xmsz'], '-'), 2);
                    if($xmsz > 100000000){
                        $zhi = round($value1['xmsz']/100000000 , 2).'亿';
                    }elseif ($xmsz > 10000) {
                        $zhi = round($value1['xmsz']/10000 , 2).'万';
                    } else {
                        $zhi  = round($value1['xmsz'] , 2).'元';
                    }
                     if($value1['bbxm'] == 'SUMLASSET' || $value1['bbxm'] == 'SUMNONLASSET' || $value1['bbxm'] == 'SUMASSET' || $value1['bbxm'] == 'SUMLIAB' || $value1['bbxm'] == 'SHARECAPITAL' || $value1['bbxm'] == 'SUMSHEQUITY'){
                      $values[$value1['bbxm']] = $zhi;  
                    }

                }
                $cwbbs2[$key]['value'] = $values;
            }
        }
        $detail['liabilities_table'] = $cwbbs2;
        

        //现金流量表
        $cwbbs3 = db("cwbbs")->field('reportdate')
                             ->where(['neeq' => $detail['stock_code'] , 'type' => 2])
                             ->where("reportdate like '%-06-30%'")
                             ->order("reportdate desc")
                             ->limit(1)
                             ->select();
        if(!empty($cwbbs3)){
            foreach ($cwbbs3 as $key => $value) {
               
                $cwbbs3[$key]['reportdate'] = substr($value['reportdate'], 0, 4).'中报';
                $values = [];
                $cwbbs_info = db("cwbbsjs")->where(['neeq' => $detail['stock_code'], 'type' => 2 , 'bbrq' => $value['reportdate']])->select();
                foreach ($cwbbs_info as $key1 => $value1) {
                    $xmsz = round(trim($value1['xmsz'], '-'), 2);
                    if($xmsz > 100000000){
                        $zhi = round($value1['xmsz']/100000000 , 2).'亿';
                    }elseif ($xmsz > 10000) {
                        $zhi = round($value1['xmsz']/10000 , 2).'万';
                    } else {
                        $zhi  = round($value1['xmsz'] , 2).'元';
                    }

                    if($value1['bbxm'] == 'NETOPERATECASHFLOW' || $value1['bbxm'] == 'NETINVCASHFLOW' || $value1['bbxm'] == 'NETFINACASHFLOW'){
                      $values[$value1['bbxm']] = $zhi;  
                    }
                }
                $cwbbs3[$key]['value'] = $values;
            }
        }
        $detail['cash_flow_table'] = $cwbbs3;
        
        //新闻告
        $gonggaos = db("st_gonggaos")->alias("gg")
                                     ->join("StSceus s" , "gg.ggid=s.ggid")
                                     ->where("s.secu_link_code","eq",$detail['stock_code'])
                                     ->field("gg.id,s.secu_s_name,gg.title,gg.date")
                                     ->select();
        $detail['public_notice'] = $gonggaos;

        //商业计划书选项卡
        //(1)商业计划书(pdf)
        $business_plan = db('MaterialLibrary')->where(['ml_id'=>$detail['business_plan']])
                                              ->field(['name,url'])
                                              ->find();
        $detail['business_plan'] = $business_plan;
        //(2)要素表(图片)
        $factor_table = db('MaterialLibrary')->where(['ml_id'=>$detail['factor_table']])
                                              ->field(['name,url'])
                                              ->find();
        $detail['factor_table'] = $factor_table;
        //(3)企业视频
        $company_video = db('MaterialLibrary')->where(['ml_id'=>$detail['company_video']])
                                              ->field(['name,url'])
                                              ->find();
        $detail['company_video'] = $company_video;
        
        //研报
        $yanbaos = db("st_yanbaos_effect")->where("relation","like","%".$detail['stock_code']."%")
                                          ->field(['id','org','title','report_date'])
                                          ->select();
        $detail['yanbaos'] = $yanbaos;
        //投资问答
        $project_qa = db("ProjectQa")->where(['pro_id' => $detail['pro_id'] ,'status' => 1 , 'hide' => 0])
                                     ->field(['question,answer'])
                                     ->order('hot desc,create_time desc')
                                     ->select();
        $detail['project_qa'] = $project_qa;
        //层级转换
        $hierarchy = db('Dict')->field('value')
                               ->where(['id'=>$detail['hierarchy']])
                               ->find();
        if (!empty($hierarchy)) {
            $detail['hierarchy'] = $hierarchy['value'];
        } else {
            $detail['hierarchy'] = '';
        }

        $transfer_type = db('Dict')->field('value')
                               ->where(['id'=>$detail['transfer_type']])
                               ->find();
        if (!empty($transfer_type)) {
            $detail['transfer_type'] = $transfer_type['value'];
        } else {
            $detail['transfer_type'] = '';
        }
        //处理头图
        $top_img = db('MaterialLibrary')->field('url')
                               ->where(['ml_id'=>$detail['top_img']])
                               ->find();
        if (!empty($top_img)) {
            $detail['top_img'] = $top_img['url'];
        } else {
            $detail['top_img'] = '';
        }
        
        return $this->result($detail);  
    } 
    
    //个人中心-我的收藏-项目列表
    public function showMyCollectProjectApi($uid = 0,$page = 1,$length = 5)
    {
        
       if (empty($uid)) {
          $this->code = 208;
          $this->msg  = '缺少当前访问用户参数';
          return $this->result('',$this->code,$this->msg);
        }

        $offset = (intval($page) - 1) * intval($length);
       
        //查询当期访问用户收藏的平台精选项目/发布项目id
        $p_id = model('Member')->field('collection,collection_project_require')
                               ->where(['uid'=>$uid])
                               ->find();         
        
        //用户未收藏发布项目且未收藏平台精选项目/定增项目/大宗项目/发布项目id
        if (!$p_id['collection_project_require'] && !$p_id['collection']) {

            $this->code = 262;
            $this->msg = '未收藏任何类型的项目';
            return $this->result('',$this->code,$this->msg);
        }
        

        //组装当前访问用户收藏的发布项目id条件      
        if (!$p_id['collection_project_require']) {
            $map['pr.pr_id'] = array('in',"");
        } else {
            $map['pr.pr_id'] = array('in',$p_id['collection_project_require']);
        }
        
        //组装当前访问用户收藏的平台精选项目id条件
        if (!$p_id['collection']) {
            $map2['p.pro_id'] = array('in',"");
        } else {
            $map2['p.pro_id'] = array('in',$p_id['collection']);
        }

        //创建查询当前访问用户收藏的发布项目的sql语句
        $collect_pr_list = model('ProjectRequire')->alias('pr')
                                 ->join('Member m','pr.uid=m.uid')
                                 ->join('Through td','pr.uid=td.uid')
                                 ->join('dict d','pr.top_dict_id=d.id')
                                 ->join('dict d2','pr.dict_id=d2.id')
                                 ->join('dict d3','pr.top_industry_id=d3.id')
                                 ->field('pr.pr_id as id,td.position as position,td.company_jc as short_com, 2 as p_type,m.userPhoto as img,m.realName as name,pr.name as top_name,d.value as par_business,d2.value as son_business,d3.value as par_industry,pr.business_des as des,pr.type,pr.view_num')
                                 ->where($map)
                                 ->where(['pr.is_show'=>1,'pr.status'=>1])
                                 ->buildSql();


        //执行合并查询当前访问用户收藏的平台精选项目&&用户发布项目     
        $collect_p_list = $this->alias('p')
                               ->join('MaterialLibrary ml','p.top_img=ml.ml_id')
                               ->join('Dict d','p.inc_top_sign=d.id')
                               ->join('Dict d2','p.inc_sign=d2.id')
                               ->join('Dict d3','p.inc_top_industry=d3.id')
                               ->field('p.pro_id as id,11 as position,222 as short_com,1 as p_type,ml.url_compress as img,p.pro_name as name,  p.company_name as top_name,d.value as par_business,d2.value as son_business,d3.value as par_industry,p.introduction as des, p.type,p.view_num')
                               ->where($map2)
                               ->where(['p.status'=>2,'p.flag'=>1])
                               ->union($collect_pr_list,true)
                               ->select(false);
        //返回排序且分页后的合并的所有的收藏的精选/用户发布项目
        $collect_sql = "($collect_p_list)";
        $collect_all_project = Db::table($collect_sql.' as a')->field('a.id,a.position,a.short_com,a.p_type,a.img,a.name,a.top_name,a.par_business,a.son_business,a.par_industry,a.des,a.type,a.view_num') 
                      ->order('a.id desc')
                      ->limit($offset, $length)
                      ->select(); 
        
        if (empty($collect_all_project)) {
            $this->code = 201;
            $this->msg = '数据不存在';
            return $this->result('',$this->code,$this->msg);
        }else{
            
            foreach ($collect_all_project as &$v) {
                if($v['type']=="project"){
                    $v['type_label'] = '精选项目';
                    $v['p_from']     = '来源：FA財';
                    $v['img']        = config('view_replace_str')['__DOMAIN__'].$v['img'];
                }else{
                    $v['type_label'] = '项目';
                    $v['p_from']     = $v['short_com']."|".$v['position'];
                }
                $v['bottom']     = $v['des'];
                $v['label']      = explode(',', $v['par_business'].",".$v['son_business'].",".$v['par_industry']);
            }
            
            $this->code = 200;
            $this->msg  = '';
            $this->data = $collect_all_project;
            
            
            return $this->result($this->data,$this->code,$this->msg);
        }
    }
    //报表详情(利润表,资产负债表,现金流量表)
    //id=1(年报),id=2(中报),id=3(最新)
    //type=1(利润表),type=0(资产负债表),type=2(现金流量表)
    //cwbbs,cwbbxms,cwbbsjs
    public function getCompletebaoApi($neeq,$id=3,$type=1){
        //确定报表的type
        $cwbbs1 = db("cwbbs")->field('reportdate')
                             ->where(['neeq' => $neeq , 'type' => $type])
                             ->order("reportdate desc")
                             ->select();
        $arr = array();
        if(!empty($cwbbs1)){
                if($id == 1){
                    //年报
                    $values = [];
                    foreach ($cwbbs1 as $key => $value) {
                        if(substr($value['reportdate'], 5, 2) == '12'){
                            $sign = '年';
                            $cwbbs1[$key]['reportdate'] = substr($value['reportdate'], 0, 4).$sign.'报';
                            $num = [];
                            $cwbbxms=db("Cwbbxms")->field('code,name')
                                                  ->where(['neeq' => $neeq , 'type' => $type])
                                                  ->select();
                            
                            foreach($cwbbxms as $key1 => $value1){
                                $cwbbs_info1 = db("cwbbsjs")->field('xmsz')
                                                            ->where(['neeq' => $neeq , 'type' => $type , 'bbrq' => $value['reportdate'],'bbxm' => $value1['code']])
                                                            ->find();
                                if(preg_match('/^[0-9-]+$/',$cwbbs_info1['xmsz'])){
                                    //说明这是日期格式
                                    $zhi = $cwbbs_info1['xmsz'];
                                }else if(preg_match('/^[1-9]\d*\,\d*|[1-9]\d*$/',$cwbbs_info1['xmsz']) || preg_match('/^[1-9]\d*\.\d*|[1-9]\d*$/',$cwbbs_info1['xmsz'])){
                                    $xmsz = floatval($cwbbs_info1['xmsz']);
                                    if($xmsz > 100000000 || $xmsz < -100000000){
                                        $zhi = round($xmsz/100000000 , 2).'亿';
                                    }elseif ($xmsz > 10000 || $xmsz < -10000) {
                                        $zhi = round($xmsz/10000 , 2).'万';
                                    } else {
                                        $zhi = round($xmsz , 2).'元';
                                    }
                                }else{
                                    //说明这是日期格式
                                    $zhi = $cwbbs_info1['xmsz'];
                                }
                                array_push($num,$zhi);
                                unset($num[0]);
                                unset($cwbbxms[0]);
                            }
                            
                            array_push($values,array("key"=>$cwbbs1[$key]['reportdate'],"value"=>$num));
                        }
                    }
                }else if($id == 2){
                    $values = [];
                    foreach ($cwbbs1 as $key => $value) {
                        if(substr($value['reportdate'], 5, 2) != '12'){
                            $sign = '中';
                            $cwbbs1[$key]['reportdate'] = substr($value['reportdate'], 0, 4).$sign.'报';
                            
                            $num = [];
                            $cwbbxms=db("Cwbbxms")->field('code,name')
                                                  ->where(['neeq' => $neeq , 'type' => $type])
                                                  ->select();
                            foreach($cwbbxms as $key1 => $value1){
                                $cwbbs_info1 = db("cwbbsjs")->field('xmsz')
                                                            ->where(['neeq' => $neeq , 'type' => $type , 'bbrq' => $value['reportdate'],'bbxm' => $value1['code']])
                                                            ->find();
                                if(preg_match('/^[0-9-]+$/',$cwbbs_info1['xmsz'])){
                                    //说明这是日期格式
                                    $zhi = $cwbbs_info1['xmsz'];
                                }else if(preg_match('/^[1-9]\d*\,\d*|[1-9]\d*$/',$cwbbs_info1['xmsz']) || preg_match('/^[1-9]\d*\.\d*|[1-9]\d*$/',$cwbbs_info1['xmsz'])){
                                    $xmsz = floatval($cwbbs_info1['xmsz']);
                                    if($xmsz > 100000000 || $xmsz < -100000000){
                                        $zhi = round($xmsz/100000000 , 2).'亿';
                                    }elseif ($xmsz > 10000 || $xmsz < -10000) {
                                        $zhi = round($xmsz/10000 , 2).'万';
                                    } else {
                                        $zhi = round($xmsz , 2).'元';
                                    }
                                }else{
                                    //说明这是日期格式
                                    $zhi = $cwbbs_info1['xmsz'];
                                }
                                array_push($num,$zhi);
                                unset($num[0]);
                                unset($cwbbxms[0]);
                            }
                            
                            array_push($values,array("key"=>$cwbbs1[$key]['reportdate'],"value"=>$num));
                        }
                    }
                }else if($id == 3){
                    $values = [];
                    foreach ($cwbbs1 as $key => $value) {
                        if(substr($value['reportdate'], 5, 2) == '12'){
                            $sign = '年';
                        }else{
                            $sign = '中';
                        }
                        $cwbbs1[$key]['reportdate'] = substr($value['reportdate'], 0, 4).$sign.'报';
                        $num = [];
                       $cwbbxms=db("Cwbbxms")->field('code,name')
                                                  ->where(['neeq' => $neeq , 'type' => $type])
                                                  ->select();
                        foreach($cwbbxms as $key1 => $value1){
                            $cwbbs_info1 = db("cwbbsjs")->field('xmsz')
                                                        ->where(['neeq' => $neeq , 'type' => $type , 'bbrq' => $value['reportdate'],'bbxm' => $value1['code']])
                                                        ->find();
                            if(preg_match('/^[0-9-]+$/',$cwbbs_info1['xmsz'])){
                                //说明这是日期格式
                                $zhi = $cwbbs_info1['xmsz'];
                            }else if(preg_match('/^[1-9]\d*\,\d*|[1-9]\d*$/',$cwbbs_info1['xmsz']) || preg_match('/^[1-9]\d*\.\d*|[1-9]\d*$/',$cwbbs_info1['xmsz'])){
                                $xmsz = floatval($cwbbs_info1['xmsz']);
                                if($xmsz > 100000000 || $xmsz < -100000000){
                                    $zhi = round($xmsz/100000000 , 2).'亿';
                                }elseif ($xmsz > 10000 || $xmsz < -10000) {
                                    $zhi = round($xmsz/10000 , 2).'万';
                                } else {
                                    $zhi = round($xmsz , 2).'元';
                                }
                            }else{
                                //说明这是日期格式
                                $zhi = $cwbbs_info1['xmsz'];
                            }
                            array_push($num,$zhi);
                            unset($num[0]);
                            unset($cwbbxms[0]);

                        }
                 
                        array_push($values,array("key"=>$cwbbs1[$key]['reportdate'],"value"=>$num));
                        
                    }
                }
        }
        $info['cwbbs1'] = $cwbbxms;
        $info['data'] = $values;
        return $info;       
    }
       
}