<?php

namespace app\home\model;

// use think\Model;

class Through extends BaseModel
{
    public function getThroughInfoById($id){
        if(empty($id)){
            $this->error = '用户id不存在';
            return false;
        }
        $result = $this->where(['uid' => $id])->find();
        return $result;
    }
  
    public function createThrough($data){
        if(empty($data)){
            $this->error = '认证数据不存在';
            return false;
        }
        $user = session('FINANCE_USER');
        $uid = $user['uid'];
        $info = $this->where(['uid' => $uid])->find();
        //验证数据信息
        $validate = validate('Through');
        $scene = empty($info) ? 'add' : 'edit';
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }

        //开启事务
        $this->startTrans();
        try{
            if(!empty($info)){
                model("Member")->where(['uid' => $uid])->update(['realName' => $data['realname']]);
                $arr = [];
                $arr['role_type'] = $data['role_type'];
                $arr['realName'] = $data['realname'];
                $arr['card'] = rtrim($data['card'],',');
                if(isset($data['phone'])){
                    $arr['phone'] = $data['phone'];
                    model("Member")->where(['uid' => $uid])->update(['userPhone' => $data['phone'] ,'userType' => 1]);
                }
                $arr['updateTime'] = time();
                $arr['status'] = 0;
                $this->where(['uid' => $uid])->update($arr);
            } else {
                model("Member")->where(['uid' => $uid])->update(['realName' => $data['realname']]);
                $arr = [];
                $arr['uid'] = $uid;
                $arr['role_type'] = $data['role_type'];
                $arr['realName'] = $data['realname'];
                //$arr['email'] = $data['email'];
                //$arr['company'] = $data['company'];
                //$arr['position'] = $data['position'];
                $arr['card'] = rtrim($data['card'],',');
                if(isset($data['phone'])){
                    $arr['phone'] = $data['phone'];
                    model("Member")->where(['uid' => $uid])->update(['userPhone' => $data['phone'] ,'userType' => 1]);
                }
                $arr['createTime'] = time();
                $arr['updateTime'] = time();
                $arr['throughTime'] = 0;
                $arr['lastTime'] = 0;
                $arr['status'] = 0;
                $this->allowField(true)->save($arr);
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
    /**
     *
     *
     *
     * FA財4.0API
     */
    //获取用户访问公众号的身份
    public function getRoleTypeByUid($uid = 0)
    {
        if(!$uid){

            $this->error = '缺少用户id参数';
            return false;
        }
        $role_type = $this->field('uid,role_type')->where(['uid'=>$uid])->find();
        if($role_type['role_type'] == 0 || empty($role_type)){

            // $this->msg = '游客';

            return $role_type;
        }

        if($role_type['role_type'] == 1){
 
            // $this->msg = '资金方';

            return $role_type;
        }
        
        if($role_type['role_type'] == 2){

            // $this->msg = '项目方';

            return $role_type;
        }
    }

    
    //注册为合伙人
    public function createThroughApi($data)
    {
        
        $info = $this->where(['uid' => $data['uid'],'status'=>1])->find();
        if(!empty($info)){
            $this->error = '该用户已注册';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            
            model("Member")->where(['uid' => $data['uid']])->update(['realName' => $data['real_name']]);
            $arr = [];
            $arr['role_type'] = $data['role_type'];
            $arr['uid'] = $data['uid'];
            $arr['realName'] = $data['real_name'];
            if(isset($data['phone'])){
                $arr['phone'] = $data['phone'];
                model("Member")->where(['uid' => $data['uid']])->update(['userPhone' => $data['phone'] ,'userType' => 1]);
            }
            $arr['createTime'] = time();
            $arr['updateTime'] = time();
            $arr['status']     = 1;
            $this->allowField(true)->save($arr);

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
     * 完善或修改个人信息
     * @param 会员信息集合 $data
     * @param 会员编号 $uid
     * @return unknown
     */
    public function perfectPersonalInfoApi($data = [],$uid = 0)
    {    
        
        if (!$uid || !is_numeric($uid)) {
            $this->code = 208;
            $this->msg = '缺少用户id参数';
            return $this->result('',$this->code,$this->msg);
        }
        $this->startTrans();
        try {
            //如果修改手机号,发送了短信验证码
            if (!empty($data['code'])) {
                $modelM   = model("MemberCode");
                $res      = $modelM->isValidCode($uid,$data['code']);
                
                //验证成功
                if($res['code']==200){
                //if($data['code'] == session('code')){
                    //session('code', null);
                    //更新认证信息
                    $this->allowField(true)->save($data,['uid'=>$uid]);
                    
                    //添加integral_record认证时间
                    $userinfo        = model("Member")->where(['uid' => $uid])->find();
                    $integral_record = model("IntegralRecord")->where(['openId' => $userinfo['openId']])->find();
                    if($integral_record['through'] == ''){
                        $today = date('Y-m-d H:i:s' , time());
                        model("IntegralRecord")->where(['openId' => $userinfo['openId']])->update(['through' => $today]);
                        //发送邀请成功的推送消息
                        if($userinfo['superior']>0){
                            $userinfo1 = model("Member")->where(['uid' => $userinfo['superior']])->find();
                            $array1 = [
                                'first'     => ['value' => 'FA財有新的金融合伙人加入!' , 'color' => '#173177'],
                                'keyword1'  => ['value' => $userinfo1['realName'] , 'color' => '#777777'],
                                'keyword2'  => ['value' => $userinfo['realName'] , 'color' => '#777777'],
                                'remark'    => ['value' => '感谢您的推荐，推荐越多奖励越多!' , 'color' => '#173177'],
                            ];
                            $end1 = sendTemplateMessage(config("recommend_success"), $userinfo1['openId'], $_SERVER['SERVER_NAME'].'/home/my_center/index', $array1);
                          }
                    }
                    
                    //更新账户信息
                    model('Member')->where(['uid'=>$uid])->update([
                        'realName'          => $data['realName'],
                        'company'           => $data['company'],
                        'company_jc'        => $data['company_jc'],
                        'position'          => $data['position'],
                        'weixin'            => $data['weixin_id'],
                        'email'             => $data['email'],
                        'company_address'   => $data['company_address'],
                        'website'           => $data['website'],
                        'userPhone'         => $data['phone'],
                        'userPhoto'         => $data['my_head'],
                        'userType'          => 2,
                        'role_type'         => $data['role_type'],
                        'person_label'      => $data['tag_name']
                    ]);
                  
                     //如果设置了个性标签
                    if (isset($data['tag_name'])) {
                        
                        if (strpos($data['tag_name'],',') !== false) {
                            $tag_name = explode(',', $data['tag_name']);
                            $arr = [];
                            foreach ($tag_name as $v) {
                                $arr[] = [
                                   'tag_name'=>$v,
                                   'uid'=>$uid,
                                   'create_time'=>time(),
                                   'delete_time'=>time()
                                ];
                            }
                            model('TagsUser')->saveAll($arr);
                        } else {
                            $arr = [
                               'tag_name'=>$data['tag_name'],
                                'uid'=>$uid,
                                'create_time'=>time(),
                                'delete_time'=>time()
                            ];
                            model('TagsUser')->save($arr);
                        }
                        model('Member')->where(['uid'=>$uid])->update(['person_label'=>$data['tag_name']]);
                    }
                    
                    
                    
                    $info = model('Member')->where(['uid'=>$uid])->find();
                    //查询是否生成二维码
                    if (!$info['qr_code']) {
                        //如果没有生成二维码,生成二维码
                        //生成专属二维码
                        $system = model("SystemConfig")->getSystemConfigInfo();
                        $result = createUserEditQrcodeDirection($system['current'] , 'Member' , $uid , '' , '');
                        $qrcode_info = model('QrcodeDirection')->field('qrcode_path')->where(['id'=>$result])->find();

                        //把二维码路径更新到用户信息中qr_code
                        $qrcode_path = config('view_replace_str')['__DOMAIN__'].'through/code/'.$qrcode_info['qrcode_path'];
                        //更新member表二维码路径
                        model('Member')->where(['uid'=>$uid])->update(['qr_code'=>$qrcode_path]);
                        $info = model('Member')->where(['uid'=>$uid])->find();
                    }
                    
                    //将更新后的用户信息查询出,并存到session
                    session('FINANCE_USER' , $info);
                    
                    $this->commit();
                    $this->code = 200;
                    $this->msg = '完善或修改个人信息成功';
                    return $this->result('',$this->code,$this->msg);

                }else{
                    $this->code = 248;
                    $this->msg = $res['msg'];
                    return $this->result('',$this->code,$this->msg);
                }

            } else {
                //更新认证信息
                $this->allowField(true)->save($data,['uid'=>$uid]);
                
                //添加integral_record认证时间
                $userinfo        = model("Member")->where(['uid' => $uid])->find();
                $integral_record = model("IntegralRecord")->where(['openId' => $userinfo['openId']])->find();
                if($integral_record['through'] == ''){
                    $today = date('Y-m-d H:i:s' , time());
                    model("IntegralRecord")->where(['openId' => $userinfo['openId']])->update(['through' => $today]);
                }
                
                //更新账户信息 
                model('Member')->where(['uid'=>$uid])->update([
                        'realName'          => $data['realName'],
                        'company'           => $data['company'],
                        'company_jc'        => $data['company_jc'],
                        'position'          => $data['position'],
                        'weixin'            => $data['weixin_id'],
                        'email'             => $data['email'],
                        'company_address'   => $data['company_address'],
                        'website'           => $data['website'],
                        'userPhone'         => $data['phone'],
                        'userPhoto'         => $data['my_head'],
                        'userType'          => 2,
                        'role_type'         => $data['role_type'],
                        'person_label'      => $data['tag_name'],
                    ]);


                    //如果设置了个性标签
                    if (isset($data['tag_name'])) {
                           $data['tag_name'] = trim($data['tag_name'],',');
                        if (strpos($data['tag_name'],',') !== false) {
                            $tag_name = explode(',', $data['tag_name']);
                            $arr = [];
                            foreach ($tag_name as $v) {
                                $arr[] = [
                                   'tag_name'=>$v,
                                   'uid'=>$uid,
                                   'create_time'=>time(),
                                   'delete_time'=>time()
                                ];
                            }
                            model('TagsUser')->saveAll($arr);
                        } else {
                            $arr = [
                               'tag_name'=>$data['tag_name'],
                                'uid'=>$uid,
                                'create_time'=>time(),
                                'delete_time'=>time()
                            ];
                            model('TagsUser')->save($arr);
                        }
                         model('Member')->where(['uid'=>$uid])->update(['person_label'=>$data['tag_name']]);
                    }
                
                //查询是否生成二维码
                $qr = model('Member')->field('qr_code')->where(['uid'=>$uid])->find();
                if (!$qr['qr_code']) {
                    //如果没有生成二维码,生成二维码
                    //生成专属二维码
                    $system = model("SystemConfig")->getSystemConfigInfo();
                    $result = createUserEditQrcodeDirection($system['current'] , 'Member' , $uid , '' , '');
                    $qrcode_info = model('QrcodeDirection')->field('qrcode_path')->where(['id'=>$result])->find();

                    //把二维码路径更新到用户信息中qr_code
                    $qrcode_path = config('view_replace_str')['__DOMAIN__'].'through/code/'.$qrcode_info['qrcode_path'];
                    //更新member表二维码路径
                    model('Member')->where(['uid'=>$uid])->update(['qr_code'=>$qrcode_path]);
                      
                }
                //将更新后的用户信息查询出,并拼接完整的路径存到session
                $info = model('Member')->where(['uid'=>$uid])->find();  
                session('FINANCE_USER' , $info);

                $this->commit();
                $this->code = 200;
                $this->msg = '完善或修改个人信息成功';
                return $this->result('',$this->code,$this->msg);
            }
                  
        } catch (\Exception $e) {
            $this->rollback();
            $this->code = 253;
            $this->msg = '完善或修改个人信息失败 '.$e->getMessage();
            return $this->result('',$this->code,$this->msg);
        }
    }
    //首页-搜索-人脉
    public function getIndexSearchConnections($keyword = '',$page = 1,$uid = 0,$length = 0,$offset = 0)
    {

        
        $match_label = '';//人脉匹配的所有条件

        //6.人脉
        $tr = $this->alias('t')
                ->join('Member m','t.uid=m.uid')
                ->field([
                    't.uid',
                    'm.userPhoto',
                    't.realName',
                    't.company_jc',
                    't.position',
                    't.type',
                ])
                ->order('CONVERT( t.realName USING gbk ) COLLATE gbk_chinese_ci ASC')
                ->where(['t.status'=>1]);
        //组装人脉用户名&&个性标签条件
                $match_label .= 't.realName like "%'.$keyword.'%" or '; 
                //根据模糊关键词匹配tags_user表中的个性标签,返回对应的用户uid
                $tu = model('TagsUser');
                $uid_arr = $tu->field('uid')
                          ->where('tag_name like "%'.$keyword.'%"')
                          ->select()
                          ->toArray();
                if(!empty($uid_arr)){
                    //组装根据人脉个性标签条件匹配到的合伙人uid
                    $match_label .= 't.uid in ('.implode(',',array_column($uid_arr,'uid')).') or ';
                }else{
                    $match_label .= 't.uid in ("") or ';
                }

                //执行查询人脉
                $through_list = $tr->where(rtrim($match_label,' or '))
                                   ->limit($offset,$length)
                                   ->select()
                                   ->toArray();
                if (empty($through_list)) {
                    $this->code = 214;
                    $this->msg = '抱歉没有发现的相关内容';
                    return $this->result('',$this->code,$this->msg);
                }

               
                   //查询当前用户关注的合伙人(未取关)
                        $to_uid_arr = [];
                        $to_uid = model('FollowUser')->field('to_uid')
                                                 ->where(['from_uid'=>$uid,'is_del'=>2])
                                                 ->select()
                                                 ->toArray();
                      
                        if (!empty($to_uid)) {
                            $to_uid_arr= array_column($to_uid, 'to_uid');
                        }
                        foreach ($through_list as &$each_through) {
                            //是否关注
                            if (in_array($each_through['uid'], $to_uid_arr)) {
                                $each_through['is_follow'] = '已关注';
                            }else{
                                $each_through['is_follow'] = '关注';
                            }
                        }
                          

               return $this->result($through_list); 
    }
}