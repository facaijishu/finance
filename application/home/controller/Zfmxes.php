<?php
namespace app\home\controller;
use think\think\db;

class Zfmxes extends Base
{
    public function conlist(){
        $this->assign('title' , ' - 定向增发');
        $this->assign('img' , '');
        //$this->assign('des' , '这里有最具体的定向增发!');
        $model = model('SystemConfig');
        $system_config = $model->getSystemConfigInfo();
        $this->assign('des' , $system_config['zfmxes_des']);
        $list = db("Zfmxes_add")->where(['zfmx_examine' => 1 , 'zfmx_financing' => 0])->select();
        $now_time = time();
        $all_time = 3600*24*90;
        $str = '';
        foreach ($list as $key => $value) {
            $time = intval($now_time)-intval($value['create_time']);
            if($time > $all_time){
                $info = model("Zfmxes")->setFinancingById($value['id']);
                if(!$info){
                    $str .= $value['id'].',';
                }
            }
        }
        
        $model = model("Zfmxes");
        $zfmxes = $model->getZfmxesLimitList(0 , config("ins_num"));
        $this->assign('num' , count($zfmxes));
        $this->assign('zfmxes' , $zfmxes);
		//增加的代码
		$model = model("dict");
		$capital_plan = $model->getDictByType('capital_plan');
        $this->assign('capital_plan' , $capital_plan);
		$transfer_type = $model->getDictByType('transfer_type');
        $this->assign('transfer_type' , $transfer_type);
        $hierarchy = $model->getDictByType('hierarchy');
        $this->assign('hierarchy' , $hierarchy);
	//客服logo
        $model = model('Project');
        $project = $model->getProjectLimitOne();
        $this->assign('project',$project);	
        return view();
    }
    public function selectMore(){
        $data = input();
        $model = model("Zfmxes");
        $list = $model->getZfmxesLimitList($data['id'] , config("ins_num"));
        if($list){
            $this->result($list, 1, '搜索成功', 'json');
        } else {
            $this->result([], 0, '已无数据', 'json');
        }
    }
	
	public function sproject(){
        $data = input();
		$id = input("id");
		$model = db('Zfmxes_add')
					->alias('za')
					->where(['zfmx_examine' => 1 , 'zfmx_exhibition' => 1 ,'zfmx_financing' => 0])
                                        ->order('plannoticeddate','desc');
        //搜索条件
		if('' !== $data['gupiao']){
			$model->where("sec_uri_tyshortname like '%".$data['gupiao']."%' or neeq like '%".$data['gupiao']."%'");
		}
		//融资金额
        if('' !== $data['raise_money']){
            if(1 == $data['raise_money']){
				$model->where('raise_money <= 1000');
            }elseif (2 == $data['raise_money']) {
				$model->where('raise_money > 1000 and raise_money <= 3000');
            }elseif (3 == $data['raise_money']) {
				$model->where('raise_money > 3000 and raise_money <= 5000');
            } else {
				$model->where('raise_money > 5000');
            }
        }
		//层级
        if('' !== $data['sublevel']) {
			if($data['sublevel'] == 9){
				$data['sublevel'] = "创新层";
			}elseif($data['sublevel'] == 10){
				$data['sublevel'] = "基础层";
			}elseif($data['sublevel'] == 11){
				$data['sublevel'] = "拟上市";
			}
            $model->where('sublevel','eq',$data['sublevel']);
        }
        //净利润
        if('' !== $data['xmsz']){
            if(1 == $data['xmsz']){
                $model->where("lirun <= 500");
            }elseif (2 == $data['xmsz']) {
                $model->where("lirun > 500 and lirun <= 2000");
            }elseif (3 == $data['xmsz']) {
                $model->where("lirun > 2000 and lirun <= 5000");
            } else {
                $model->where("lirun > 5000");
            }
        }
        //年营业额
        if('' !== $data['xmsz1']) {
            if (1 == $data['xmsz1']) {
                $model->where("shouru <= 500");
            } elseif (2 == $data['xmsz1']) {
                $model->where("shouru > 500 and shouru <= 2000");
            } elseif (3 == $data['xmsz1']) {
                $model->where("shouru > 2000 and shouru <= 5000");
            } elseif (4 == $data['xmsz1']) {
                $model->where("shouru > 5000 and shouru <= 10000");
            } elseif (5 == $data['xmsz1']) {
                $model->where("shouru > 10000 and shouru <= 20000");
            } else {
                $model->where("shouru > 20000");
            }
        }

		if(isset($id)){
			$result = $model->limit($id, config('ins_num'))->select();
		}else{
			$result = $model->limit(0, config('ins_num'))->select();
		}
        //var_dump(db('Zfmxes_add')->getLastSql());
		if(empty($result)){
			$this->result('' , 0 , '不存在数据1' , 'json');
		}
		/***
        //净利润
        if('' !== $data['xmsz']){
            if(1 == $data['xmsz']){
				$k = $this->sed(500,'<','yingli',$result,'NETPROFIT');
            }elseif (2 == $data['xmsz']) {
				$k = $this->sed(array(500,2000),'','yingli',$result,'NETPROFIT');
            }elseif (3 == $data['xmsz']) {
				$k = $this->sed(array(2000,5000),'','yingli',$result,'NETPROFIT');
            } else {
				$k = $this->sed(5000,'','yingli',$result,'NETPROFIT');
            }
        }
		//年营业额
        if('' !== $data['xmsz1']){
            if(1 == $data['xmsz1']){
				$k = $this->sed(500,'<','zong',$result,'OPERATEREVE');
            }elseif (2 == $data['xmsz1']) {
                $k = $this->sed(array(500,2000),'','zong',$result,'OPERATEREVE');
            }elseif (3 == $data['xmsz1']) {
				$k = $this->sed(array(2000,5000),'','zong',$result,'OPERATEREVE');
            }elseif (4 == $data['xmsz1']) {
				$k = $this->sed(array(5000,10000),'','zong',$result,'OPERATEREVE');
            }elseif (5 == $data['xmsz1']) {
				$k = $this->sed(array(10000,20000),'','zong',$result,'OPERATEREVE');
            } else {
				$k = $this->sed(20000,'','zong',$result,'OPERATEREVE');
            }
        }
         ****/
		if(!isset($k)){
			$k = $result;
		}else{
			$k = $k;
		}
		foreach($k as $key=>$value){
		    $trademethod = model('Zfmxes')->where("neeq = ".$k[$key]['neeq'])->order('plannoticeddate desc')->limit(1)->find();
			if($trademethod['trademethod'] == "做市"){
				$k[$key]['trademethod'] = "市";
			}else{
				$k[$key]['trademethod'] = "竞";
			}
			$k[$key]['sec_uri_tyshortname'] = $trademethod['sec_uri_tyshortname'];
			$k[$key]['url'] = url('ZfmxesInfo/index',['neeq' => $k[$key]['neeq'] ,'plannoticeddate' => $k[$key]['plannoticeddate']]);
		}

        if($k){
            $this->result($k , 1 , '搜索成功' , 'json');
        } else {
            $this->result('' , 0 , '不存在数据' , 'json');
        }
    }
	/**
	   $a :  array()
	   $b :  符号 > < >= <=
	   $c :  'yingli'  'zong'
	   $result 
	   $d :  'OPERATEREVE' ,'NETPROFIT'
	**/
	
	public function sed($a,$b,$c,$result,$d){
		if(!empty($a)){
			if(!is_array($a)){
				foreach($result as $key => $x){
					$cw2 = db('cwbbsjs')->field('xmsz')->where("xmsz>$a")->where(array('bbxm' => $d,'neeq'=>$x['neeq'],'type'=>2))->order('bbrq desc')->limit(1)->select();
					
					if(!empty($cw2) && $cw2[0]['xmsz']>$a){
						$result[$key][$c] = $cw2[0]['xmsz'];
						array_push($result,$result[$key][$c]);
					}
				}
				foreach($result as $key=>$value){
					if(empty($result[$key][$c])){
						unset($result[$key]);
					}
				}
			}else if($b == "<"){
				foreach($result as $key => $x){
					$cw2 = db('cwbbsjs')->field('xmsz')->where("xmsz<=$a")->where(array('bbxm' => $d,'neeq'=>$x['neeq'],'type'=>2))->order('bbrq desc')->limit(1)->select();


					if(!empty($cw2) && $cw2[0]['xmsz']<=$a){
						$result[$key][$c] = $cw2[0]['xmsz'];
						array_push($result,$result[$key][$c]);
					}
				}
				foreach($result as $key=>$value){
					if(empty($result[$key][$c])){
						unset($result[$key]);
					}
				}
				
			}else{
				foreach($result as $key => $x){
					$cw2 = db('cwbbsjs')->field('xmsz')->where("xmsz>$a[0] and xmsz<=$a[1]")->where(array('bbxm' => $d,'neeq'=>$x['neeq'],'type'=>2))->order('bbrq desc')->limit(1)->select();
					
					if(!empty($cw2) && $cw2[0]['xmsz']>$a[0] && $cw2[0]['xmsz']<=$a[1]){
						$result[$key][$c] = $cw2[0]['xmsz'];
						array_push($result,$result[$key][$c]);
					}
				}
				foreach($result as $key=>$value){
					if(empty($result[$key][$c])){
						unset($result[$key]);
					}
				}
			}
		}
		return $result;
	}
	
}
