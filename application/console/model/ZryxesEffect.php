<?php

namespace app\console\model;

use think\Model;
use sys\Wechat;

class ZryxesEffect extends Model
{
    public function getZryxesEffectList () {
        $result = $this->select();
        return $result;
    }

    public function setFinancingById($id){
        $info = $this->where(['id' => $id])->update(['zryx_financing' => 1]);
        return $info;
    }

    public function getZryxesEffectInfoById($id){
        $info = $this->where(['id' => $id])->find();
        //企业信息
        $model = model("Gszls");
        $gszls = $model->getGszlsInfoByNeeq($info['neeq']);
        $info['gszls'] = $gszls;
        return $info;
    }
    public function getZryxesEffectByCode($neeq){
        $info = $this->where(['neeq' => $neeq , 'zryx_financing' => 0])->find();
        return $info;
    }
    public function setZryxesEffectExhibitionTrue($id){
        if(empty($id)){
            $this->error = '相关大宗项目id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['id' => $id])->update(['zryx_exhibition' => 1]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    public function setZryxesEffectExhibitionFalse($id){
        if(empty($id)){
            $this->error = '相关大宗项目id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['id' => $id])->update(['zryx_exhibition' => 0]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    public function createZryxesEffect($data){
        if(empty($data)){
            $this->error = '缺少相关修改数据';
            return false;
        }
        //验证数据信息
        $validate = validate('ZryxesEffect');
        $scene = isset($data['id']) ? 'edit' : 'add';
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $user = \app\console\service\User::getInstance()->getInfo();
            if(isset($data['id'])){
                $arr = [];
                $arr['sec_uri_tyshortname'] = $data['sec_uri_tyshortname'];
                $arr['trademethod'] = $data['trademethod'];
                $arr['sublevel'] = $data['sublevel'];
                $arr['new'] = $data['new'];
                $arr['direction'] = $data['direction'];
                $arr['price'] = $data['price'];
                $arr['num'] = $data['num'];
                $arr['enddate'] = $data['enddate'];
                $arr['isdiscount'] = $data['isdiscount'];
                $arr['name'] = $data['name'];
                $arr['contact'] = $data['contact'];
                $arr['accountment'] = $data['accountment'];
                $arr['share_des'] = $data['share_des'];
                $arr['update_time'] = time();
                $arr['update_uid'] = $user['uid'];
                $arr['img_id'] = $data['img_id'];
                $this->allowField(true)->where(['id' => $data['id']])->update($arr);
            } else {
                $info = $this->where(['neeq' => $data['neeq']])->find();
                if(empty($info)){
                    $data['secucode'] = $data['neeq'].'.OC';
                    $data['create_time'] = time();
                    $data['created_at'] = date('Y-m-d H:i:s' , time());
                    $data['create_uid'] = $user['uid'];
                    $this->allowField(true)->save($data);
                    $id = $this->getLastInsID();
//                    $filename = 'zr_'.$id.genRandomString(10).'.jpg';
//                    $this->where(['id' => $id])->update(['qr_code' => $filename]);
//                    $arr['qr_code'] = $filename;
                    $system = model("SystemConfig")->getSystemConfigInfo();
                    $result = createProEditQrcodeDirection($system['current'] , 'zryxes' , $id , '' , '');
                    if(!$result){
                        $this->error = '创建二维码指向失败';
                        return false;
                    } else {
                        $this->where(['id' => $id])->update(['qr_code' => $result]);
                    }
                    
//                    
//                    $weObj = new Wechat(config("WeiXinCg"));
//                    $access_token = getAccessTokenFromFile();
//                    $result = $weObj->checkAuth('', '', $access_token);
//                    $result = $weObj->getQRCode('zryxes&'.$id, 1);
//                    $result = $weObj->getQRUrl($result['ticket']);
//                    $result = getImage($result, config("QRCodePath"), $filename, 1);
//                    if($result['error']){
//                        $this->error = $result['msg'];
//                        return false;
//                    }
                } else {
                    $this->error = '该股票代码的大宗项目已存在';
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
    public function setZryxesEffectTopTrue($id){
        if(empty($id)){
            $this->error = '相关增发明细项目id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['id' => $id])->update(['stick' => 1]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    public function setZryxesEffectTopFalse($id){
        if(empty($id)){
            $this->error = '相关增发明细项目id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['id' => $id])->update(['stick' => 0]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }

    public function sendProjectInfo($id,$uid,$openId,$userType,$name){
        //开启事务
        $this->startTrans();
        try{
            $page_info = $this->getZryxesEffectInfoById($id);
            $info = $this->where(['id' => $id])->find();
            //$list = model("Member")->select();
            //$model = model("dict");
            //$industry = explode(',', $info['inc_industry']);
            //$inc_industry = '';
            //foreach ($industry as $key => $value) {
            //    $dict = $model->getDictInfoById($value);
            //    $inc_industry .= $dict['value'].' , ';
            //}
            //$info['inc_industry'] = trim($inc_industry , ' , ');
             $member = model("Member");
            if (0 !== $uid) {
                $member->where(['uid'=>$uid]);
            }
            if ('' !== $openId) {
                $member->where('openId','like','%'.$openId.'%');
            }
            if ('' !== $userType) {
                $member->where(['userType'=>$userType]);
            }
            
            $list = $member->select();
            $array = [
                'first' => ['value' => '您好，您有一个新项目上线!' , 'color' => '#173177'],
                'keyword1' => ['value' => $name , 'color' => '#0D0D0D'],
                'keyword2' => ['value' => $info['sec_uri_tyshortname'] , 'color' => '#0D0D0D'],
                'keyword3' => ['value' => $page_info['share_des'], 'color' => '#0D0D0D'],
                'keyword4' => ['value' => $page_info['gszls']['xyfl'], 'color' => '#0D0D0D'],
                'keyword5' => ['value' => "大宗转让" , 'color' => '#0D0D0D'],
                'remark' => ['value' => '请点击查看详情链接至大宗详情!' , 'color' => '#173177'],
            ];
            //$list = array(
                //array("uid"=>5,"openId"=>"osmRL1pOykkfyrelNMOr5G8wNnIc"),
                //array("uid"=>33,"userName"=>"再来一杯","openId"=>"o_5AJs5mZ3lacviQEMBxBFuAMuOw"),
                //array("uid"=>1903,"userName"=>"心向天空","openId"=>"o_5AJs3Ds1KUQ0fc0gL-83o9C-eo"),
            //);
            $model = model('Send');
            $send_id = $model->addSendInfo(['type' => 'zryxes', 'act_id' => $id]);
            //$unSub = [];
            //已关注&发送失败的用户
            $sendFailMember = [];
            //未关注的用户
            $unSubMember = [];
            foreach ($list as $key => $value) {
             $isFollow = isFollow($value['openId'],getAccessTokenFromFile());
                if($isFollow){
                   //sendTemplateMessage(config("new_project"), $value['openId'], $_SERVER['SERVER_NAME'].'/home/zryxes_effect_info/index/id/'.$info['id'], $array);
                   $re = sendTemplateMessage(config("new_project"), $value['openId'], $_SERVER['SERVER_NAME'].'/home/zryxes_effect_info/index/id/'.$info['id'], $array);
                   if(!$re){
                      $data['userName'] = $value['userName'];
                      $data['openId'] = $value['openId'];
                      $data['send_id'] = $send_id;
                      $data['created_at'] = date('Y-m-d H:i:s', time());
                      $data['type'] = 1;
                      $sendFailMember[] = $data;
                   }
                }else{
                   //$unSub[] = $value['uid'];
                     $data['userName'] = $value['userName'];
                   $data['openId'] = $value['openId'];
                   $data['send_id'] = $send_id;
                   $data['created_at'] = date('Y-m-d H:i:s', time());
                   $data['type'] = 2;
                   $unSubMember[] = $data;
                }
            }
            $allFailMember = array_merge($sendFailMember,$unSubMember);
            model('SendError')->addSendErrorInfo($allFailMember);
           // $ars_list = [];
           // foreach ($list as $key => $value) {
             //   $end = sendTemplateMessage(config("new_project"), $value['openId'], $_SERVER['SERVER_NAME'].'/home/zryxes_effect_info/index/id/'.$info['id'], $array);
             //   $end = getSendCodeMsg($end);
             //   $result = explode('-', $end);
             //   $result = $result[0] == 0 ? 'true' : 'false';
             //   $ars = ['userName' => $value['userName'], 'openId' => $value['openId'], 'send_id' => $send_id, 'content' => $end, 'result' => $result];
             //   $ars_list[] = $ars;
               // model('SendList')->addSendListInfo($ars);
                //if(!$end){
                //    $this->error .= $value['uid'];
                //}
            //}
            //$model = model('SendList');
            //$model->addSendListInfo($ars_list);
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
