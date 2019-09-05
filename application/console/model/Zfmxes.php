<?php

namespace app\console\model;

use think\Model;
use sys\Wechat;

class Zfmxes extends Model
{
    public function addZfmxesQrCode($neeq , $plannoticeddate , $filename){
        //开启事务
        $this->startTrans();
        try{
            $info = model("ZfmxesAdd")->where(['neeq' => $neeq , 'plannoticeddate' => $plannoticeddate])->update(['qr_code' => $filename]);
            $info = model("ZfmxesAdd")->where(['neeq' => $neeq , 'plannoticeddate' => $plannoticeddate])->find();
            $weObj = new Wechat(config("WeiXinCg"));
            $access_token = getAccessTokenFromFile();
            $result = $weObj->checkAuth('', '', $access_token);
            $result = $weObj->getQRCode('zfmxes&'.$info['neeq'].'&'.$info['plannoticeddate'], 1);
            $result = $weObj->getQRUrl($result['ticket']);
            $result = getImage($result, config("QRCodePath"), $filename, 1);
            if($result['error']){
                $this->error = $result['msg'];
                return false;
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
    public function getZfmxesSimpleInfo($id){
        $info = $this->where(['id' => $id])->find();
        return $info;
    }
    public function getZfmxesInfoById($id){
        $info = $this->where(['id' => $id])->find();
        //企业信息
        $model = model("Gszls");
        $gszls = $model->getGszlsInfoByNeeq($info['neeq']);
        $info['gszls'] = $gszls;
        //增发明细添加信息
        $model = model("ZfmxesAdd");
        $zfmx_add = $model->getZfmxesAddByCodeAndDate($info['neeq'] , $info['plannoticeddate']);
        $info['add'] = $zfmx_add;
        //备注信息
        $model = model("CaijiRemark");
        $remark = $model->getRemarkByCodeDateAndType($info['neeq'] , $info['plannoticeddate'] , 'Zfmxes' , 0);
        $info['remark'] = $remark;
        //签约
        $model = model("ZfmxesSign");
        $sign = $model->getZfmxesSignByCodeAndDate($info['neeq'] , $info['plannoticeddate']);
        $info['sign'] = $sign;
        return $info;
    }
    public function setZfmxesExamine($id){
        if(empty($id)){
            $this->error = '相关增发明细项目id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $info = $this->where(['id' => $id])->find();
            $model = model("ZfmxesAdd");
            $info1 = $model->getZfmxesAddByCodeAndDate($info['neeq'] , $info['plannoticeddate']);
            if(empty($info1)){
                $lirun = model('Cwbbsjs')->where(['neeq' => $info['neeq'], 'type' => 1, 'bbxm' => 'NETPROFIT'])->order('bbrq desc')->limit(1)->find();
                $shouru = model('Cwbbsjs')->where(['neeq' => $info['neeq'], 'type' => 1, 'bbxm' => 'OPERATEREVE'])->order('bbrq desc')->limit(1)->find();
                $lirun = empty($lirun) ? 0 : $lirun['xmsz'];
                $shouru = empty($shouru) ? 0 : $shouru['xmsz'];
                $arr = [];
                $arr['plannoticeddate'] = $info['plannoticeddate'];
                $arr['neeq'] = $info['neeq'];
                $arr['lirun'] = $lirun;
                $arr['shouru'] = $shouru;
                $arr['sublevel'] = $info['sublevel'];
                $arr['sec_uri_tyshortname'] = $info['sec_uri_tyshortname'];
                $arr['zfmx_examine'] = 1;
                $arr['create_time'] = time();
                $model->allowField(true)->save($arr);
                $system = model("SystemConfig")->getSystemConfigInfo();
                $result = createProEditQrcodeDirection($system['current'] , 'zfmxes' , '' , $info['neeq'] , $info['plannoticeddate']);
                if(!$result){
                    $this->error = '创建二维码指向失败';
                    return false;
                } else {
                    $model->where(['neeq' => $info['neeq'] , 'plannoticeddate' => $info['plannoticeddate']])->update(['qr_code' => $result]);
                }
            } else {
                $model->allowField(true)->where(['neeq' => $info['neeq'] , 'plannoticeddate' => $info['plannoticeddate']])->update(['zfmx_examine' => 1]);
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
    public function setZfmxesTopTrue($id){
        if(empty($id)){
            $this->error = '相关增发明细项目id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $info = $this->where(['id' => $id])->find();
            $model = model("ZfmxesAdd");
            $info1 = $model->where(['neeq' => $info['neeq'] , 'plannoticeddate' => $info['plannoticeddate']])->update(['stick' => 1]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    public function setZfmxesTopFalse($id){
        if(empty($id)){
            $this->error = '相关增发明细项目id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $info = $this->where(['id' => $id])->find();
            $model = model("ZfmxesAdd");
            $info1 = $model->where(['neeq' => $info['neeq'] , 'plannoticeddate' => $info['plannoticeddate']])->update(['stick' => 0]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    public function setZfmxesFinancing($id){
        if(empty($id)){
            $this->error = '相关增发明细项目id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $info = $this->where(['id' => $id])->find();
            $model = model("ZfmxesAdd");
            $info1 = $model->getZfmxesAddByCodeAndDate($info['neeq'] , $info['plannoticeddate']);
            if(empty($info1)){
                $arr = [];
                $arr['plannoticeddate'] = $info['plannoticeddate'];
                $arr['neeq'] = $info['neeq'];
                $arr['zfmx_financing'] = 1;
                $model->allowField(true)->save($arr);
            } else {
                $model->allowField(true)->where(['neeq' => $info['neeq'] , 'plannoticeddate' => $info['plannoticeddate']])->update(['zfmx_financing' => 1]);
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
    public function setZfmxesExhibitionTrue($id){
        if(empty($id)){
            $this->error = '相关增发明细项目id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $info = $this->where(['id' => $id])->find();
            $model = model("ZfmxesAdd");
            $info1 = $model->getZfmxesAddByCodeAndDate($info['neeq'] , $info['plannoticeddate']);
            if(empty($info1)){
                $arr = [];
                $arr['plannoticeddate'] = $info['plannoticeddate'];
                $arr['neeq'] = $info['neeq'];
                $arr['zfmx_exhibition'] = 1;
                $model->allowField(true)->save($arr);
            } else {
                $model->allowField(true)->where(['neeq' => $info['neeq'] , 'plannoticeddate' => $info['plannoticeddate']])->update(['zfmx_exhibition' => 1]);
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
    public function setZfmxesExhibitionFalse($id){
        if(empty($id)){
            $this->error = '相关增发明细项目id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $info = $this->where(['id' => $id])->find();
            $model = model("ZfmxesAdd");
            $info1 = $model->getZfmxesAddByCodeAndDate($info['neeq'] , $info['plannoticeddate']);
            if(empty($info1)){
                $arr = [];
                $arr['plannoticeddate'] = $info['plannoticeddate'];
                $arr['neeq'] = $info['neeq'];
                $arr['zfmx_exhibition'] = 0;
                $model->allowField(true)->save($arr);
            } else {
                $model->allowField(true)->where(['neeq' => $info['neeq'] , 'plannoticeddate' => $info['plannoticeddate']])->update(['zfmx_exhibition' => 0]);
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