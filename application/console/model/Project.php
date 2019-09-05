<?php

namespace app\console\model;

use think\Model;
use sys\Wechat;

class Project extends Model
{
    /**
     * 获取项目详细信息
     * @param 项目ID $id
     * @return boolean|unknown
     */
    public function getProjectInfoById($id){
        if(empty($id)){
            $this->error = '该项目id不存在';
            return false;
        }
        $result = $this->where(['pro_id' => $id])->find();
        return $result;
    }
    
    /**
     * 设置项目状态
     * @param 项目ID、 $id
     * @param 项目状态  $status 1：草稿; 2：融资中; 3：结束
     * @return boolean|数据集合
     */
    public function setProjectStatus($id , $status){
        if(empty($id)){
            $this->error = '该项目id不存在';
            return false;
        }
        if(!is_numeric($status)){
            $this->error = '项目状态不为整数';
            return false;
        }
        $data = [];
        $data['status'] = $status;
        $result = $this->where(['pro_id' => $id])->update($data);
        return $result;
    }
    
    /**
     * 项目排序显示设置
     * @param 数据集合 $data
     * @return boolean
     */
    public function setProjectOrderList($data){
        if(empty($data)){
            $this->error = '缺少相关数据';
            return false;
        }
        //验证数据信息
        $validate = validate('ProjectHot');
        $scene = isset($data['id']) ? 'edit' : 'add';
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['pro_id' => $data['id']])->update(['list_order' => $data['list_order']]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    
    /**
     * 保存项目
     * @param 项目数据集合 $data
     * @return boolean
     */
    public function createProjectDraft($data){
        if(empty($data)){
            $this->error = '项目信息不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            //企业亮点
            $img        = explode(',' , trim($data['enterprise_url'], ','));
            $material   = '';
            foreach ($img as $key => $value) {
                $arr       = explode('-', $value);
                $material .= $arr[0].',';
            }
            $data['enterprise_url'] = trim($material, ',');
            
            //公司简介
            $img      = explode(',' , trim($data['company_url'], ','));
            $material = '';
            foreach ($img as $key => $value) {
                $arr       = explode('-', $value);
                $material .= $arr[0].',';
            }
            $data['company_url'] = trim($material, ',');
            
            //核心产品
            $img      = explode(',' , trim($data['product_url'], ','));
            $material = '';
            foreach ($img as $key => $value) {
                $arr       = explode('-', $value);
                $material .= $arr[0].',';
            }
            $data['product_url'] = trim($material, ',');
            
            //行业分析
            $img      = explode(',' , trim($data['analysis_url'], ','));
            $material = '';
            foreach ($img as $key => $value) {
                $arr       = explode('-', $value);
                $material .= $arr[0].',';
            }
            $data['analysis_url'] = trim($material, ',');
            
            //所属行业
            $data['inc_industry'] = trim($data['inc_industry'], ',');
            $dict = model("Dict")->getDictInfoById($data['inc_industry']);
            $data['inc_top_industry'] = $dict['fid'];
            
            //所属业务
            $data['inc_sign']     = trim($data['inc_sign'], ',');
            $dict = model("Dict")->getDictInfoById($data['inc_sign']);
            $data['inc_top_sign'] = $dict['fid'];
            
            //所属区域
            $data['inc_area']     = trim($data['inc_area'], ',');
            
            $data['build_time']   = strtotime($data['build_time']);
            $data['last_time']    = time();
            if(isset($data['pro_id'])){
                $this->allowField(true)->save($data ,['pro_id' => $data['pro_id']]);
            } else {
                $data['create_time']  = time();
                $data['status']       = 1;
                
                $info   = \app\console\service\User::getInstance()->getInfo();
                $data['create_uid']   = $info['uid'];
                
                $this->allowField(true)->save($data);
                $id     = $this->getLastInsID();
                
                $system = model("SystemConfig")->getSystemConfigInfo();
                $result = createProEditQrcodeDirection($system['current'] , 'project' , $id , '' , '');
                if(!$result){
                    $this->error = '创建二维码指向失败';
                    return false;
                } else {
                    $this->where(['pro_id' => $id])->update(['qr_code' => $result]);
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
    
    
    public function createBoutiqueProject($id){
        if(empty($id)){
            $this->error = '大宗转让项目不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $info = model("ZryxesEffect")->getZryxesEffectInfoById($id);
            $info1 = $this->where(['stock_code' => $info['neeq']])->find();
            if(empty($info1)){
                model("ZryxesEffect")->where(['id' => $id])->update(['boutique' => 1]);
                $user = \app\console\service\User::getInstance()->getInfo();
                $system = model("SystemConfig")->getSystemConfigInfo();
                $arr = [];
                $arr['pro_name'] = $info['sec_uri_tyshortname'];
                $arr['stock_code'] = $info['neeq'];
                $arr['stock_name'] = $info['sec_uri_tyshortname'];
                $arr['inc_sign'] = 15;
                $arr['capital_plan'] = 5;
                if($info['trademethod'] == '协议'){
                    $arr['transfer_type'] = 7;
                } else {
                    $arr['transfer_type'] = 8;
                }
                if($info['sublevel'] == '基础层'){
                    $arr['hierarchy'] = 10;
                } else {
                    $arr['hierarchy'] = 9;
                }
                $arr['contacts'] = $info['name'];
                if($info['contact'] == '电话'){
                    $arr['contacts_tel'] = $info['accountment'];
                }
                $arr['create_time'] = time();
                $arr['last_time'] = time();
                $arr['create_uid'] = $user['uid'];
                $arr['status'] = 1;
                $this->allowField(true)->save($arr);
                $pro_id = $this->getLastInsID();
                $result = createProEditQrcodeDirection($system['current'] , 'project' , $pro_id , '' , '');
                if(!$result){
                    $this->error = '创建二维码指向失败';
                    return false;
                } else {
                    $this->where(['pro_id' => $pro_id])->update(['qr_code' => $result]);
                }
            } else {
                $pro_id = $info1['pro_id'];
                
                model("ZryxesEffect")->where(['id' => $id])->update(['boutique' => 1]);
            }
            // 提交事务
            $this->commit();
            return $pro_id;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    public function stopBoutiqueProject($id){
        if(empty($id)){
            $this->error = '大宗转让项目不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $info = model("ZryxesEffect")->getZryxesEffectInfoById($id);
            $info1 = $this->where(['stock_code' => $info['neeq']])->find();
            if(!empty($info1)){
                $pro_id = $info1['pro_id'];
                model("ZryxesEffect")->where(['id' => $id])->update(['boutique' => 0]);
            } 
            // 提交事务
            $this->commit();
            return $pro_id;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    public function createProject($data){
        if(empty($data)){
            $this->error = '缺少相关数据';
            return false;
        }
        //验证数据信息
        $validate = validate('Project');
        $scene = isset($data['pro_id']) ? 'edit' : 'add';
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }

        //开启事务
        $this->startTrans();
        try{
            $img = explode(',' , trim($data['enterprise_url'], ','));
            $material = '';
            foreach ($img as $key => $value) {
                $arr = explode('-', $value);
                $material .= $arr[0].',';
            }
            $data['enterprise_url'] = trim($material, ',');
            $img = explode(',' , trim($data['company_url'], ','));
            $material = '';
            foreach ($img as $key => $value) {
                $arr = explode('-', $value);
                $material .= $arr[0].',';
            }
            $data['company_url'] = trim($material, ',');
            $img = explode(',' , trim($data['product_url'], ','));
            $material = '';
            foreach ($img as $key => $value) {
                $arr = explode('-', $value);
                $material .= $arr[0].',';
            }
            $data['product_url'] = trim($material, ',');
            $img = explode(',' , trim($data['analysis_url'], ','));
            $material = '';
            foreach ($img as $key => $value) {
                $arr = explode('-', $value);
                $material .= $arr[0].',';
            }
            $data['analysis_url'] = trim($material, ',');
            
            //所属行业
            $data['inc_industry'] = trim($data['inc_industry'], ',');
            $dict = model("Dict")->getDictInfoById($data['inc_industry']);
            $data['inc_top_industry'] = $dict['fid'];
            
            //所属业务
            $data['inc_sign']     = trim($data['inc_sign'], ',');
            $dict = model("Dict")->getDictInfoById($data['inc_sign']);
            $data['inc_top_sign'] = $dict['fid'];
            
            //所属区域
            $data['inc_area']     = trim($data['inc_area'], ',');
            
            $data['last_time']    = time();
            
            $data['build_time']   = strtotime($data['build_time']);
            $data['status']       = 2;
            
            if(isset($data['pro_id'])){
                $arr = [];
                $arr = $data;
                $this->allowField(true)->save($arr , ['pro_id' => $data['pro_id']]);
            } else {
                $data['create_time']    = time();
                
                $info = \app\console\service\User::getInstance()->getInfo();
                $data['create_uid']     = $info['uid'];
                $this->allowField(true)->save($data);
                $id = $this->getLastInsID();
                
                $system = model("SystemConfig")->getSystemConfigInfo();
                $result = createProEditQrcodeDirection($system['current'] , 'project' , $id , '' , '');
                if(!$result){
                    $this->error = '创建二维码指向失败';
                    return false;
                } else {
                    $this->where(['pro_id' => $id])->update(['qr_code' => $result]);
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
    public function stopProject($id){
        //开启事务
        $this->startTrans();
        try{
            $this->where(['pro_id' => $id])->update(['flag' => -1]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    public function startProject($id){
        //开启事务
        $this->startTrans();
        try{
            $this->where(['pro_id' => $id])->update(['flag' => 1]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    
    /**
     * 项目发送微信消息推送
     * @param 项目ID $id 字符串多个用户英文逗号分隔
     * @param 用户ID $uid 字符串多个用户英文逗号分隔
     * @param 发送对象身份类型  $userType 99：全部用户   0：游客  1：绑定用户  2：合伙人
     * @param 项目联系对象 $name
     * @return boolean
     */
    public function sendProjectInfo($id,$uid,$userType,$name){
        //开启事务
        $this->startTrans();
        try{
            
            $map = [];
            
            //获取发送对象的集合
            $member = model("Member");
            //发送UID不为空的话则优先UID的发送内容
            if($uid !==""){
                if(strpos($uid,',') !== false){ 
                    $map['uid'] = array('in',$uid);
                }else{
                    $map['uid'] = $uid;
                }
            }else{
                //发送对象为全员
                if($userType==99){
                    $map = [];
                }else{
                    $map['userType'] = $userType;
                }
            }
            
            $map['is_follow'] = 1;
            
            $list   = $member->where($map)->select();
            $count  = count($list);
            if($count>0){
                //获取项目的详细信息
                $info = $this->where(['pro_id' => $id])->find();
                
                //获取项目所属行业
                $model          = model("dict");
                /*$industry       = explode(',', $info['inc_industry']);
                $inc_industry   = '';
                foreach ($industry as $key => $value) {
                    $dict = $model->getDictInfoById($value);
                    $inc_industry .= $dict['value'].' , ';
                }
                $info['inc_industry'] = trim($inc_industry , ' , ');*/
                
                $dict = $model->getDictInfoById($info['inc_industry']);
                $info['inc_industry'] = $dict['value'];
                
                $top_dict = $model->getDictInfoById($info['inc_top_industry']);
                $info['inc_top_industry'] = $top_dict['value'];
                
                //项目联系人信息
                if($name ===""){
                    $name = $info['contacts']."（".$info['contacts_tel']."）";
                }
                
                //消息主体内容
                $array = [
                    'first'    => ['value' => '您好，您有一个新项目上线!' , 'color' => '#173177'],
                    'keyword1' => ['value' => $name , 'color' => '#777777'],
                    'keyword2' => ['value' => $info['pro_name'] , 'color' => '#777777'],
                    'keyword3' => ['value' => $info['introduction'] , 'color' => '#777777'],
                    'keyword4' => ['value' => $info['inc_top_industry']."-".$info['inc_industry'] , 'color' => '#777777'],
                    'keyword5' => ['value' => $info['need'] , 'color' => '#777777'],
                    'remark'   => ['value' => '请点击详情查看!' , 'color' => '#173177'],
                ];
                $model   = model('Send');
                $send_id = $model->addSendInfo(['type' => 'project', 'act_id' => $id]);
                wechatLog("项目ID为".$id."的微信消息推送开始");
                foreach ($list as $key => $value) {
                    $re = sendTemplateMessage(config("new_project"), $value['openId'], $_SERVER['SERVER_NAME'].'/home/project_info/index/id/'.$info['pro_id'], $array);
                    wechatLog("用户UID为".$value['uid']."的推送结果为：".json_encode($re));
                }
                wechatLog("项目ID为".$id."的微信消息推送结束");
                
                // 提交事务
                $this->commit();
                return true;
            }else{
                return false;
            }
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
}
