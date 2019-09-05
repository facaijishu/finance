<?php

namespace app\console\model;

use think\Model;

class Through extends Model
{   
    protected $resultSetType = 'collection';
    public function getThroughInfoById($id){
        $result = $this->alias("t")
                ->join("Member m" , "t.uid=m.uid")
                ->where(['t.t_id' => $id])
                ->field("t.*,m.superior,m.uid,m.person_label")
                ->find();
        // $result = $this->query();
        if(stripos($result['card'], ',')){
             $result['card'] = explode(',', $result['card']);
        }else{
             $result['card'] = [$result['card']];
        }
        // dump($result);die;
        $result['createTime'] = date('Y-m-d H:i:s' , $result['createTime']);
        $result['superior'] = $result['superior'] == 0 ? '暂无' : $result['superior'];
        if($result['superior'] != 0){
            $info = $this->where(['uid' => $result['superior']])->find();
            $result['superior'] = $info['realName'];
        }
        if($result['status'] == 0){
            $result['status_des'] = '未审核';
        }elseif ($result['status'] == -1) {
            $result['status_des'] = '审核失败';
        } else {
            $result['status_des'] = '已审核';
        }
        if($result['status'] == 1){
            $num = [];
            $model = model("Member");
            $all = $model->where(['superior' => $result['uid']])->select();
            $a = $this->getStatisticsData($all);
            $a_num = count($all);
            $time = date('Y-m' , time());
            $now = $model->where('CREATE_TIME','like',$time.'%')->where(['superior' => $result['uid']])->select();
            $b = $this->getStatisticsData($now);
            $b_num = count($now);
            $time = date('Y-m' , strtotime('-1 month'));
            $prev = $model->where('CREATE_TIME','like',$time.'%')->where(['superior' => $result['uid']])->select();
            $c = $this->getStatisticsData($prev);
            $c_num = count($prev);
            $time = date('Y-m-d' , strtotime('-1 day'));
            $yesterday = $model->where('CREATE_TIME','like',$time.'%')->where(['superior' => $result['uid']])->select();
            $d = $this->getStatisticsData($yesterday);
            $d_num = count($yesterday);
            $num = [['游客',$a[0],$c[0],$b[0],$d[0]] , ['绑定用户',$a[1],$c[1],$b[1],$d[1]] , ['认证合伙人',$a[2],$c[2],$b[2],$d[2]] , ['合计',$a_num,$c_num,$b_num,$d_num]];
            $result['num'] = $num;
        }
        $model = model("ThroughDefaultInfo");
        $reason = $model->where(['uid' => $result['uid']])->select();
        $model = model("User");
        foreach ($reason as $key => $value) {
            $user = $model->getUserById($value['create_uid']);
            $reason[$key]['create_user'] = $user['real_name'];
        }
        $result['reason'] = $reason;

        //1.业务类型(1级+2级)
        $service_type = $this->alias('th')
             ->join('DictLabel dl','th.uid=dl.uid')
             ->join('Dict d','d.id=dl.dict_id')
             ->join('DictType dt','d.dt_id=dt.dt_id')
             ->field([
                'dl.dict_fid',
                'group_concat(d.value)'=>'service_name',
             ])
             ->where([
                'th.t_id'=>$id,
                'dl.is_del'=>2,
                'd.status'=>1,
                'dt.sign'=>'service_type'
             ])
             ->group('dict_fid')
             ->select()
             ->toArray();
        
        if (!empty($service_type)) {
            foreach ($service_type as $k =>$each_service_type) {
                if ($each_service_type['dict_fid']>0) {
                    $father = model('Dict')->field('value')
                                           ->where(['id'=>$each_service_type['dict_fid']])
                                           ->find();
                    if (!empty($father)) {
                        $service_type[$k]['father'] = $father['value'];
                    }
                    
                } 
                
            }
            $result['service_type'] = $service_type;
        }else{
            $result['service_type'] = []; 
        } 
        //2.行业类型(1级+2级)
        $industry = $this->alias('th')
                         ->join('DictLabel dl','th.uid=dl.uid')
                         ->join('Dict d','d.id=dl.dict_id')
                         ->join('DictType dt','d.dt_id=dt.dt_id')
                         ->field([
                            'dl.dict_fid',
                            'group_concat(d.value)'=>'industry_name',
                         ])
                         ->where([
                            'th.t_id'=>$id,
                            'dl.is_del'=>2,
                            'd.status'=>1,
                            'dt.sign'=>'industry'
                         ])
                         ->group('dict_fid')
                         ->select()
                         ->toArray();
        if (!empty($industry)) {
            foreach ($industry as $k =>$each_industry) {
                if ($each_industry['dict_fid']>0) {
                    $father = model('Dict')->field('value')
                                           ->where(['id'=>$each_industry['dict_fid']])
                                           ->find();
                    if (!empty($father)) {
                        $industry[$k]['father'] = $father['value'];
                    }
                    
                } 
                
            }
            $result['industry'] = $industry;
        }else{
            $result['industry'] = [];
        } 
        
        //3.投融规模(1级)
            $size = $this->alias('th')
                         ->join('DictLabel dl','th.uid=dl.uid')
                         ->join('Dict d','d.id=dl.dict_id')
                         ->join('DictType dt','d.dt_id=dt.dt_id')
                         ->field([
                            'dl.uid',
                            'group_concat(d.value)'=>'size_name',
                         ])
                         ->where([
                            'th.t_id'=>$id,
                            'dl.is_del'=>2,
                            'd.status'=>1,
                            'dt.sign'=>'size'
                         ])
                         ->group('dl.uid')
                         ->select()
                         ->toArray();
        if (!empty($size)) {
            $result['size'] = $size;
        } else {
            $result['size'] = [];
        }
        
        //3.省份(1级)
            $province = $this->alias('th')
                         ->join('DictLabel dl','th.uid=dl.uid')
                         ->join('Dict d','d.id=dl.dict_id')
                         ->join('DictType dt','d.dt_id=dt.dt_id')
                         ->field([
                            'dl.uid',
                            'group_concat(d.value)'=>'province_name',
                         ])
                         ->where([
                            'th.t_id'=>$id,
                            'dl.is_del'=>2,
                            'd.status'=>1,
                            'dt.sign'=>'to_province'
                         ])
                         ->group('dl.uid')
                         ->select()
                         ->toArray();
        if (!empty($province)) {
            $result['province'] = $province;
        } else {
            $result['province'] = [];
        }
        
        //用户角色
        $result['role_type']= checkRoleType($result['role_type']);
        return $result;
    }

    public function setThroughSuccess($id){
        //开启事务
        $this->startTrans();
        try{
            $data = [];
            $data['throughTime']    = time();
            $data['THROUGH_TIME']   = date('Y-m-d H:i:s' , time());
            $data['status']         =  1;
            $this->where(['t_id' => $id])->update($data);
            
            $info = $this->where(['t_id' => $id])->find();
            model("Member")->where(['uid' => $info['uid']])->update(['userType' => 2]);
            
            
            
            
            $admin = \app\console\service\User::getInstance()->getInfo();
            $arr['uid']         = $info['uid'];
            $arr['reason']      = '认证成功';
            $arr['create_time'] = time();
            $arr['create_uid']  = $admin['uid'];
            model("ThroughDefaultInfo")->allowField(true)->save($arr);
            
            $info = model("Member")->where(['uid' => $info['uid']])->find();
            
            //合伙人审核通过通知
            $userinfo = model("Member")->where(['uid' => $info['uid']])->find();
            //添加integral_record认证时间
            $integral_record = model("IntegralRecord")->where(['openId' => $userinfo['openId']])->find();
            if($integral_record['through'] == ''){
                model("IntegralRecord")->where(['openId' => $userinfo['openId']])->update(['through' => $data['THROUGH_TIME']]);
            }
            
            $isFollow = isFollow($userinfo['openId'],getAccessTokenFromFile());
            if($isFollow){
                $array = [
                    'first'     => ['value' => '您好，你的金融合伙人审核请求已通过，欢迎使用。' , 'color' => '#173177'],
                    'keyword1'  => ['value' => $userinfo['realName'] , 'color' => '#0D0D0D'],
                    'keyword2'  => ['value' => $userinfo['userPhone'] , 'color' => '#0D0D0D'],
                    'keyword3'  => ['value' => date('Y-m-d H:i:s' , time()) , 'color' => '#0D0D0D'],
                    'remark'    => ['value' => '更多精彩项目尽在FA財' , 'color' => '#173177'],
                ];
                $end = sendTemplateMessage(config("register_examine"), $userinfo['openId'], $_SERVER['SERVER_NAME'].'/home/plat_form/index', $array);
                if(!$end){
                    $this->error = '模板消息发送失败';
                    return false;
                }
            }
            
            /*判断推荐用户是否存在,是否退订,如果存在且没退订,就发模板消息*/
            //推荐用户的审核通知
            if($userinfo['superior'] != 0){
                $userinfo1 = model("Member")->where(['uid' => $userinfo['superior']])->find();
                $isFollow = isFollow($userinfo1['openId'],getAccessTokenFromFile());
                if($isFollow){
                    $array1 = [
                        'first'     => ['value' => 'FA財有新的金融合伙人加入!' , 'color' => '#173177'],
                        'keyword1'  => ['value' => $userinfo1['realName'] , 'color' => '#0D0D0D'],
                        'keyword2'  => ['value' => $userinfo['realName'] , 'color' => '#0D0D0D'],
                        'remark'    => ['value' => '感谢您的推荐，查看推荐奖励详情请进' , 'color' => '#173177'],
                    ];
                    $end1 = sendTemplateMessage(config("recommend_success"), $userinfo1['openId'], $_SERVER['SERVER_NAME'].'/home/my_center/index', $array1);
                    if(!$end1){
                        $this->error = '模板消息发送失败';
                        return false;
                    }
                }  
            }
            // 提交事务
            $this->commit();
            return $info;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    public function getStatisticsData($data){
        if(empty($data)){
            $a = 0;$b = 0;$c = 0;
        } else {
            $a = 0;$b = 0;$c = 0;
            foreach ($data as $key => $value) {
                if($value['userType'] == 0){
                    $a++;
                }elseif ($value['userType'] == 1) {
                    $b++;
                } else {
                    $c++;
                }
            }
        }
        $arr = [$a , $b , $c];
        return $arr;
    }
    public function setThroughStop($id){
        //开启事务
        $this->startTrans();
        try{
            $data = [];
            $data['flag'] = -1;
            $this->where(['t_id' => $id])->update($data);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    public function setThroughStart($id){
        //开启事务
        $this->startTrans();
        try{
            $data = [];
            $data['flag'] = 1;
            $this->where(['t_id' => $id])->update($data);
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
     * 认证会员导出
     * @param 真实姓名 $real_name
     * @param 手机号码 $phone
     * @return 数据集合
     */
    public function getThroughAuditedInfo($real_name,$phone)
    {
        $where = [];
        if($real_name!=="" && $phone!==""){
            $where = ['t.status' => 1,'t.flag' => 1,'t.realName' => $real_name,'t.phone' => $phone];
        }else{
            if($real_name!==""){
                $where = ['t.status' => 1,'t.flag' => 1,'t.realName' => $real_name];
            }
            if($phone!==""){
                $where = ['t.status' => 1,'t.flag' => 1,'t.phone' => $phone];
            }
            if($real_name=="" && $phone==""){
                $where = ['t.status' => 1,'t.flag' => 1];
            }
        }
        $result = $this->alias("t")
                       ->join("Member m" , "t.uid=m.uid")
                       ->where($where)
                       ->field("t.*,m.superior,m.person_label,m.is_follow")
                       ->order("t.t_id desc")
                       ->select();
        foreach ($result as $k => $val) {
            //1.业务类型(1级+2级)
            $service_type   = model('DictLabel')->alias('dl')
                                                ->join('Dict d','d.id=dl.dict_id')
                                                ->join('DictType dt','d.dt_id=dt.dt_id')
                                                ->field([
                                                    'dl.dict_fid',
                                                    'group_concat(d.value)'=>'service_name',
                                                ])
                                                ->where([
                                                    'dl.uid'    => $val['uid'],
                                                    'dl.is_del' => 2,
                                                    'd.status'  => 1,
                                                    'dt.sign'   => 'service_type'
                                                ])
                                                ->group('dict_fid')
                                                ->select();
             
            if (!empty($service_type)) {
                foreach ($service_type as $kk =>$each_service_type) {
                    if ($each_service_type['dict_fid']>0) {
                        $father = model('Dict')->field('value')
                        ->where(['id'=>$each_service_type['dict_fid']])
                        ->find();
                        if (!empty($father)) {
                            $service_type[$kk]['father'] = $father['value'];
                        }
                        
                    }
                    
                }
                $service_list = $service_type[0]['service_name'];
            }else{
                $service_list = "";
            }
            $result[$k]['service'] = $service_list;
            
            //2.行业类型(1级+2级)
            $industry   = model('DictLabel')->alias('dl')
                                            ->join('Dict d','d.id=dl.dict_id')
                                            ->join('DictType dt','d.dt_id=dt.dt_id')
                                            ->field([
                                                'dl.dict_fid',
                                                'group_concat(d.value)'=>'industry_name',
                                            ])
                                            ->where([
                                                'dl.uid'    => $val['uid'],
                                                'dl.is_del' => 2,
                                                'd.status'  => 1,
                                                'dt.sign'   => 'industry'
                                            ])
                                            ->group('dict_fid')
                                            ->select();
            if (!empty($industry)) {
                foreach ($industry as $kk =>$each_industry) {
                    if ($each_industry['dict_fid']>0) {
                        $father = model('Dict')->field('value')
                        ->where(['id'=>$each_industry['dict_fid']])
                        ->find();
                        if (!empty($father)) {
                            $industry[$kk]['father'] = $father['value'];
                        }
                        
                    }
                    
                }
                $industry_list = $industry[0]['industry_name'];
            }else{
                $industry_list = "";
            }
            $result[$k]['industry'] = $industry_list;
            
            
            //3.投融规模(1级)
            $size   = model('DictLabel')->alias('dl')
                                        ->join('Dict d','d.id=dl.dict_id')
                                        ->join('DictType dt','d.dt_id=dt.dt_id')
                                        ->field([
                                            'dl.uid',
                                            'group_concat(d.value)'=>'size_name',
                                        ])
                                        ->where([
                                            'dl.uid'    => $val['uid'],
                                            'dl.is_del' => 2,
                                            'd.status'  => 1,
                                            'dt.sign'   => 'size'
                                        ])
                                        ->group('dl.uid')
                                        ->select();
            if (!empty($size)) {
                $size_list = $size[0]['size_name'];
            } else {
                $size_list = "";
            }
            $result[$k]['size'] = $size_list;
            
            //4.省份(1级)
            $province   = model('DictLabel')->alias('dl')
                                            ->join('Dict d','d.id=dl.dict_id')
                                            ->join('DictType dt','d.dt_id=dt.dt_id')
                                            ->field([
                                                'dl.uid',
                                                'group_concat(d.value)'=>'province_name',
                                            ])
                                            ->where([
                                                'dl.uid'    => $val['uid'],
                                                'dl.is_del' => 2,
                                                'd.status'  => 1,
                                                'dt.sign'   => 'to_province'
                                            ])
                                            ->group('dl.uid')
                                            ->select();
            if (!empty($province)) {
                $province_list = $province[0]['province_name'];
            } else {
                $province_list = "";
            }
            $result[$k]['province'] = $province_list;
            
            $result[$k]['createTime'] = date('Y-m-d H:i:s',$val['createTime']);
            
            if($val['is_follow'] == 1){
                $result[$k]['is_follow'] = "是";
            }else{
                $result[$k]['is_follow'] = "否";
            }
            
            
            
            $result[$k]['role_type'] = checkRoleType($val['role_type']);
            if(0 == $val['superior']){
                $result[$k]['superior'] = '-';
            }else{
                $realName = $this->field('realName')->where(['uid'=>$val['superior']])->find();
                $result[$k]['superior'] = $realName['realName'];
            }
        }
        return $result->toArray();
    }
    
    
    //根据uid找到对应认证用户信息
    public function readThroughByUid($uid = '')
    {
        if (!$uid) {
            $this->error = '用户不存在';
            return false;
        }
        $re = $this->where(['uid' => $uid])->find();
        return $re['realName'];
    }
}