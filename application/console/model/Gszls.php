<?php

namespace app\console\model;

use think\Model;

class Gszls extends Model
{
    public function getGszlsInfoByNeeq($neeq){
        if(empty($neeq)){
            $this->error = '股票代码不存在';
            return false;
        }
        $info = $this->where(['neeq' => $neeq])->find();
        return $info;
    }

    public function getGszlsInfoById($id){
        $info = $this->where(['id' => $id])->find();
        //$gdyjs = db("gdyjs")->where(['neeq' => $info['neeq']])->select();
		
		$gdyjs_date = db("gdyjs")->where(['neeq' => $info['neeq']])->group("jzrq desc")->field("jzrq")->select();
		if(!empty($gdyjs_date)){
            $gdyjs = db("gdyjs")->where(['neeq' => $info['neeq'] , 'jzrq' => $gdyjs_date[0]['jzrq']])->limit(10)->select();
        } else {
            $gdyjs = [];
        }
		
        $ggzls = db("ggzls")->where(['neeq' => $info['neeq']])->where("deleted_at is null")->select();
        $zyyws = db("zyyws")->where(['neeq' => $info['neeq']])->select();
        $zfmxes = db("ZfmxesAdd")
                ->alias("za")
                ->join("Zfmxes z","z.neeq=za.neeq and z.plannoticeddate=za.plannoticeddate")
                ->where("za.neeq","eq",$info['neeq'])
                ->field("z.*,za.zfmx_financing")
                ->select();
        $zryxes = db("ZryxesEffect")->where(['neeq' => $info['neeq']])->select();
        if(!empty($zfmxes)){
            $info['zfmxes'] = $zfmxes;
        } else {
            $info['zfmxes'] = [];
        }
		
        if(!empty($zryxes)){
            foreach ($zryxes as $key => $value) {
                $zryxes[$key]['create_time'] = date("Y-m-d H:i:s" , $value['create_time']);
                if($value['update_time'] == 0){
                    $zryxes[$key]['update_time'] = '-';
                } else {
                    $zryxes[$key]['update_time'] = date("Y-m-d H:i:s" , $value['update_time']);
                }
            }
            $info['zryxes'] = $zryxes;
        } else {
            $info['zryxes'] = [];
        }
		
		//占比
		if(!empty($info['zgbwg'])){
			foreach($gdyjs as $key=>$value){
				$gdyjs[$key]['fen'] = round(floatval($gdyjs[$key]['cgsl']/(preg_replace('/,*/','',$info['zgbwg'])*10000)),4)*100;
			}
		}else{
			foreach($gdyjs as $key=>$value){
				$gdyjs[$key]['fen'] = '';
			}
		}
        if(!empty($gdyjs)){
            $info['gdyjs'] = $gdyjs;
        } else {
            $info['gdyjs'] = [];
        }
        if(!empty($ggzls)){
            $info['ggzls'] = $ggzls;
        } else {
            $info['ggzls'] = [];
        }
        if(!empty($zyyws)){
            $info['zyyws'] = $zyyws;
        } else {
            $info['zyyws'] = [];
        }
		
        return $info;
    }

    public function hideGszls($id){
        if(empty($id)){
            $this->error = '该企业id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $info = $this->where(['id' => $id])->find();
            if(empty($info)){
                $this->error = '该id不存在企业信息';
                return false;
            }
            $result = model("CaijiStatus")->where(['neeq' => $info['neeq']])->find();
            if(empty($result)){
                $arr = [];
                $arr['neeq'] = $info['neeq'];
                $arr['gszls_status'] = -1;
                model("CaijiStatus")->allowField(true)->save($arr);
            } else {
                model("CaijiStatus")->where(['neeq' => $info['neeq']])->update(['gszls_status' => -1]);
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
    public function showGszls($id){
        if(empty($id)){
            $this->error = '该企业id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $info = $this->where(['id' => $id])->find();
            if(empty($info)){
                $this->error = '该id不存在企业信息';
                return false;
            }
            $result = model("CaijiStatus")->where(['neeq' => $info['neeq']])->find();
            if(empty($result)){
                $arr = [];
                $arr['neeq'] = $info['neeq'];
                $arr['gszls_status'] = 1;
                model("CaijiStatus")->allowField(true)->save($arr);
            } else {
                model("CaijiStatus")->where(['neeq' => $info['neeq']])->update(['gszls_status' => 1]);
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
}
