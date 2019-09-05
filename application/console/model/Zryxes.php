<?php

namespace app\console\model;

use think\Model;
use sys\Wechat;

class Zryxes extends Model
{
    public function addZryxesQrCode($id , $filename){
        //开启事务
        $this->startTrans();
        try{
            model("ZryxesEffect")->where(['id' => $id])->update(['qr_code' => $filename]);
            $weObj = new Wechat(config("WeiXinCg"));
            $access_token = getAccessTokenFromFile();
            $result = $weObj->checkAuth('', '', $access_token);
            $result = $weObj->getQRCode('zryxes&'.$id, 1);
            $result = $weObj->getQRUrl($result['ticket']);
            $result = getImage($result, config("QRCodePath"), $filename, 1);
            if($result['error']){
                $this->error = $result['msg'];
                return false;
            }
            // 提交事务
            $this->commit();
            return $id;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }

    public function getZryxesInfoById($id){
        $info = $this->where(['id' => $id])->find();
        //企业信息
        $model = model("Gszls");
        $gszls = $model->getGszlsInfoByNeeq($info['neeq']);
        $info['gszls'] = $gszls;
        return $info;
    }
    public function releaseZryxes($id){
        if(empty($id)){
            $this->error = '相关大宗项目id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $info = $this->where(['id' => $id])->find();
            $model = model("ZryxesEffect");
            $result = $model->getZryxesEffectByCode($info['neeq']);
            $user = \app\console\service\User::getInstance()->getInfo();
            $status = db("caiji_status")->where(['neeq' => $info['neeq']])->find();
            if(!empty($status)){
                model("CaijiStatus")->where(['neeq' => $info['neeq']])->update(['zryxes_status' => 1]);
            } else {
                $arr = [];
                $arr['neeq'] = $info['neeq'];
                $arr['zryxes_status'] = 1;
                model("CaijiStatus")->allowField(true)->save($arr);
            }
            if(empty($result)){
                $lirun = model('Cwbbsjs')->where(['neeq' => $info['neeq'], 'type' => 1, 'bbxm' => 'NETPROFIT'])->order('bbrq desc')->limit(1)->find();
                $shouru = model('Cwbbsjs')->where(['neeq' => $info['neeq'], 'type' => 1, 'bbxm' => 'OPERATEREVE'])->order('bbrq desc')->limit(1)->find();
                $lirun = empty($lirun) ? 0 : $lirun['xmsz'];
                $shouru = empty($shouru) ? 0 : $shouru['xmsz'];
                $arr = [];
                $arr['neeq'] = $info['neeq'];
                $arr['sublevel'] = $info['sublevel'];
                $arr['trademethod'] = $info['trademethod'];
                $arr['secucode'] = $info['secucode'];
                $arr['sec_uri_tyshortname'] = $info['sec_uri_tyshortname'];
                $arr['new'] = $info['new'];
                $arr['direction'] = $info['direction'];
                $arr['price'] = $info['price'];
                $arr['num'] = $info['num'];
                $arr['isdiscount'] = $info['isdiscount'];
                $arr['enddate'] = $info['enddate'];
                $arr['name'] = $info['name'];
                $arr['contact'] = $info['contact'];
                $arr['accountment'] = $info['accountment'];
                $arr['noticedate'] = $info['noticedate'];
                $arr['created_at'] = $info['created_at'];
                $arr['create_time'] = time();
                $arr['create_uid'] = $user['uid'];
                $arr['lirun'] = $lirun;
                $arr['shouru'] = $shouru;
//                $filename = 'zr_'.$id.genRandomString(10).'.jpg';
//                $arr['qr_code'] = $filename;
                $model->allowField(true)->save($arr);
                $id = $model->getLastInsID();
                $system = model("SystemConfig")->getSystemConfigInfo();
                $result = createProEditQrcodeDirection($system['current'] , 'zryxes' , $id , '' , '');
                if(!$result){
                    $this->error = '创建二维码指向失败';
                    return false;
                } else {
                    $model->where(['id' => $id])->update(['qr_code' => $result]);
                }
//                
//                
//                $weObj = new Wechat(config("WeiXinCg"));
//                $access_token = getAccessTokenFromFile();
//                $result = $weObj->checkAuth('', '', $access_token);
//                $result = $weObj->getQRCode('zryxes&'.$id, 1);
//                $result = $weObj->getQRUrl($result['ticket']);
//                $result = getImage($result, config("QRCodePath"), $filename, 1);
//                if($result['error']){
//                    $this->error = $result['msg'];
//                    return false;
//                }
            } else {
                $this->error = '该股票已发布,无法重复发布';
                return false;
            }
            // 提交事务
            $this->commit();
            return $id;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
}