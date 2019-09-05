<?php

namespace app\home\model;

use think\Model;

class Zfmxes extends Model
{
    public function setFinancingById($id){
        $info = db("zfmxes_add")->where(['id' => $id])->update(['zfmx_financing' => 1]);
        return $info;
    }
    public function getCollectionZfmxesList($collection){
        $collection = trim($collection, ',');
        if($collection != ''){
            $arr = explode(',', $collection);
            foreach ($arr as $key => $value) {
                $arr[$key] = explode('_', $value);
            }
            foreach ($arr as $key => $value) {
                $info = $this->getZfmxesSimpleInfo($value[0], $value[1]);
                $arr[$key] = $info;
            }
        } else {
            $arr = [];
        }
        return $arr;
    }
    public function getZfmxesSimpleInfo($neeq , $plannoticeddate){
        $info = $this->where(['neeq' => $neeq , 'plannoticeddate' => $plannoticeddate])->find();
        $add = db("zfmxes_add")->where(['neeq' => $neeq , 'plannoticeddate' => $plannoticeddate])->find();
        $erweima = db("qrcode_direction")->where(['id' => $add['qr_code']])->find();
        $add['qr_code'] = $erweima['qrcode_path'];
        $info['add'] = $add;
        return $info;
    }
    public function getZfmxesCompleteInfo($neeq , $plannoticeddate){
        $info = $this->where(['neeq' => $neeq , 'plannoticeddate' => $plannoticeddate])->find();
        $add = db("zfmxes_add")->where(['neeq' => $neeq , 'plannoticeddate' => $plannoticeddate])->find();
        $info['add'] = $add;
        $gszls = db("gszls")->where(['neeq' => $neeq])->find();
        $info['gszls'] = $gszls;
        $gdyjs_date = db("gdyjs")->where(['neeq' => $neeq])->group("jzrq desc")->field("jzrq")->select();
        if(!empty($gdyjs_date)){
            $gdyjs = db("gdyjs")->where(['neeq' => $neeq , 'jzrq' => $gdyjs_date[0]['jzrq']])->limit(10)->select();
        } else {
            $gdyjs = [];
        }
        $cgsl_num = 0;$cgbl_num = 0;
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
        $info['gdyjs'] = $gdyjs;
        $info['gdyjs_num'] = count($gdyjs);
        $info['cgsl_num'] = $cgsl_num;
        $info['cgbl_num'] = $cgbl_num;
        $ggzls = db("ggzls")->where(['neeq' => $neeq])->where("deleted_at is null")->select();
        $info['ggzls'] = $ggzls;
        $gonggaos = db("st_gonggaos")->alias("gg")
//                ->join("StGonggaosStatus ggs" , "gg.ggid=ggs.ggid" , "LEFT")
                ->join("StSceus s" , "gg.ggid=s.ggid")
                ->where("s.secu_link_code","eq",$neeq)
//                ->where("ggs.examine","eq",1)
                ->field("gg.*,s.secu_s_name")
                ->select();
        $info['gonggaos'] = $gonggaos;
        $yanbaos = db("st_yanbaos_effect")->where("relation","like","%".$neeq."%")->select();
        $info['yanbaos'] = $yanbaos;
        //利润树状图
        $lirun = db("cwbbsjs")->where(['neeq' => $neeq , 'type' => 1 , 'bbxm' => 'NETPROFIT'])->field("bbrq,xmsz")->order("bbrq asc")->select();
        $lirun_bi = db("cwbbsjs")->where(['neeq' => $neeq , 'type' => 3 , 'bbxm' => 'PROFIT_YOY'])->field("bbrq,xmsz")->order("bbrq asc")->select();
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
        $arr = $this->getDataAndBi($lirun, $lirun_bi);
        $info['lirun'] = $arr[0];
        $info['lirun_bi'] = $arr[1];
        //营业收入树状图
        $shouru = db("cwbbsjs")->where(['neeq' => $neeq , 'type' => 1 , 'bbxm' => 'OPERATEREVE'])->field("bbrq,xmsz")->order("bbrq asc")->select();
        $shouru_bi = db("cwbbsjs")->where(['neeq' => $neeq , 'type' => 3 , 'bbxm' => 'revenue_YOY'])->field("bbrq,xmsz")->order("bbrq asc")->select();
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
        $arr = $this->getDataAndBi($shouru, $shouru_bi);
        $info['shouru'] = $arr[0];
        $info['shouru_bi'] = $arr[1];
        //每股净资产树状图
        $zichan = db("cwbbsjs")->where(['neeq' => $neeq , 'type' => 3 , 'bbxm' => 'BPS'])->field("bbrq,xmsz")->order("bbrq asc")->select();
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
        $arr = $this->getDataAndBi($zichan, $zichan_bi);
        $info['zichan'] = $arr[0];
        $info['zichan_bi'] = $arr[1];
        //每股现金流树状图
        $xianjinliu = db("cwbbsjs")->where(['neeq' => $neeq , 'type' => 3 , 'bbxm' => 'CFPS'])->field("bbrq,xmsz")->order("bbrq asc")->select();
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
        $arr = $this->getDataAndBi($xianjinliu, $xianjinliu_bi);
        $info['xianjinliu'] = $arr[0];
        $info['xianjinliu_bi'] = $arr[1];
        //毛利率树状图
        $maolilv = db("cwbbsjs")->where(['neeq' => $neeq , 'type' => 3 , 'bbxm' => 'GROSSPROFITMARGIN'])->field("bbrq,xmsz")->order("bbrq asc")->select();
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
        $info['maolilv'] = $arr[0];
        $info['maolilv_bi'] = $arr[1];
        //每股收益树状图
        $shouyi = db("cwbbsjs")->where(['neeq' => $neeq , 'type' => 1 , 'bbxm' => 'BASICEPS'])->field("bbrq,xmsz")->order("bbrq asc")->select();
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
        $info['shouyi'] = $arr[0];
        $info['shouyi_bi'] = $arr[1];
        //每股公积金树状图
        $gongjijin = db("cwbbsjs")->where(['neeq' => $neeq , 'type' => 3 , 'bbxm' => 'SURPLUSCAPITALPS'])->field("bbrq,xmsz")->order("bbrq asc")->select();
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
        $arr = $this->getDataAndBi($gongjijin, $gongjijin_bi);
        $info['gongjijin'] = $arr[0];
        $info['gongjijin_bi'] = $arr[1];
        //每股未分配树状图
        $weifenpei = db("cwbbsjs")->where(['neeq' => $neeq , 'type' => 3 , 'bbxm' => 'UNDISTRIBUTEDPS'])->field("bbrq,xmsz")->order("bbrq asc")->select();
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
        $arr = $this->getDataAndBi($weifenpei, $weifenpei_bi);
        $info['weifenpei'] = $arr[0];
        $info['weifenpei_bi'] = $arr[1];
        //利润表
        $cwbbs1 = db("cwbbs")->where(['neeq' => $neeq , 'type' => 1])->order("reportdate desc")->select();
        if(!empty($cwbbs1)){
            foreach ($cwbbs1 as $key => $value) {
                if(substr($value['reportdate'], 5, 2) == '12'){
                    $sign = '年';
                } else {
                    $sign = '中';
                }
                $cwbbs1[$key]['reportdate'] = substr($value['reportdate'], 0, 4).$sign.'报';
                $values = [];
                $cwbbs_info = db("cwbbsjs")->where(['neeq' => $neeq , 'type' => 1 , 'bbrq' => $value['reportdate']])->select();
                foreach ($cwbbs_info as $key1 => $value1) {
                    $xmsz = round(trim($value1['xmsz'], '-'), 2);
                    if($xmsz > 100000000){
                        $zhi = round($value1['xmsz']/100000000 , 2).'亿';
                    }elseif ($xmsz > 10000) {
                        $zhi = round($value1['xmsz']/10000 , 2).'万';
                    } else {
                        $zhi  = round($value1['xmsz'] , 2).'元';
                    }
                    $values[$value1['bbxm']] = $zhi;
                }
                $cwbbs1[$key]['value'] = $values;
            }
        }
        $info['cwbbs1'] = $cwbbs1;
        //资产负债表
        $cwbbs2 = db("cwbbs")->where(['neeq' => $neeq , 'type' => 0])->order("reportdate desc")->select();
        if(!empty($cwbbs2)){
            foreach ($cwbbs2 as $key => $value) {
                if(substr($value['reportdate'], 5, 2) == '12'){
                    $sign = '年';
                } else {
                    $sign = '中';
                }
                $cwbbs2[$key]['reportdate'] = substr($value['reportdate'], 0, 4).$sign.'报';
                $values = [];
                $cwbbs_info = db("cwbbsjs")->where(['neeq' => $neeq , 'type' => 0 , 'bbrq' => $value['reportdate']])->select();
                foreach ($cwbbs_info as $key1 => $value1) {
                    $xmsz = round(trim($value1['xmsz'], '-'), 2);
                    if($xmsz > 100000000){
                        $zhi = round($value1['xmsz']/100000000 , 2).'亿';
                    }elseif ($xmsz > 10000) {
                        $zhi = round($value1['xmsz']/10000 , 2).'万';
                    } else {
                        $zhi  = round($value1['xmsz'] , 2).'元';
                    }
                    $values[$value1['bbxm']] = $zhi;
                }
                $cwbbs2[$key]['value'] = $values;
            }
        }
        $info['cwbbs2'] = $cwbbs2;
        //现金流量表
        $cwbbs3 = db("cwbbs")->where(['neeq' => $neeq , 'type' => 2])->order("reportdate desc")->select();
        if(!empty($cwbbs3)){
            foreach ($cwbbs3 as $key => $value) {
                if(substr($value['reportdate'], 5, 2) == '12'){
                    $sign = '年';
                } else {
                    $sign = '中';
                }
                $cwbbs3[$key]['reportdate'] = substr($value['reportdate'], 0, 4).$sign.'报';
                $values = [];
                $cwbbs_info = db("cwbbsjs")->where(['neeq' => $neeq , 'type' => 2 , 'bbrq' => $value['reportdate']])->select();
                foreach ($cwbbs_info as $key1 => $value1) {
                    $xmsz = round(trim($value1['xmsz'], '-'), 2);
                    if($xmsz > 100000000){
                        $zhi = round($value1['xmsz']/100000000 , 2).'亿';
                    }elseif ($xmsz > 10000) {
                        $zhi = round($value1['xmsz']/10000 , 2).'万';
                    } else {
                        $zhi  = round($value1['xmsz'] , 2).'元';
                    }
                    $values[$value1['bbxm']] = $zhi;
                }
                $cwbbs3[$key]['value'] = $values;
            }
        }
        $info['cwbbs3'] = $cwbbs3;
        return $info;
    }
    public function getZfmxesLimitList($start = 0 , $length = 0){
        $result = db("zfmxes_add")
                ->where(['zfmx_examine' => 1 , 'zfmx_exhibition' => 1 ,'zfmx_financing' => 0])
                ->order("stick desc,plannoticeddate desc")
                ->limit($start , $length)
                ->select();
        $model = model("Zfmxes");
        foreach ($result as $key => $value) {
            if(strpos($value['lirun'],'-') === false){
                if ($value['lirun'] >= 100000000) {
                   $result[$key]['lirun'] = round(($value['lirun']/100000000),2).'亿'; 
                } elseif ($value['lirun'] >= 10000) {
                    $result[$key]['lirun'] = round(($value['lirun']/10000),2).'万';
                } else{
                    $result[$key]['lirun'] = round($value['lirun'],2).'元';
                }
            }else{
                $lirun = trim($value['lirun'],'-');
                if ($lirun >= 100000000) {
                   $result[$key]['lirun'] = '-'.round(($lirun/100000000),2).'亿'; 
                } elseif ($lirun >= 10000) {
                    $result[$key]['lirun'] = '-'.round(($lirun/10000),2).'万';
                } else{
                    $result[$key]['lirun'] = '-'.round($lirun,2).'元';
                }
            }
            $info = $model->where(['neeq' => $value['neeq'] , 'plannoticeddate' => $value['plannoticeddate']])->find();
            $result[$key]['sec_uri_tyshortname'] = $info['sec_uri_tyshortname'];
            $result[$key]['trademethod'] = $info['trademethod'];
        }
        $model = model("Cwbbs");
        foreach ($result as $key => $value) {
            $profit = $model->getNetProfitByNeeq($value['neeq']);
            $profit1 = round(trim($profit, '-'), 2);
            if($profit1 > 10000){
                $result[$key]['profit'] = round($profit/10000, 2).'万';
            }elseif ($profit1 > 100000000) {
                $result[$key]['profit'] = round($profit/100000000, 2).'亿';
            } else {
                $result[$key]['profit'] = round($profit, 2);
            }
            $result[$key]['url'] = url("ZfmxesInfo/index" , ['neeq' => $value['neeq'] , 'plannoticeddate' => $value['plannoticeddate']]);
        }
        return $result;
    }
    public function setCollection($neeq , $plannoticeddate , $sign){
        if(empty($neeq)){
            $this->error = '项目代码不存在';
            return false;
        }
        if(empty($plannoticeddate)){
            $this->error = '项目公告日不存在';
            return false;
        }
        if(empty($sign)){
            $this->error = '缺少收藏标签sign';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $arr = [];
            $user = session("FINANCE_USER");
            $collection_zfmxes = explode(',', $user['collection_zfmxes']);
            if($sign == 'true'){
                if(in_array($neeq.'_'.$plannoticeddate, $collection_zfmxes)){
                    $this->error = '该项目已收藏,不能重复收藏';
                    return false;
                } else {
                    array_push($collection_zfmxes, $neeq.'_'.$plannoticeddate);
                    $coll = trim(implode(',', $collection_zfmxes) , ',');
                    $model = model("Member");
                    $model->where(['uid' => $user['uid']])->update(['collection_zfmxes' => $coll]);
                    $user = $model->where(['uid' => $user['uid']])->find();
                    session('FINANCE_USER' , $user);
                }
            } else {
                if(in_array($neeq.'_'.$plannoticeddate, $collection_zfmxes)){
                    foreach ($collection_zfmxes as $key => $value) {
                        if($value == $neeq.'_'.$plannoticeddate){
                            unset($collection_zfmxes[$key]);
                        }
                    }
                    $coll = trim(implode(',', $collection_zfmxes) , ',');
                    $model = model("Member");
                    $model->where(['uid' => $user['uid']])->update(['collection_zfmxes' => $coll]);
                    $user = $model->where(['uid' => $user['uid']])->find();
                    session('FINANCE_USER' , $user);
                } else {
                    $this->error = '该项目未收藏,不能取消收藏';
                    return false;
                }
            }
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
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
	
	//新方法，财报
	public function getZfmxesCompletebao($neeq,$id,$type){
		//利润表
		$cwbbs1 = db("cwbbs")->where(['neeq' => $neeq , 'type' => $type])->order("reportdate desc")->select();
		$arr = array();
		//最新
		if(!empty($cwbbs1)){
				if($id == 1){
					$values = [];
					foreach ($cwbbs1 as $key => $value) {
						if(substr($value['reportdate'], 5, 2) == '12'){
							$sign = '年';
							$cwbbs1[$key]['reportdate'] = substr($value['reportdate'], 0, 4).$sign.'报';
							/***
							$cwbbs_info = db("cwbbsjs")->where(['neeq' => $neeq , 'type' => $type , 'bbrq' => $value['reportdate']])->select();
							//var_dump(db('cwbbsjs')->getLastSql());
							foreach ($cwbbs_info as $key1 => $value1) {
								if(preg_match('/^[0-9-]+$/',$value1['xmsz'])){
									$values[$value1['bbxm']] = $value1['xmsz'];
								}else{
									$xmsz = round(trim($value1['xmsz'], '-'), 2);
									if($xmsz > 100000000){
										$zhi = round($value1['xmsz']/100000000 , 2).'亿';
									}elseif ($xmsz > 10000) {
										$zhi = round($value1['xmsz']/10000 , 2).'万';
									} else {
										$zhi  = round($value1['xmsz'] , 2).'元';
									}
									$values[$value1['bbxm']] = $zhi;
								}
							}
							$cwbbs1[$key]['value'] = $values;
							array_push($arr,$cwbbs1[$key]);
							***/
							$num = [];
							$cwbbxms=db("Cwbbxms")->where(['neeq' => $neeq , 'type' => $type])->select();
							foreach($cwbbxms as $key1 => $value1){
								$cwbbs_info1 = db("cwbbsjs")->where(['neeq' => $neeq , 'type' => $type , 'bbrq' => $value['reportdate'],'bbxm' => $value1['code']])->find();
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
								$cwbbxms[$key1]['num'] = $zhi;
								array_push($num,$cwbbxms[$key1]['num']);
								unset($num[0]);
								unset($cwbbxms[0]);
							}
							$cwbbs_info = db("cwbbsjs")->where(['neeq' => $neeq , 'type' => $type , 'bbrq' => $value['reportdate']])->order("bbxmxh")->select();
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
							$cwbbxms=db("Cwbbxms")->where(['neeq' => $neeq , 'type' => $type])->select();
							foreach($cwbbxms as $key1 => $value1){
								$cwbbs_info1 = db("cwbbsjs")->where(['neeq' => $neeq , 'type' => $type , 'bbrq' => $value['reportdate'],'bbxm' => $value1['code']])->find();
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
								$cwbbxms[$key1]['num'] = $zhi;
								array_push($num,$cwbbxms[$key1]['num']);
								unset($num[0]);
								unset($cwbbxms[0]);
							}
							$cwbbs_info = db("cwbbsjs")->where(['neeq' => $neeq , 'type' => $type , 'bbrq' => $value['reportdate']])->order("bbxmxh")->select();
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
						$cwbbxms=db("Cwbbxms")->where(['neeq' => $neeq , 'type' => $type])->select();
						foreach($cwbbxms as $key1 => $value1){
							$cwbbs_info1 = db("cwbbsjs")->where(['neeq' => $neeq , 'type' => $type , 'bbrq' => $value['reportdate'],'bbxm' => $value1['code']])->find();
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
							$cwbbxms[$key1]['num'] = $zhi;
							array_push($num,$cwbbxms[$key1]['num']);
							unset($num[0]);
							unset($cwbbxms[0]);
						}
						$cwbbs_info = db("cwbbsjs")->where(['neeq' => $neeq , 'type' => $type , 'bbrq' => $value['reportdate']])->order("bbxmxh")->select();
						array_push($values,array("key"=>$cwbbs1[$key]['reportdate'],"value"=>$num));
						
					}
				}
		}
		$info['cwbbs1'] = $cwbbxms;
		$info['data'] = $values;
		return $info;		
	}
}
