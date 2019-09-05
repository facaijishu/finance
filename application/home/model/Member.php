<?php

namespace app\home\model;

// use think\Model;

class Member extends BaseModel
{
    public function setCollectionStudy($data){
        if(empty($data)){
            $this->error = '缺少文章数据';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $arr = [];
            $model = model("Member");
            $user = $model->where(['openId' => $data['openid']])->find();
            $collection_study = explode(',', $user['collection_study']);
            if(!in_array($data['id'], $collection_study)){
                array_push($collection_study, $data['id']);
                $coll = trim(implode(',', $collection_study), ',');
                $model->where(['openId' => $data['openid']])->update(['collection_study' => $coll]);
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
    
    public function getMemberInfoById($id){
        $result = $this->where(['uid' => $id])->find();
        return $result;
    }
    
    public function setMemberProSearch($text){
        $uid = session('FINANCE_USER.uid');
        $result = true;
        $info = $this->where(['uid' => $uid])->find();
        $arr = explode(',', $info['pro_search']);
        $search = array_slice($arr, 0, 10);
        if(!in_array($text, $arr)){
            array_push($search, $text);
            $search = array_slice($search, 0, 10);
            $str = trim(implode(',', $search), ',');
            session('FINANCE_USER.pro_search' , $str);
            $result = $this->where(['uid' => $uid])->update(['pro_search' => $str]);
        }
        return $result;
    }

    /**
	 * 用户登录验证
	 */
	public function checkLogin($wxUser){
		$where = array();
		$where['openId'] = $wxUser['openid'];
        $user = $this->where($where)->find();
		//如果存在，则进行登录，如果不存在，则进行注册
		if(!empty($user) && $user['openId']==$wxUser['openid']){
			if($user['status']==1){
				$data = array();
				$data['userName']  = $wxUser['nickname'];
				$data['lastTime']  = time();
				$data['lastIP'] = get_client_ip();
				// $data['userPhoto'] = $wxUser['headimgurl'];
                $data['userPhoto'] = $user['userPhoto']; 
		    	$this->where(['uid' => $user['uid']])->update($data);
			} else {
                //账号不能用,此情况暂时没有
            }
		}else {
            $data = array();
			$data['userType'] = 0;
            $data['role_type'] = 0;
            if($wxUser['superior'] != null){
                $info = $this->getMemberInfoById($wxUser['superior']);
                if($info['userType'] == 1 || $info['userType'] == 2){
                    $data['superior'] = $wxUser['superior'];
                    $arr = [];
                    $arr['uid'] = $wxUser['superior'];
                    $arr['integral'] = config("integral");
                    $arr['reason'] = '分享链接，带来注册用户：'.$wxUser['nickname'];
                    $arr['create_time'] = time();
                    $arr['openId'] = $wxUser['openid'];
                    model("IntegralRecord")->allowField(true)->save($arr);
                } else {
                    $data['superior'] = 0;
                }
            } else {
                $data['superior'] = 0;
            }
			$data['userName'] = $wxUser['nickname'];
			$data['userSex'] = $wxUser['sex']==0 ? 3 : $wxUser['sex']==1 ? 1 : 2;
            //第一次注册，用户身份为游客,真实姓名,电话,专属二维码地址,业务数量,需求数量默认为空
            $data['realName'] = '';
            $data['company'] = '';
            $data['company_jc'] = '';
            $data['position'] = '';
            $data['weixin'] = '';
            $data['email'] = '';
            $data['company_address'] = '';
            $data['website'] = '';
			$data['userPhone'] = '';
            $data['qr_code'] = '';
            $data['service_num'] = 0;
            $data['require_num'] = 0;
            
			$data['createTime'] = time();
			$data['CREATE_TIME'] = date('Y-m-d H:i:s' , time());
			$data['userPhoto'] = $wxUser['headimgurl'];
			$data['lastTime'] = time();
			$data['lastIP'] = get_client_ip();
			$data['openId'] = $wxUser['openid'];
            $data['status'] = 1;
            $data['person_label'] = '';
			$result = $this->allowField(true)->save($data);

		}
        $user = $this->where($where)->find()->toArray();        
		return $user;
	}
    
    public function showMemberDetailApi($uid=0,$from_uid=0)
    {
        $user = $this->where(['uid'=>$uid,'status'=>1])->find()->toArray();
        if (empty($user)) {
            $this->code = 208;
            $this->msg = '缺少用户uid参数';
            return $this->result('',$this->code,$this->msg);
        }

        $result = [];
        $result['userPhone']    = $user['userPhone'];
        $result['userPhoto']    = $user['userPhoto'];
        $result['realName']     = $user['realName'];
        $result['company_jc']   = $user['company_jc'];
        $result['position']     = $user['position'];
        $result['person_label'] = explode(',', $user['person_label']);
        
        $userFollow = model('Member')->where(['uid'=>$from_uid])->find();
        $result['follow_flg']         = $userFollow['is_follow'];
        
        //是否关注
            $fu = model('FollowUser')->where(['from_uid'=>$from_uid,'to_uid'=>$user['uid']])
                                     ->select()
                                     ->toArray();
            if (empty($fu)) {
                //未关注
                $result['is_follow'] = '关注';
            }else{
                if ($fu[0]['is_del'] == 1) {
                    //未关注
                    $result['is_follow'] = '关注';
                } 
               
                if ($fu[0]['is_del'] == 2) {
                    //已关注
                    $result['is_follow'] = '已关注';
                } 
            }
        //分享内容
        $result['share']['desc'] = 'FA財名片';
        $result['share']['title'] = $result['realName'];
        $result['share']['imgUrl'] = $result['userPhoto'];
        return $this->result($result);
    }
    
    /**
     * 收藏/取消收藏项目列表
     * @param 收藏项目ID $id
     * @param 收藏标签   $sign
     * @return 状态集合
     */
    public function setCollectProjecte($id , $sign){
        
        if(empty($id)){
            $this->code = 255;
            $this->msg = '缺少项目或资金的id';
            return $this->result('',$this->code,$this->msg);
        }
        if(empty($sign)){
            $this->code = 271;
            $this->msg = '缺少收藏标签';
            return $this->result('',$this->code,$this->msg);
        }
        //开启事务
        $this->startTrans();
        try{
            $arr  = [];
            $uid  = session('FINANCE_USER.uid');
            $user = model('Member')->getMemberInfoById($uid);
            session('FINANCE_USER',$user);
            
            
            $collection = explode(',', $user['collection_project']);
            if($sign == 'true'){
                if(in_array($id, $collection)){
                    $this->code = 272;
                    $this->msg  = '不能重复收藏';
                    return $this->result('',$this->code,$this->msg);
                } else {
                    array_push($collection, $id);
                    $coll   = trim(implode(',', $collection) , ',');
                    $model  = model("Member");
                    $model->where(['uid' => $user['uid']])->update(['collection_project' => $coll]);
                    $user   = $model->where(['uid' => $user['uid']])->find();
                    session('FINANCE_USER' , $user);
                }
            } else {
                if(in_array($id, $collection)){
                    foreach ($collection as $key => $value) {
                        if($value == $id){
                            unset($collection[$key]);
                        }
                    }
                    $coll   = trim(implode(',', $collection) , ',');
                    $model  = model("Member");
                    $model->where(['uid' => $user['uid']])->update(['collection_project' => $coll]);
                    $user   = $model->where(['uid' => $user['uid']])->find();
                    session('FINANCE_USER' , $user);
                } else {
                    $this->code = 273;
                    $this->msg  = '未收藏,不能取消收藏';
                    return $this->result('',$this->code,$this->msg);
                    
                }
            }
            // 提交事务
            $this->commit();
            return $this->result();
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            $this->code = 274;
            $this->msg  = '系统错误:'.$e->getMessage();
            return $this->result('',$this->code,$this->msg);
        }
    }
    
    /**
     * 收藏/取消项目需求列表
     * @param 项目需求ID $id
     * @param 收藏标签 $sign
     * @return 状态集合
     */
    public function setCollectProjectRequire($id , $sign){

        if(empty($id)){
            $this->code = 255;
            $this->msg  = '缺少项目或资金的id';
            return $this->result('',$this->code,$this->msg);
        }
        if(empty($sign)){
            $this->code = 271;
            $this->msg  = '缺少收藏标签';
            return $this->result('',$this->code,$this->msg);
        }
        //开启事务
        $this->startTrans();
        try{
            $arr  = [];
            $uid  = session('FINANCE_USER.uid');
            $user = model('Member')->getMemberInfoById($uid);
            session('FINANCE_USER',$user);
            
            $collection = explode(',', $user['collection_project_require']);
            if($sign == 'true'){
                if(in_array($id, $collection)){
                    $this->code = 272;
                    $this->msg  = '不能重复收藏';
                    return $this->result('',$this->code,$this->msg);
                } else {
                    array_push($collection, $id);
                    $coll   = trim(implode(',', $collection) , ',');
                    $model  = model("Member");
                    $model->where(['uid' => $user['uid']])->update(['collection_project_require' => $coll]);
                    $user   = $model->where(['uid' => $user['uid']])->find();
                    session('FINANCE_USER' , $user);
                }
            } else {
                if(in_array($id, $collection)){
                    foreach ($collection as $key => $value) {
                        if($value == $id){
                            unset($collection[$key]);
                        }
                    }
                    $coll   = trim(implode(',', $collection) , ',');
                    $model  = model("Member");
                    $model->where(['uid' => $user['uid']])->update(['collection_project_require' => $coll]);
                    $user   = $model->where(['uid' => $user['uid']])->find();
                    session('FINANCE_USER' , $user);
                } else {
                    $this->code = 273;
                    $this->msg  = '未收藏,不能取消收藏';
                    return $this->result('',$this->code,$this->msg);

                }
            }
            // 提交事务
            $this->commit();
            return $this->result();
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            $this->code = 274;
            $this->msg  = '系统错误:'.$e->getMessage();
            return $this->result('',$this->code,$this->msg);
        }
    }
    
    
    /**
     * 收藏/取消机构中心列表
     * @param 机构中心ID $id
     * @param 收藏标签 $sign
     * @return 状态集合
     */
    public function setCollectOrganize($id , $sign){
        if(empty($id)){
            $this->code = 255;
            $this->msg = '缺少项目或资金的id';
            return $this->result('',$this->code,$this->msg);
        }
        if(empty($sign)){
            $this->code = 271;
            $this->msg = '缺少收藏标签';
            return $this->result('',$this->code,$this->msg);
        }
        //开启事务
        $this->startTrans();
        try{
            $arr = [];
            $uid  = session('FINANCE_USER.uid');
            $user = model('Member')->getMemberInfoById($uid);
            session('FINANCE_USER',$user);
            
            $collection = explode(',', $user['collection_organize']);
            if($sign == 'true'){
                if(in_array($id, $collection)){
                    $this->code = 272;
                    $this->msg = '不能重复收藏';
                    return $this->result('',$this->code,$this->msg);
                } else {
                    array_push($collection, $id);
                    $coll = trim(implode(',', $collection) , ',');
                    $model = model("Member");
                    $model->where(['uid' => $user['uid']])->update(['collection_organize' => $coll]);
                    $user = $model->where(['uid' => $user['uid']])->find();
                    session('FINANCE_USER' , $user);
                }
            } else {
                if(in_array($id, $collection)){
                    foreach ($collection as $key => $value) {
                        if($value == $id){
                            unset($collection[$key]);
                        }
                    }
                    $coll = trim(implode(',', $collection) , ',');
                    $model = model("Member");
                    $model->where(['uid' => $user['uid']])->update(['collection_organize' => $coll]);
                    $user = $model->where(['uid' => $user['uid']])->find();
                    session('FINANCE_USER' , $user);
                } else {
                    $this->code = 273;
                    $this->msg = '未收藏,不能取消收藏';
                    return $this->result('',$this->code,$this->msg);
                }
            }
            // 提交事务
            $this->commit();
            return $this->result();
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            $this->code = 274;
            $this->msg = '系统错误:'.$e->getMessage();
            return $this->result('',$this->code,$this->msg);
        }
    }
    
    
    /**
     * 收藏/取消发布资金
     * @param 发布资金的ID $id
     * @param 收藏标签 $sign
     * @return 状态集合
     */
    public function setCollectOrganizeRequire($id , $sign){

        if(empty($id)){
            $this->code = 255;
            $this->msg  = '缺少项目或资金的id';
            return $this->result('',$this->code,$this->msg);
        }
        if(empty($sign)){
            $this->code = 271;
            $this->msg  = '缺少收藏标签';
            return $this->result('',$this->code,$this->msg);
        }
        //开启事务
        $this->startTrans();
        try{
            $arr  = [];
            $uid  = session('FINANCE_USER.uid');
            $user = model('Member')->getMemberInfoById($uid);
            session('FINANCE_USER',$user);
            
            $collection = explode(',', $user['collection_organize_require']);
            if($sign == 'true'){
                if(in_array($id, $collection)){
                    $this->code = 272;
                    $this->msg  = '不能重复收藏';
                    return $this->result('',$this->code,$this->msg);
                } else {
                    array_push($collection, $id);
                    $coll   = trim(implode(',', $collection) , ',');
                    $model  = model("Member");
                    $model->where(['uid' => $user['uid']])->update(['collection_organize_require' => $coll]);
                    $user   = $model->where(['uid' => $user['uid']])->find();
                    session('FINANCE_USER' , $user);
                }
            } else {
                if(in_array($id, $collection)){
                    foreach ($collection as $key => $value) {
                        if($value == $id){
                            unset($collection[$key]);
                        }
                    }
                    $coll   = trim(implode(',', $collection) , ',');
                    $model  = model("Member");
                    $model->where(['uid' => $user['uid']])->update(['collection_organize_require' => $coll]);
                    $user   = $model->where(['uid' => $user['uid']])->find();
                    session('FINANCE_USER' , $user);
                } else {
                    $this->code = 273;
                    $this->msg  = '未收藏,不能取消收藏';
                    return $this->result('',$this->code,$this->msg);
                }
            }
            // 提交事务
            $this->commit();
            return $this->result();
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            $this->code = 274;
            $this->msg = '系统错误:'.$e->getMessage();
            return $this->result('',$this->code,$this->msg);
        }
    }
    
    //用户电话联系该合伙人,该合伙人被电话联系次数+1
    public function editTelNumApi($id=0)
    {
        $re = $this->where(['uid'=>$id])->find()->toArray();
        if (empty($re)) {
            $this->code = 255;
            $this->msg = '该合伙人不存在';
            return $this->result('',$this->code,$this->msg);
        }
        $result = $this->where(['uid'=>$id])->setInc('tel_num');
        if ($result) {
            $this->code = 200;
            $this->msg = '更新电话联系次数成功';
            return $this->result('',$this->code,$this->msg);            
        }

        $this->code = 270;
        $this->msg = '更新电话联系次数失败';
        return $this->result('',$this->code,$this->msg);
    }
    
    //获取微信用户openid
    public function getOpenid($uid = 0)
    {
        $re = $this->where(['uid'=>$uid])->field('openId')->find();
        if ($re) {
            return $re['openId'];
        }
        return null;
    }
    
    //获取真实姓名
    public function getRealName($uid = 0)
    {
        $re = $this->where(['uid'=>$uid])->field('realName')->find();
        if ($re) {
            return $re['realName'];
        }
        return null;   
    }
    
    /**
     * 更新微信关注状态
     * @param 会员编号  $uid
     * @param 微信关注状态  $follow
     */
    public function updateFollow($openId,$follow){
        $mem = $this->where(['openId'=>$openId])->field('is_follow')->find();
        if($mem['is_follow']!==$follow){
            $data = [];
            $data['is_follow']      = $follow;
            $res = $this->allowField(true)->isUpdate(true)->save($data, ['openId' => $openId]);
        } 
    }
    
    /**
     * 更新微信关注状态
     * @param 会员编号  $uid
     * @param 微信关注状态  $follow
     */
    public function getNewInfo($openId){
        $result = $this->where(['openId' => $openId])->find();
        return $result;
    }
}