<?php 
namespace app\home\model;
use think\Db;
/**
 * 
 */
class OrganizeRequire extends BaseModel
{
	public function getOrganizeRequireLimitListApi($role_type = 0,$uid = 0,$length = 3)
	{


        if(!$uid){
            $this->code = 208;
            $this->msg = '缺少用户id参数';
            return $this->result('',$this->code,$this->msg);
        }

        if(!isset($length) || !is_numeric($length)){
            $this->code = 207;
            $this->msg = '数据展示条数参数不正确';  
            return $this->result('',$this->code,$this->msg);
        }
        
        $dl = model('DictLabel');
        $dl = $dl->getDictLabelApi($uid);
        
        $dict_fid = "";
        //用户无标签
        if(empty($dl)){
            //$dict_fid = "234,243,250,262,270,280,329,332,339,344,351,355,366,369,373,375,384,388,396,402,408,410,412,414,418,420,448,474";
            //$or_map['ord.dict_fid']  = array('in',$dict_fid);
            $all_result              = $this->alias('or')
                                            ->join('Member m','or.uid=m.uid')
                                            ->field([
                                                'or.or_id'          =>'id',
                                                'm.userPhoto'       =>'url',
                                                'm.realName'        =>'name',
                                                'or.name'           =>'top',
                                                'or.business_des'   =>'bottom',
                                                'or.view_num',
                                                'or.type',
                                            ])
                                            ->where([
                                                'or.is_show' => 1,
                                                'or.status'  => 1,
                                            ])
                                            ->order('or.rank desc,or.or_id desc')
                                            ->limit($length)
                                            ->select()
                                            ->toArray();
        }else{
            $dt = model('DictType');
            $service_type_son       = [];//当前用户选择的业务类型所有的2级标签(用来匹配的)
            $service_type_son_par   = [];//当前用户选择的业务类型所有的1级标签(用来匹配的)
            $industry_type_son      = [];//当前用户选中行业的二级标签所属的二级标签(用来匹配的)
            $industry_type_son_par  = [];//当前用户选中行业的二级标签所属的一级标签(用来匹配的)
            $size                   = [];//当前用户选择的投融资规模(用来匹配的)
            $to_province            = [];//当前用户选择的所在省份(用来匹配的)
            $province_all           = 0;
            
            foreach ($dl as $k => $v) {
                $sign = $dt->field('sign')->where(['dt_id'=>$v['dict_type_id']])->find();
                
                //业务类型下的二级标签
                if($sign['sign'] == 'service_type'){
                    if($v['dict_fid'] > 0){
                        $service_type_son[]     = $v['dict_id'];
                        $service_type_son_par[] = $v['dict_fid'];
                    }
                }
                
                //所属行业下的一级标签
                if($sign['sign'] == 'industry'){
                    if($v['dict_fid'] > 0){
                        $industry_type_son[]     = $v['dict_fid'];
                        $industry_type_son_par[] = $v['dict_fid'];
                    }
                }
                
                //投融资规模
                if($sign['sign'] == 'size'){
                    $size[] = $v['dict_id'];
                }
                
                //所在省份
                if($sign['sign'] == 'to_province'){
                    if($v['dict_id']==328){
                        $province_all = 1;
                    }
                    $to_province[] = $v['dict_id'];
                }
            }
            //标签为全国的时候无需区别
            if($province_all==1){
                $to_province       = [];
            }
            $model = model('Dict');
            $size_str = "";
            if(!empty($size)){
                foreach ($size as $va) {
                    $value = $model->field('value')->where(['id'=>$va])->find();
                    if ($value['value'] == '500万以下') {
                        $size_str .= 'or.invest_scale<500 and ';
                    }elseif ($value['value'] == '500-1999万') {
                        $size_str .= '(or.invest_scale>=500 && pr.financing_scale<=1999) and ';
                    }elseif ($value['value'] == '2000-3999万') {
                        $size_str .= '(or.invest_scale>=2000 && pr.financing_scale<=3999) and ';
                    }elseif ($value['value'] == '4000-6999万') {
                        $size_str .= '(or.invest_scale>=4000 && pr.financing_scale<=6999) and ';
                    }elseif ($value['value'] == '7000-9999万') {
                        $size_str .= '(or.invest_scale>=7000 && pr.financing_scale<=9999) and ';
                    }elseif ($value['value'] == '1亿以上 ') {
                        $size_str .= 'or.invest_scale>=10000 and ';
                    }
                }
            }
            
            $sql  = "";
            $sql1 = "";
            $sql2 = "";
            $sql3 = "";
            
            $sql1 = "SELECT DISTINCT aa.or_id as id,
                    		m.userPhoto as url,
                    		m.realName as name,
                    		aa.name as top,
                    		aa.business_des as bottom,
                    		aa.view_num,
                    		aa.type
                            from fic_organize_require aa
                            LEFT JOIN fic_member m on aa.uid=m.uid LEFT JOIN fic_organize_require_dict ord on aa.or_id=ord.or_id
		                    where aa.is_show = 1 and aa.`status` = 1  ";
            
            //匹配业务类型二级标签
            if(!empty($service_type_son)){
                $sql1 .= "and ord.dict_id in (".implode(',', $service_type_son).") ";
            }else{
                $sql1 .= "and ord.dict_id in (234,243,250,262,270,280) ";
            }
    
            if($size_str!==""){
                $sql1 .= " and ".$size_str;//投资规模
            }
            //匹配所属行业未选中二级的一级标签
            if(!empty($industry_type_son_par)){
                $sql2 = "SELECT DISTINCT tt.id,
                            	tt.url,
                            	tt.name,
                            	tt.top,
                            	tt.bottom,
                            	tt.view_num,
                            	tt.type from ( ".$sql1." ) as tt ";
                $sql2 .= "LEFT JOIN  fic_organize_require_dict ttord on tt.id=ttord.or_id
                          where ttord.dict_id  in (".implode(',', $industry_type_son_par).") ";
                
                if(!empty($to_province)){
                    $sql3 = "SELECT * from (".$sql2.") as zz LEFT JOIN  fic_organize_require_dict zzord on zz.id=zzord.or_id where  zzord.dict_id in ( ".implode(',', $to_province).") ";
                    $sql = $sql3;
                }else{
                    $sql = $sql2;
                }
            }else{
                if(!empty($to_province)){
                    $sql2 = "SELECT DISTINCT tt.id,
                            	tt.url,
                            	tt.name,
                            	tt.top,
                            	tt.bottom,
                            	tt.view_num,
                            	tt.type from ( ".$sql1." ) as tt ";
                    $sql2 .= "LEFT JOIN  fic_organize_require_dict ttord on tt.id=ttord.or_id
                          where ttord.dict_id  in ( ".implode(',', $to_province).") ";
                    $sql = $sql2;
                }else{
                    $sql = $sql1;
                }
            }
            
            $sql .= " limit ".$length." ";
            $all_result      = $this->query($sql);
        }
        
        foreach ($all_result as &$v) {
            $all_label = [];
            //查询每一条发布资金的所有业务标签
            $ord1   = model('OrganizeRequireDict');
            $par_business    = $ord1->alias('ord')
                                    ->join('DictType dt','ord.dict_type_id=dt.dt_id')
                                    ->join('Dict d','ord.dict_id=d.id')
                                    ->where(['dt.sign'=>'service_type'])
                                    ->where(['ord.or_id'=>$v['id']])
                                    ->field(['group_concat(d.value)'=>'label'])
                                    ->group('ord.or_id')
                                    ->select()
                                    ->toArray();
            if (!empty($par_business)) {
                $all_label = explode(',', $par_business[0]['label']);
            }
            
            if (count($all_label)>3) {
                $all_label = array_slice($all_label, 0,3);
            }
            $v['label']         = $all_label;
            $v['type_label']    = '资金';
        }
        
        if(empty($all_result)){
            $this->code = 201;
            $this->msg = '没有更多项目或资金';
            return $this->result('',$this->code,$this->msg);
        }
        return $this->result($all_result);
	}

    public function addOrganizeRequireApi($or_data = [],$ord_data = [],$top_dict_value = '')
    {
        if(empty($or_data) || empty($ord_data))
        {
            $this->code = 201;
            $this->msg = '数据不存在';
            $this->result('',$this->code,$this->msg);
        }
        $this->startTrans();
        try {
            $this->allowField(true)->save($or_data);
            //资金id
            $or_id = $this->getLastInsId();
            //insert资金id,一级业务类型的值dict_id,父级dict_fid,标签所属数据类型dict_type_id
            $top_dict_id = $ord_data['top_dict_id'];
            $dict = model('Dict');
            $dt_id = $dict->getDictDtidById($top_dict_id);

            $final_ins = [];
            $final_ins[] = ['or_id'=>$or_id,'dict_id'=>$top_dict_id,'dict_fid'=>0,'dict_type_id'=>$dt_id,'create_time'=>time()];
            
            //insert资金id,二级业务类型的值dict_id,父级dict_fid,标签所属数据类型dict_type_id
            $dict_id = trim($ord_data['dict_id'],',');

            //接收二级业务类型的值(多选或单选)
            if (strpos($dict_id,',') !== false) {
                //多选
                $dict_id_arr = explode(',', $dict_id);
                foreach ($dict_id_arr as $v) {
                    $final_ins[] = ['or_id'=>$or_id,'dict_id'=>$v,'dict_fid'=>$top_dict_id,'dict_type_id'=>$dt_id,'create_time'=>time()];
                }
            }else{
                //单选
                $final_ins[] = ['or_id'=>$or_id,'dict_id'=>$dict_id,'dict_fid'=>$top_dict_id,'dict_type_id'=>$dt_id,'create_time'=>time()]; 
            }

            //insert资金id,一级行业类型的值dict_id,父级dict_fid,标签所属数据类型dict_type_id
            //(如果业务类型是配资业务则,不用插入行业标签)
            if ($top_dict_value !== '配资业务') {
                //接收一级行业类型的值(多选或单选)
                $dict_id = trim($ord_data['top_industry'],',');

                $dt_id = $dict->Distinct(true)
                              ->field('dt_id')
                              ->where('id in ('.$dict_id.')')
                              ->select()
                              ->toArray();
                $dt_id = $dt_id[0]['dt_id'];
                if (strpos($dict_id,',') !== false) {
                    //多选
                    $dict_id_arr = explode(',', $dict_id);
                    foreach ($dict_id_arr as $v) {
                        $final_ins[] = ['or_id'=>$or_id,'dict_id'=>$v,'dict_fid'=>0,'dict_type_id'=>$dt_id,'create_time'=>time()];
                    }
                }else{
                    //单选
                    $final_ins[] = ['or_id'=>$or_id,'dict_id'=>$dict_id,'dict_fid'=>0,'dict_type_id'=>$dt_id,'create_time'=>time()]; 
                }

                //insert资金id,二级行业类型的值dict_id,父级dict_fid,标签所属数据类型dict_type_id
                //接收e二级行业类型的值(多选或单选),
                
                $dict_id = trim($ord_data['industry'],',');
                if (!empty($dict_id)) {
                    $dt_id = $dict->Distinct(true)->field('dt_id')->where('id in ('.$dict_id.')')->select();
                    $dt_id = $dt_id[0]['dt_id'];
                    if (strpos($dict_id,',') !== false) {
                        //多选
                        $dict_id_arr = explode(',', $dict_id);
                        foreach ($dict_id_arr as $v) {
                            $dict_fid = $dict->field('fid')->where(['id'=>$v])->find();
                            $dict_fid = $dict_fid['fid'];
                            $final_ins[] = ['or_id'=>$or_id,'dict_id'=>$v,'dict_fid'=>$dict_fid,'dict_type_id'=>$dt_id,'create_time'=>time()];
                        }
                    }else{
                        //单选
                        $dict_fid = $dict->field('fid')->where(['id'=>$dict_id])->find();
                        $dict_fid = $dict_fid['fid'];
                        $final_ins[] = ['or_id'=>$or_id,'dict_id'=>$dict_id,'dict_fid'=>$dict_fid,'dict_type_id'=>$dt_id,'create_time'=>time()]; 
                    }
                }
            }
            
            //insert资金id,省份类型的值dict_id,父级dict_fid,标签所属数据类型dict_type_id
            //接收省份类型的值(多选或单选)
            $dict_id = trim($ord_data['province'],',');
            $dt_id = $dict->Distinct(true)->field('dt_id')->where('id in ('.$dict_id.')')->select();
            $dt_id = $dt_id[0]['dt_id'];
            if (strpos($dict_id,',') !== false) {
                //多选
                $dict_id_arr = explode(',', $dict_id);
                foreach ($dict_id_arr as $v) {
                    $final_ins[] = ['or_id'=>$or_id,'dict_id'=>$v,'dict_fid'=>0,'dict_type_id'=>$dt_id,'create_time'=>time()];
                }
            }else{
                //单选
                $final_ins[] = ['or_id'=>$or_id,'dict_id'=>$dict_id,'dict_fid'=>0,'dict_type_id'=>$dt_id,'create_time'=>time()]; 
            }

            //批量插入OrganizeRequireDict
            $ord = model('OrganizeRequireDict');
            $ord->allowField(true)->saveAll($final_ins);
            //每发布一条资金,member对应的业务数量+1
            model('Member')->where(['uid'=>$or_data['uid']])->setInc('service_num');
            //同时更新到session中
            $info = model('Member')->where(['uid'=>$or_data['uid']])->find()->toArray();
             session('FINANCE_USER',$info);
            $this->commit();
            $this->code = 200;
            $this->msg = '恭喜 发布成功';
            return $this->result('',$this->code,$this->msg);
        } catch (\Exception $e) {

            $this->rollback();
            $this->code = 218;
            $this->msg = '发布失败 '.$e->getMessage();
            return $this->result('',$this->code,$this->msg);    
        }    
    }

    //查看发布资金的详情
    public function getOrganizeRequireDetail($data = [])
    {
        
        $white  = model("whiteUid");
        if($white->getWhite($data['uid'])){
            //查看人数+1
            $this->where(['or_id'=>$data['id'],'is_show'=>1,'status'=>1])->setInc('view_num');
        }
        
        $result = [];
        //查询资金是否存在
        $detail = $this->alias('or')
                       ->join('Member m','or.uid=m.uid')
                       ->where(['or.is_show'=>1,'or.status'=>1,'or.or_id'=>$data['id']])
                       ->find()
                       ->toArray();
        if (empty($detail)) {
            $this->code = 201;
            $this->msg = '数据不存在';
            return $this->result('',$this->code,$this->msg);
        }
        //当前登录用户是否关注当前资金发布人
        $fu = model('FollowUser')->where(['from_uid'=>$data['uid'],'to_uid'=>$detail['uid']])->find();
        if (empty($fu)) {
            //未关注
            $result['is_follow'] = '关注';
        }else{
            if ($fu['is_del'] == 1) {
                //未关注
                $result['is_follow'] = '关注';
            } 
           
            if ($fu['is_del'] == 2) {
                //已关注
                $result['is_follow'] = '已关注';
            } 
        }
        $result['or_id']            = $detail['or_id'];
        $result['uid']              = $detail['uid'];
        $result['name']             = $detail['name'];
        $result['real_name']        = $detail['realName'];
        $result['userPhone']        = $detail['userPhone']; 
        $result['user_photo']       = $detail['userPhoto'];
        $result['company_jc']       = $detail['company_jc'];
        $result['position']         = $detail['position'];
        $result['contact_status']   = contact_status_org($detail['contact_status']);
        $result['service_num']      = $detail['service_num'];
        $result['require_num']      = $detail['require_num'];
        
        $userFollow = model('Member')->where(['uid'=>$data['uid']])->find();
        $result['follow_flg']= $userFollow['is_follow'];
       
        //查询资金的一级业务类型
        $par_business = model('OrganizeRequireDict')->alias('ord')
                                                    ->join('Dict d','ord.dict_id=d.id')
                                                    ->join('DictType dt','ord.dict_type_id=dt.dt_id')
                                                    ->field('ord.dict_id,d.value')
                                                    ->where(['ord.or_id'=>$detail['or_id'],'dt.sign'=>'service_type','ord.dict_fid'=>0])
                                                    ->find()
                                                    ->toArray();
        if (empty($par_business)) {
            $this->code = 216;
            $this->msg = '一级业务标签不存在';
            return $this->result('',$this->code,$this->msg);
        }
        $result['par_business'] = $par_business['value'].'投资范围';
        //查询资金的所在省份
        $province = model('OrganizeRequireDict')->alias('ord')
                                                ->join('Dict d','ord.dict_id=d.id')
                                                ->join('DictType dt','ord.dict_type_id=dt.dt_id')
                                                ->field('d.value')
                                                ->where(['ord.or_id'=>$detail['or_id'],'dt.sign'=>'to_province','ord.dict_fid'=>0])
                                                ->select()
                                                ->toArray();
        if(!empty($province)){
            $province = array_column($province, 'value');

        }else{
            $province = [];

        }
        $result['province'] = $province;
        // 查询当前资金一级标签下的二级业务标签
        $son_business = model('OrganizeRequireDict')->alias('ord')
                                                    ->field('d.value')
                                                    ->join('Dict d','ord.dict_id=d.id')
                            ->where(['ord.or_id'=>$detail['or_id'],'ord.dict_fid'=>$par_business['dict_id']])
                                                    ->select()
                                                    ->toArray();
        if(!empty($son_business)){
            $son_business = array_column($son_business, 'value');

        }else{
            $son_business = [];

        }
        $result['son_business'] = $son_business;
        //业务描述
        $result['business_des'] = '业务描述：'.$detail['business_des'];

        //查询当前所有的行业标签(选到二级的就展示二级)(非配资业务)
        if ($par_business['value'] !== '配资业务') {
            $par_industry = model('OrganizeRequireDict')->alias('ord')
                                                        ->join('Dict d','ord.dict_id=d.id')
                                                        ->join('DictType dt','ord.dict_type_id=dt.dt_id')
                                                        ->field('d.id,d.fid,d.value')
                                                        ->where([
                                                            'ord.or_id'=>$detail['or_id'],
                                                            'dt.sign'=>'industry',
                                                        ])
                                                        ->select()
                                                        ->toArray();

            if (!empty($par_industry)) {
                $all_label = [];
                $result['industry_title'] = '关注的行业';
                $industry_fid = array_column($par_industry, 'fid');
                 foreach ($par_industry as $val) {
                    if ($val['fid']>0) {
                        array_push($all_label, $val['value']);
                    } else {
                        if (!in_array($val['id'], $industry_fid)) {
                           array_push($all_label, $val['value']);
                        }

                    }
                            
                }
                $result['par_industry'] = $all_label;                                          
            } else {
                $result['industry_title'] = '关注的行业';
                $result['par_industry'] = [];
            }
                                                                                                    
        }else{
            $result['industry_title'] = '';
            $result['par_industry'] = [];
        }
       
        //资金信息
        $result['money_title'] = '资金信息';
        $result['type_title'] = '资金类型';
        $fund_type_arr = explode(',', $detail['fund_type']);
        $fund_type_new_arr = [];
        foreach ($fund_type_arr as $each_fund_type) {
            $fund_type_new_arr[] = getFundType($each_fund_type); 
        }
        $result['fund_type'] = $fund_type_new_arr;
        if ($par_business['value'] == '配资业务') {
            $result['scale_title'] = '资金规模';
        } else {
            $result['scale_title'] = '投资规模';
        }
        
        $result['invest_scale'] = $detail['invest_scale'].'万元';
        $result['period_title'] = '投资期限';
        $result['financing_period'] = getFinancingPeriod($detail['financing_period']);
        $result['mode_title'] = '投资方式';
        $invest_mode_arr = explode(',', $detail['invest_mode']);
        $invest_mode_new_arr = [];
        foreach ($invest_mode_arr as $each_invest_mode) {
            $invest_mode_new_arr[] = getInvestMode($each_invest_mode); 
        }
        $result['invest_mode'] = $invest_mode_new_arr;

        //投资标准
        if ($par_business['value'] == '配资业务') {
            $result['target_title'] = '配资标准';
        } else {
            $result['target_title'] = '投资标准';
        }
        $result['invest_target'] = $detail['invest_target'];

        //业务截止日期
        $result['deadline'] = deadline($detail['create_time'],$detail['validity_period']); 
        //查看人数
        $result['view_num'] = $detail['view_num'];
        //收藏状态
        if (!empty($data['collection_organize_require'])) {
            $cor_arr = explode(',', $data['collection_organize_require']);
            if (in_array($detail['or_id'], $cor_arr)) {
                $result['is_collect'] = '已收藏';
            } else {
                $result['is_collect'] = '收藏';
            }
            
        }else{
            $result['is_collect'] = '收藏';
        }
        $result['type'] = 'organize_require';
        
        
        $desc = implode('，', $result['par_industry']);
        //分享内容
        $result['share']['title']  = $result['real_name']."的投资需求";
        $result['share']['desc']   = $desc;
        $result['share']['imgUrl'] = $result['user_photo'];  
        
        return $this->result($result);
    }
      
    //个人中心-删除一条业务(发布资金)
    public function delMyOrganizeRequireApi($id=0,$uid=0)
    {
        if (empty($id)) {
            $this->code = 255;
            $this->msg = '缺少项目或资金的id';
            return $this->result('',$this->code,$this->msg);
        }
        if (empty($uid)) {
            $this->code = 208;
            $this->msg = '缺少用户id参数';
            return $this->result('',$this->code,$this->msg);
        }

        $this->startTrans();
        try {
            //删除一条资金
            $this->where(['or_id'=>$id])->update(['is_show'=>3]);
            //每删除一条项目,member对应的业务数量-1
            model('Member')->where(['uid'=>$uid])->setDec('service_num');
            //删除对应的标签
            model('OrganizeRequireDict')->where(['or_id'=>$id])->update(['is_del'=>1]);
            $info = model('Member')->where(['uid'=>$uid])->find()->toArray();
            session('FINANCE_USER',$info);
            $this->commit();
            $this->code = 200;
            $this->msg = '删除成功';
            return $this->result('',$this->code,$this->msg);
        } catch (\Exception $e) {
            $this->rollback();
            $this->code = 259;
            $this->msg = '删除失败';
            return $this->result('',$this->code,$this->msg);
        }
    }

    //用户查看发布资金,该资金查看人数+1
    public function editViewNumApi($id)
    {
        $re = $this->where(['or_id'=>$id])->find()->toArray();
        if (empty($re)) {
            $this->code = 255;
            $this->msg = '资金不存在';
            return $this->result('',$this->code,$this->msg);
        }
        $result = $this->where(['or_id'=>$id])->setInc('view_num');
        if ($result) {
            $this->code = 267;
            $this->msg = '更新查看人数成功';
            return $this->result('',$this->code,$this->msg);            
        }

        $this->code = 268;
        $this->msg = '更新查看人数失败';
        return $this->result('',$this->code,$this->msg);
    }
    //用户电话联系发布资金的拥有者,该资金电话联系次数+1
    public function editTelNumApi($id)
    {
        $re = $this->where(['or_id'=>$id])->find()->toArray();
        if (empty($re)) {
            $this->code = 255;
            $this->msg = '资金不存在';
            return $this->result('',$this->code,$this->msg);
        }
        $result = $this->where(['or_id'=>$id])->setInc('tel_num');
        if ($result) {
            $this->code = 200;
            $this->msg = '更新电话联系次数成功';
            return $this->result('',$this->code,$this->msg);            
        }

        $this->code = 270;
        $this->msg = '更新电话联系次数失败';
        return $this->result('',$this->code,$this->msg);
    }
    
    
    //市场-精选栏,用户身份为项目方展示资金列表
    public function getCareOrganizeAndOrganizeRequireMarketList($data = [])
    {
       
        //查询当前登录用户添加的偏好标签
        $dl     = model('DictLabel')->getDictLabelApi($data['uid']);
        //每页显示数
        $length = $data['length'];
        //计算偏移量
        $offset = $data['offset'];
        //项目方
        //用户未添加标签,则展示手动排序最前,最新de状态为(发布并展示)的平台资金+发布资金
        if(empty($dl)){
            //平台资金
           $organize_list = model('Organize')->alias('o')
                        ->join('Dict d','o.unit = d.id')
                        ->join('MaterialLibrary ml','o.top_img=ml.ml_id')
                        ->field([
                            'o.org_id'=>'id',
                            'ml.url',
                            'o.org_short_name'=>'name',
                            'concat(o.contacts," | ",o.position)'=>'top',
                            'o.inc_industry'=>'label',
                            'concat(o.scale_min,"-",o.scale_max,"",d.value)'=>'bottom',
                            'o.view_num',
                            'o.type',
                        ])
                        ->where(['o.status'=>2,'o.flag'=>1])
                        ->order('o.view_num desc,o.list_order desc,o.org_id desc')
                        ->select()
                        ->toArray();
           if(!empty($organize_list)){
               
                $dict = model('Dict');

               foreach ($organize_list as $k => &$v) {
                    if(!$v['label']){
                        $this->code = 211;
                        $this->msg = '请先往后台添加精选机构的行业标签';
                         return $this->result('',$this->code,$this->msg);
                        
                    }
                    //查询每条精选机构添加的行业标签是否在数据字典中存在    
                    $industry_dict = $dict->field('id,fid,value')
                                          ->where('id in('.$v['label'].')')
                                          ->select();
                    if(empty($industry_dict)){
                        $this->code = 211;
                        $this->msg = '数据字典中不存在该精选机构添加的行业标签';
                       return $this->result('',$this->code,$this->msg);
                       
                    }

                    //如果存在,查询当前精选机构的每一个行业标签是否存在对应的一级行业标签
                    $bq = [];
                    
                    foreach ($industry_dict as $ke => $va) {
                        $top_industry = $dict->field('value')->where(['id'=>$va['fid']])->find();
                        //如果存在,把当前行业标签值替换为一级标签的值
                        if(!empty($top_industry)){
                            if (!in_array($top_industry['value'], $bq)) {
                              $bq[] = $top_industry['value'];
                           } 
                        }else{
                            $bq[] = $va['value'];
                        }
                    }
                    
                    if (count($bq)>3) {
                       $bq = array_slice($bq, 0,3);
                    }
                    $v['label']         = $bq;
                    $v['bottom']        = '管理规模:'.$v['bottom'].'元';
                    $v['type_label']    = '精选资金';
                    $v['url'] = config('view_replace_str')['__DOMAIN__'].$v['url'];
                }
              
               
            }
           
            //发布的后台已置精资金
           /*$organize_require_list = $this->alias('or')
                         ->join('Member m','or.uid=m.uid')
                         ->field([
                            'or.or_id'=>'id',
                            'm.userPhoto'=>'url',
                            'm.realName'=>'name',
                            'or.name'=>'top',
                            'or.business_des'=>'bottom',
                            'or.view_num',
                            'or.type',
                            'or.create_time',
                            'or.validity_period',
                        ])
                         ->where(['or.is_show'=>1,'or.status'=>1,'or.is_care'=>1])
                         ->order('or.view_num desc,or.is_top asc,or.rank desc,or.or_id desc')
                         ->select()
                         ->toArray();
            
           if(!empty($organize_require_list)){
                foreach ($organize_require_list as $k => &$v) {
                    $is_vaild = prorStatus2($v['create_time'],$v['validity_period']);
                    if ($is_vaild) {
                        $all_label = [];
                        //查询所有的业务标签
                        $ord1 = model('OrganizeRequireDict');
                        $all_business = $ord1->alias('ord')
                                            ->join('DictType dt','ord.dict_type_id=dt.dt_id')
                                            ->join('Dict d','ord.dict_id=d.id')
                                            ->where(['dt.sign'=>'service_type'])
                                            ->where(['ord.or_id'=>$v['id']])
                                            ->field('d.value')
                                            ->select()
                                            ->toArray();
                        if (!empty($all_business)) {
                            $all_label = array_column($all_business, 'value');
                        } 

                        if (count($all_label)>3) {
                           $all_label = array_slice($all_label, 0,3);
                        }

                        $v['label'] = $all_label;
                        $v['type_label'] = '精选资金';
                        $v['bottom'] = '简介：'.$v['bottom'];
                    } else {
                        unset($organize_require_list[$k]);
                    }
                }
           }*/
           
            //if (empty($organize_list) && empty($organize_require_list)) {
            if (empty($organize_list)) {
                $this->code = 201;
                $this->msg  = '没有更多项目或资金';
                return $this->result('',$this->code,$this->msg);
            }
            
            $all_organize   = array_merge($organize_list);
            $final_organize = array_slice($all_organize, $offset,$length);
            return $this->result($final_organize);
            
        }
        
            //用户添加标签的情况下(平台机构无业务类型标签)
            $dt = model('DictType'); 
            $industry_type_all      = [];//当前用户选择的行业的所有一二级标签
            $industry_type_son_par  = [];//当前用户选中行业的二级标签所属的一级标签
            $industry_type_son      = [];//当前用户选择的行业所有的2级标签(用来匹配的)
            $size                   = [];//当前用户选择的投融资规模(用来匹配的)
            $to_province            = [];//当前用户选择的所在省份(用来匹配的)
            foreach ($dl as $k => $v) {
               $sign = $dt->field('sign')->where(['dt_id'=>$v['dict_type_id']])->find();
               //所属行业下的标签
               if($sign['sign'] == 'industry'){
                   // $industry[] = $v['dict_id'];
                   $industry_type_all[] = $v['dict_id'];
                    if($v['dict_fid'] > 0){
                      $industry_type_son[] = $v['dict_id'];
                      $industry_type_son_par[] = $v['dict_fid'];
                    }    
               }
               
               
               //投融资规模
               if($sign['sign'] == 'size'){
                   $size[] = $v['dict_id'];
               }
               //所在省份
               if($sign['sign'] == 'to_province'){
                   $to_province[] = $v['dict_id'];
               }
            }            
            //得到当前用户选中二级标签所属的一级标签和未选中二级标签的一级标签(所属行业)
            $industry_type_all_par = array_diff($industry_type_all, $industry_type_son);
            //得到当前用户未选中二级标签的一级标签(用来匹配的条件)(所属行业)
            $industry_type_null_par = array_diff($industry_type_all_par,$industry_type_son_par);
            //统一所有融资规模单位为万(取最大值比较)
            $wan = model('Organize')->alias('o')
                        ->join('Dict d','o.unit = d.id')
                        ->field('o.org_id,o.scale_max,d.value as unit_name')
                        ->where(['o.status' => 2 , 'o.flag' => 1])
                        ->select();
            if(empty($wan)){
                $this->code = 201;
                $this->msg = '单位不存在,请先往后台添加';
                return $this->result('',$this->code,$this->msg);     
            }

            foreach ($wan as &$v) {
                if($v['unit_name'] == '亿'){
                    $v['scale_max'] = $v['scale_max']*10000;
                    $v['unit_name'] ='万';
                }
            }
            //dump($wan);die;
            $match_str = '';
            //匹配所属行业二级标签
            $org_id_column = '';
            $od = model('OrganizeDict');
            if(!empty($industry_type_son)){
                $where = implode(',', $industry_type_son); 
                $org_id = $od->Distinct('true')
                             ->field('org_id')
                             ->where('id in ('.$where.')')
                             ->select()
                             ->toArray();
                if(!empty($org_id)){
                    $org_id_column .= implode(',',array_column($org_id, 'org_id')).',';
                }
                  
            }
            
            //匹配所属行业未选中二级的一级标签
            if(!empty($industry_type_null_par)){
                $where = implode(',', $industry_type_null_par); 
                $org_id = $od->Distinct('true')
                             ->field('org_id')
                             ->where('id in ('.$where.')')
                             ->select()
                             ->toArray();
                if(!empty($org_id)){
                    $org_id_column .= implode(',',array_column($org_id, 'org_id')).',';   
                }
                
            }
            
            //匹配所在省份
            if(!empty($to_province)){
                $od2 = model('OrganizeDict2');
                $where = implode(',', $to_province);
                $org_id = $od2->Distinct('true')->field('org_id')->where('to_province in ('.$where.')')->select()->toArray();
                if(!empty($org_id)){
                   $org_id_column .= implode(',',array_column($org_id, 'org_id')).','; 
                }
                 
            } 
            //匹配投融规模
            $model = model('Dict');
            if(!empty($size)){
                // dump($wan);die;
                   foreach ($size as $va) {
                       $value = $model->field('value')->where(['id'=>$va])->find();
                       if ($value['value'] == '500万以下') {
                           // $match_str .= 'financing_amount<500 or';
                           foreach ($wan as $k=>$v) {
                               // dump($v);die;
                               // dump($v['scale_max']);die;
                               if($v['scale_max']<500){
                                 $org_id_column .= $v['org_id'].',';
                                 // unset($wan[$k]);
                               }
                           }                            
                       }
                       if ($value['value'] == '500-1999万') {
                           // $match_str .= '(financing_amount>=500 && financing_amount<=1999) or';
                           foreach ($wan as $k => $v) {
                               if($v['scale_max']>=500 && $v['scale_max']<=1999){
                                  $org_id_column .=$v['org_id'].',';
                                  // unset($wan[$k]);
                               }
                           }
                       }
                       if ($value['value'] == '2000-3999万') {
                           // $match_str .= '(financing_amount>=2000 && financing_amount<=3999) or';
                           foreach ($wan as $k => $v) {
                               if($v['scale_max']>=2000 && $v['scale_max']<=3999){
                                  $org_id_column .=$v['org_id'].',';
                                  // unset($wan[$k]);
                               }
                           }
                       }
                       if ($value['value'] == '4000-6999万') {
                           // $match_str .= '(financing_amount>=4000 && financing_amount<=6999) or';
                           foreach ($wan as $k => $v) {
                               if($v['scale_max']>=4000 && $v['scale_max']<=6999){
                                  $org_id_column .=$v['org_id'].',';
                                  // unset($wan[$k]);
                               }
                           }   
                       }
                       if ($value['value'] == '7000-9999万') {
                           // $match_str .= '(financing_amount>=7000 && financing_amount<=9999) or';
                           foreach ($wan as $k => $v) {
                               if($v['scale_max']>=7000 && $v['scale_max']<=9999){
                                  $org_id_column .=$v['org_id'].',';
                                  // unset($wan[$k]);
                               }
                           }
                       }
                       if ($value['value'] == '1亿以上 ') {
                           // $match_str .= 'financing_amount>=10000 or';
                           foreach ($wan as $k => $v) {
                               if($v['scale_max']>=10000){
                                  $org_id_column .=$v['org_id'].',';
                                  // unset($wan[$k]);
                               }
                           }
                       }
                   }
            }
             $org_id_column = trim($org_id_column,',');
             $map_org_id['o.org_id'] = array('in',$org_id_column);
            //开始匹配平台资金
            $organize_list = model('Organize')
                        ->alias('o')
                        ->join('Dict d','o.unit = d.id')
                        ->join('MaterialLibrary ml','o.top_img=ml.ml_id')
                        ->field([
                            'o.org_id'=>'id',
                            'ml.url',
                            'o.org_short_name'=>'name',
                            'concat(o.contacts," | ",o.position)'=>'top',
                            'o.inc_industry'=>'label',
                            'concat(o.scale_min,"-",o.scale_max,"",d.value)'=>'bottom',
                            'o.view_num',
                            'o.type',
                        ])
                        ->where(['o.status'=>2,'o.flag'=>1])
                        ->where($map_org_id)
                        ->order('o.view_num desc,o.list_order desc,o.org_id desc')
                        ->select()
                        ->toArray();
            
            if(!empty($organize_list)){
               
                $dict = model('Dict');

               foreach ($organize_list as $k => &$v) {
                    if(!$v['label']){
                        $this->code = 211;
                        $this->msg = '请先往后台添加精选机构的行业标签';
                         return $this->result('',$this->code,$this->msg);
                        
                    }
                    //查询每条精选机构添加的行业标签是否在数据字典中存在    
                    $industry_dict = $dict->field('id,fid,value')
                                          ->where('id in('.$v['label'].')')
                                          ->select();
                    if(empty($industry_dict)){
                        $this->code = 211;
                        $this->msg = '数据字典中不存在该精选机构添加的行业标签';
                       return $this->result('',$this->code,$this->msg);
                       
                    }

                    //如果存在,查询当前精选机构的每一个行业标签是否存在对应的一级行业标签
                    $bq = [];
                    
                    foreach ($industry_dict as $ke => $va) {
                        $top_industry = $dict->field('value')->where(['id'=>$va['fid']])->find();
                        //如果存在,把当前行业标签值替换为一级标签的值
                        if(!empty($top_industry)){
                            if (!in_array($top_industry['value'], $bq)) {
                               $bq[] = $top_industry['value'];
                            }     
                        }else{
                            $bq[] = $va['value'];
                        }
                    }
                    if (count($bq)>3) {
                       $bq = array_slice($bq, 0,3);
                    }
                    $v['label'] = $bq;
                    $v['bottom'] = '管理规模:'.$v['bottom'].'元';
                    $v['type_label'] = '精选资金';
                    $v['url'] = config('view_replace_str')['__DOMAIN__'].$v['url'];
                }
              
                
            }

            //匹配发布资金
            //用户添加标签的情况下
            //service_type,industry,to_province,size一级标签集合
            // dump($organize_list);die;
            /*$dict_id_user = [];
            foreach ($dl as $ka => $va) {
                if($va['dict_fid'] == 0){
                    $dict_id_user[] = $va['dict_id'];
                }
            }
            $dict_id_str = implode(',', $dict_id_user);
            //根据用户添加的一级标签的集合匹配到对应拥有同样一级标签的项目
            $ord = model('OrganizeRequireDict');
            $map_dict_id['dict_id'] = array('in',$dict_id_str);
            $org_id_column  = $ord->Distinct(true)
                                  ->field('or_id')
                                  ->where($map_dict_id)
                                  ->where(['is_del'=>2])
                                  ->select()
                                  ->toArray();
                                 
            if(!empty($org_id_column)){
                
               //拼接匹配到的发布资金id的条件
                $or_id_arr = array_column($org_id_column,'or_id');
                $map_or_id['or.or_id'] = array('in',implode(',',$or_id_arr));
                //查询匹配的结果
                $organize_require_list = $this->alias('or')
                             ->join('Member m','or.uid=m.uid')
                             ->field([
                                'or.or_id'=>'id',
                                'm.userPhoto'=>'url',
                                'm.realName'=>'name',
                                'or.name'=>'top',
                                'or.business_des'=>'bottom',
                                'or.view_num',
                                'or.type',
                                'or.create_time',
                                'or.validity_period',
                            ])
                             ->where(['or.is_show'=>1,'or.status'=>1,'or.is_care'=>1])
                             ->where($map_or_id)
                             ->order('or.view_num desc,or.is_top asc,or.rank desc,or.or_id desc')
                             ->select()
                             ->toArray();
                        foreach ($organize_require_list as $k => &$v) {
                            $is_vaild = prorStatus2($v['create_time'],$v['validity_period']);
                            if ($is_vaild) {
                                $all_label = [];
                                //查询每一条发布资金的业务标签
                                $ord1 = model('OrganizeRequireDict');
                                $all_business = $ord1->alias('ord')
                                                ->join('DictType dt','ord.dict_type_id=dt.dt_id')
                                                ->join('Dict d','ord.dict_id=d.id')
                                                ->where(['dt.sign'=>'service_type'])
                                                ->where(['ord.or_id'=>$v['id']])
                                                ->field('d.value')
                                                ->select()
                                                ->toArray();
                                if (!empty($all_business)) {
                                    $all_label = array_column($all_business, 'value');
                                } 
                               
                                if (count($all_label)>3) {
                                   $all_label = array_slice($all_label, 0,3);
                                }
                                $v['label'] = $all_label;
                                $v['type_label'] = '精选资金';
                                $v['bottom'] = '简介：'.$v['bottom'];
                            } else {
                                unset($organize_require_list[$k]);
                            }
                    }

            }else{
                $organize_require_list = [];
            }*/
            
            if (empty($organize_list)) {
                $this->code = 201;
                $this->msg  = '没有更多项目或资金';
                return $this->result('',$this->code,$this->msg);
            }
            $all_organize   = array_merge($organize_list);

            $final_organize = array_slice($all_organize, $offset,$length);
             
        return $this->result($final_organize);                       
    }
    
    
    //首页-搜索-资金
    public function getIndexSearchOrganize($keyword = '',$page = 1,$uid = 0,$length = 0,$offset = 0)
    {

        $match_orgainze = '';//精选机构匹配的所有条件
        $match_orgainze_require = '';//发布机构匹配的所有条件
                       
        //3.平台精选资金
        $organize = model('Organize');
        $o = $organize ->alias('o')
                 ->join('OrganizeDict od','o.org_id=od.org_id')
                 ->join('Dict d','od.id=d.id')
                 ->join('Dict d2','d.fid = d2.id','left')
                 ->join('Dict d3','o.unit = d3.id')
                 ->join('MaterialLibrary ml','o.top_img = ml.ml_id')
                 ->field([
                    'o.org_id',
                    'ml.url'=>'img_url',
                    'o.org_short_name'=>'name',
                    'concat_ws("|",o.contacts,o.position)'=>'top_name',
                    'group_concat(d2.value separator ",")'=>'tag',
                    'concat(o.scale_min,"-",o.scale_max,d3.value)'=>'des',
                    'o.view_num',
                    'o.type',
                  ])
                 ->group('o.org_id')
                 ->where(['o.status'=>2, 'o.flag'=>1 ]);
        
        //4.用户发布资金
        $or = $this->alias('or')
                   ->join('Member m','or.uid=m.uid')
                   ->join('OrganizeRequireDict ord','or.or_id=ord.or_id')
                   ->join('Dict d','ord.dict_id=d.id')
                   ->join('DictType dt','ord.dict_type_id=dt.dt_id')
                   ->field([
                    'or.or_id'=>'org_id',
                    'm.userPhoto'=>'img_url',
                    'm.realName'=>'name',
                    'or.name'=>'top_name',
                    'group_concat(d.value separator ",")'=>'tag',
                    'or.business_des'=>'des',
                    'or.view_num',
                    'or.type',
                    ])
                   ->group('or.or_id')
                   ->where([
                    'or.is_show'=>1,
                    'or.status'=>1,
                    'ord.dict_fid'=>0
                    ]);
        
        //组装精选机构简称&&全称条件
        $match_orgainze .= 'o.org_short_name like "%'.$keyword.'%" or o.org_name like "%'.$keyword.'%" or ';

        //组装精选机构投融规模条件
        $match_orgainze .= 'o.scale_max like "%'.$keyword.'%" or ';

        //组装发布机构简称条件
        $match_orgainze_require .= 'or.name like "%'.$keyword.'%" or ';

        //组装发布机构投融规模条件
        $match_orgainze_require .= 'or.invest_scale like "%'.$keyword.'%" or ';
        
        //获取数据字典to_province类型的标签dict_id
        $where = "dt.sign = 'to_province'";

        //查询dict表中模糊匹配到的to_province偏好标签dict_id
        $dict = model('Dict');
        $dict_column = $dict->alias('d')
                     ->join('DictType dt','d.dt_id = dt.dt_id')
                     ->field('d.id')
                     ->where($where) 
                     ->where('d.value like "%'.$keyword.'%"')
                     ->where(['d.status'=>1])
                     ->select()
                     ->toArray();

        if(!empty($dict_column)){
            $dict_id = implode(',', array_column($dict_column,'id'));
            //组装精选机构省份条件
            $od2 = model('OrganizeDict2');
            $province = $od2->Distinct(true)
                            ->field('org_id')
                            ->where('to_province in ('.$dict_id.')')
                            ->select()
                            ->toArray();
            if(!empty($province)){
                $org_id = implode(',', array_column($province, 'org_id'));
                $match_orgainze .= 'o.org_id in ('.$org_id.') or ';

            }

            //组装发布机构省份条件 
            $ord = model('OrganizeRequireDict');
            $province2 = $ord->Distinct(true)
                             ->field('or_id')
                             ->where('dict_id in ('.$dict_id.')')
                             ->select()
                             ->toArray();
            if(!empty($province2)){
                $or_id = implode(',', array_column($province2, 'or_id'));
                $match_orgainze_require .= 'or.or_id in ('.$or_id.') or ';
            }
            
            
               
        } 
        //创建查询用户发布机构的sql语句
        $organize_require_list = $or->where(rtrim($match_orgainze_require,' or '))->buildSql();
       
        //执行合并查询平台机构
        $organize_list = $o->where(rtrim($match_orgainze,' or '))
                           ->union($organize_require_list,true)  //用户发布机构
                           ->select(false);
        
        //返回排序且分页后的合并的精选/用户发布机构(资金)
        $org_sql = "($organize_list)";
        $all_organize = Db::table($org_sql.' as b')->field([
                    'b.org_id',
                    'b.img_url',
                    'b.name',
                    'b.top_name',
                    'b.tag',
                    'b.des',
                    'b.view_num',
                    'b.type',
                    ])  
                      ->order('b.org_id desc')
                      ->limit($offset, $length)
                      ->select();
        if (empty($all_organize)) {
            $this->code = 214;
            $this->msg = '抱歉没有发现的相关内容';
            return $this->result('',$this->code,$this->msg);
        }
       
        foreach ($all_organize as &$value) {
            $label = explode(',', $value['tag']);
            if (count($label)>3) {
                 $label = array_slice($label,0,3);
            }
            $value['tag'] = $label;
            if ($value['type'] == 'organize') {
                $value['des'] = '管理规模:'.$value['des'].'元';
            } else {
                $value['des'] = '简介:'.$value['des'];
            }
            
        }
                       
        return $this->result($all_organize);         

    }
    
    
    /**
     * 股权栏列表展示的是一级业务为新三板+股权
     */
    public function getStockRightOrganizeRequireMarketList($data = [])
    {

        $dl = model('DictLabel');
        $dl = $dl->getDictLabelApi($data['uid']);
        
        $dict_fid = "";
        //用户无标签
        if(empty($dl)){
            $sql1 = "SELECT DISTINCT aa.or_id as id,
                    		m.userPhoto as url,
                    		m.realName as name,
                    		aa.name as top,
                    		aa.business_des as bottom,
                    		aa.view_num,
                    		aa.type,
                    		aa.create_time,
                    		aa.rank,
                    		aa.is_top,
                    		aa.is_care,
                            aa.validity_period
                            from fic_organize_require aa
                            LEFT JOIN fic_member m on aa.uid=m.uid LEFT JOIN fic_organize_require_dict ord on aa.or_id=ord.or_id
		                    where aa.is_show = 1 and aa.`status` = 1 and ord.dict_fid  in (234,243) ";
            $dict_fid = "329,332,339,344,351,355,366,369,373,375,384,388,396,402,408,410,412,414,418,420,448,474";
            $sql = "SELECT DISTINCT tt.id,
                            	tt.url,
                            	tt.name,
                            	tt.top,
                            	tt.bottom,
                            	tt.view_num,
                            	tt.type,
                            	tt.create_time,
                            	tt.rank,
                            	tt.is_top,
                            	tt.is_care,
                            	tt.validity_period from ( ".$sql1." ) as tt ";
            $sql .= "LEFT JOIN  fic_organize_require_dict ttord on tt.id=ttord.or_id
                          where ttord.dict_id  in (".$dict_fid.") ";
            
            $sql .= " ORDER BY is_top asc,is_care asc,rank desc,view_num desc,create_time desc ";
            $all_result      = $this->query($sql);
            
        }else{
            $dt = model('DictType');
            $service_type_son       = [];//当前用户选择的业务类型所有的2级标签(用来匹配的)
            $industry_type_son_par  = [];//当前用户选中行业的二级标签所属的一级标签(用来匹配的)
            $size                   = [];//当前用户选择的投融资规模(用来匹配的)
            $to_province            = [];//当前用户选择的所在省份(用来匹配的)
            $province_all           = 0;
            
            foreach ($dl as $k => $v) {
                $sign = $dt->field('sign')->where(['dt_id'=>$v['dict_type_id']])->find();
                
                //业务类型下的二级标签
                if($sign['sign'] == 'service_type'){
                    if($v['dict_fid'] > 0){
                        $service_type_son[] = $v['dict_id'];
                    }
                }
                
                //所属行业下的一级标签
                if($sign['sign'] == 'industry'){
                    if($v['dict_fid'] > 0){
                        $industry_type_son_par[] = $v['dict_fid'];
                    }
                }
                
                //投融资规模
                if($sign['sign'] == 'size'){
                    $size[] = $v['dict_id'];
                }
                
                //所在省份
                if($sign['sign'] == 'to_province'){
                    if($v['dict_id']==328){
                        $province_all = 1;
                    }
                    $to_province[] = $v['dict_id'];
                }
            }
            //标签为全国的时候无需区别
            if($province_all==1){
                $to_province       = [];
            }
            $model = model('Dict');
            $size_str = "";
            if(!empty($size)){
                foreach ($size as $va) {
                    $value = $model->field('value')->where(['id'=>$va])->find();
                    if ($value['value'] == '500万以下') {
                        $size_str .= 'or.invest_scale<500 and ';
                    }elseif ($value['value'] == '500-1999万') {
                        $size_str .= '(or.invest_scale>=500 && pr.financing_scale<=1999) and ';
                    }elseif ($value['value'] == '2000-3999万') {
                        $size_str .= '(or.invest_scale>=2000 && pr.financing_scale<=3999) and ';
                    }elseif ($value['value'] == '4000-6999万') {
                        $size_str .= '(or.invest_scale>=4000 && pr.financing_scale<=6999) and ';
                    }elseif ($value['value'] == '7000-9999万') {
                        $size_str .= '(or.invest_scale>=7000 && pr.financing_scale<=9999) and ';
                    }elseif ($value['value'] == '1亿以上 ') {
                        $size_str .= 'or.invest_scale>=10000 and ';
                    }
                }
            }
            
            $sql  = "";
            $sql1 = "";
            $sql2 = "";
            $sql3 = "";
            //行业一级分类为股票与新三板
            $sql1 = "SELECT DISTINCT aa.or_id as id,
                    		m.userPhoto as url,
                    		m.realName as name,
                    		aa.name as top,
                    		aa.business_des as bottom,
                    		aa.view_num,
                    		aa.type,
                    		aa.create_time,
                    		aa.rank,
                    		aa.is_top,
                    		aa.is_care, 
                            aa.validity_period 
                            from fic_organize_require aa 
                            LEFT JOIN fic_member m on aa.uid=m.uid LEFT JOIN fic_organize_require_dict ord on aa.or_id=ord.or_id
		                    where aa.is_show = 1 and aa.`status` = 1 and ord.dict_fid  in (234,243) ";
            
            //匹配业务类型二级标签
            if(!empty($service_type_son)){
                $sql1 .= "and ord.dict_id in (".implode(',', $service_type_son).") ";
            }
            if($size_str!==""){
                $sql1 .= " and ".$size_str;//投资规模
            }
            
            //匹配所属行业未选中二级的一级标签
            if(!empty($industry_type_son_par)){
                $sql2 = "SELECT DISTINCT tt.id,
                            	tt.url,
                            	tt.name,
                            	tt.top,
                            	tt.bottom,
                            	tt.view_num,
                            	tt.type,
                            	tt.create_time,
                            	tt.rank,
                            	tt.is_top,
                            	tt.is_care,
                            	tt.validity_period from ( ".$sql1." ) as tt ";
                $sql2 .= "LEFT JOIN  fic_organize_require_dict ttord on tt.id=ttord.or_id
                          where ttord.dict_id  in (".implode(',', $industry_type_son_par).") ";
                
                if(!empty($to_province)){
                    $sql3 = "SELECT * from (".$sql2.") as zz LEFT JOIN  fic_organize_require_dict zzord on zz.id=zzord.or_id where  zzord.dict_id in ( ".implode(',', $to_province).") ";
                    $sql = $sql3;
                }else{
                    $sql = $sql2;
                }
            }else{
                if(!empty($to_province)){
                    $sql2 = "SELECT DISTINCT tt.id,
                            	tt.url,
                            	tt.name,
                            	tt.top,
                            	tt.bottom,
                            	tt.view_num,
                            	tt.type,
                            	tt.create_time,
                            	tt.rank,
                            	tt.is_top,
                            	tt.is_care,
                            	tt.validity_period from ( ".$sql1." ) as tt ";
                    $sql2 .= "LEFT JOIN  fic_organize_require_dict ttord on tt.id=ttord.or_id
                          where ttord.dict_id  in ( ".implode(',', $to_province).") ";
                    $sql = $sql2;
                }else{
                    $sql = $sql1;
                }
            }
            $sql .= " ORDER BY is_top asc,is_care asc,rank desc,view_num desc,create_time desc ";
            $all_result      = $this->query($sql);
        }
        //处理数据(添加标签,类型标签)
        $dict = model('Dict');
        $ord  = model('OrganizeRequireDict');
        $dt   = model('DictType');
        foreach ($all_result as $k => $v) {
            
            $is_vaild = prorStatus($v['create_time'],$v['validity_period']);
            if ($is_vaild) {
                $label = [];
                $all_business = $ord->alias('ord')
                ->join('DictType dt','ord.dict_type_id=dt.dt_id')
                ->join('Dict d','ord.dict_id=d.id')
                ->where(['dt.sign'=>'service_type'])
                ->where(['ord.or_id'=>$v['id']])
                ->field('d.value')
                ->select()
                ->toArray();
                if (!empty($all_business)) {
                    $label = array_column($all_business, 'value');
                }
                
                if (count($label)>3) {
                    $label = array_slice($label,0,3);
                }
                $all_result[$k]['bottom']         = subStrLen($v['bottom'],23);
                $all_result[$k]['label']          = $label;
                if ($v['is_care'] == 1) {
                    $all_result[$k]['type_label'] = '精选资金';
                } else {
                    $all_result[$k]['type_label'] = '资金';
                }
            } else {
                unset($all_result[$k]);
            }
            
        }
        $all_result = array_slice($all_result,$data['offset'],$data['length']);
        if (empty($all_result)) {
            $this->code = 201;
            $this->msg = '没有更多股权或新三板的资金';
            return $this->result('',$this->code,$this->msg);
        }
        
        return $this->result($all_result); 
    }

    
    //市场-债权栏-资金列表
    public function getClaimsRightOrganizeRequireMarketList($data = [])
    {

        $dl = model('DictLabel');
        $dl = $dl->getDictLabelApi($data['uid']);
        
        $dict_fid = "";
        //用户无标签
        if(empty($dl)){
            $dict_fid = "329,332,339,344,351,355,366,369,373,375,384,388,396,402,408,410,412,414,418,420,448,474";
            $sql1 = "SELECT DISTINCT aa.or_id as id,
                    		m.userPhoto as url,
                    		m.realName as name,
                    		aa.name as top,
                    		aa.business_des as bottom,
                    		aa.view_num,
                    		aa.type,
                    		aa.create_time,
                    		aa.rank,
                    		aa.is_top,
                    		aa.is_care,
                            aa.validity_period
                            from fic_organize_require aa
                            LEFT JOIN fic_member m on aa.uid=m.uid LEFT JOIN fic_organize_require_dict ord on aa.or_id=ord.or_id
		                    where aa.is_show = 1 and aa.`status` = 1 and ord.dict_fid  in (250) ";
            
            $dict_fid = "329,332,339,344,351,355,366,369,373,375,384,388,396,402,408,410,412,414,418,420,448,474";
            $sql = "SELECT DISTINCT tt.id,
                            	tt.url,
                            	tt.name,
                            	tt.top,
                            	tt.bottom,
                            	tt.view_num,
                            	tt.type,
                            	tt.create_time,
                            	tt.rank,
                            	tt.is_top,
                            	tt.is_care,
                            	tt.validity_period from ( ".$sql1." ) as tt ";
            $sql .= "LEFT JOIN  fic_organize_require_dict ttord on tt.id=ttord.or_id
                          where ttord.dict_id  in (".$dict_fid.") ";
            
            $sql .= " ORDER BY is_top asc,is_care asc,rank desc,view_num desc,create_time desc ";
            $all_result      = $this->query($sql);
        }else{
            $dt = model('DictType');
            $service_type_son       = [];//当前用户选择的业务类型所有的2级标签(用来匹配的)
            $industry_type_son_par  = [];//当前用户选中行业的二级标签所属的一级标签(用来匹配的)
            $size                   = [];//当前用户选择的投融资规模(用来匹配的)
            $to_province            = [];//当前用户选择的所在省份(用来匹配的)
            $province_all           = 0;
            
            foreach ($dl as $k => $v) {
                $sign = $dt->field('sign')->where(['dt_id'=>$v['dict_type_id']])->find();
                
                //业务类型下的二级标签
                if($sign['sign'] == 'service_type'){
                    if($v['dict_fid'] > 0){
                        $service_type_son[] = $v['dict_id'];
                    }
                }
                
                //所属行业下的一级标签
                if($sign['sign'] == 'industry'){
                    if($v['dict_fid'] > 0){
                        $industry_type_son_par[] = $v['dict_fid'];
                    }
                }
                
                //投融资规模
                if($sign['sign'] == 'size'){
                    $size[] = $v['dict_id'];
                }
                
                //所在省份
                if($sign['sign'] == 'to_province'){
                    if($v['dict_id']==328){
                        $province_all = 1;
                    }
                    $to_province[] = $v['dict_id'];
                }
            }
            //标签为全国的时候无需区别
            if($province_all==1){
                $to_province       = [];
            }
            $model = model('Dict');
            $size_str = "";
            if(!empty($size)){
                foreach ($size as $va) {
                    $value = $model->field('value')->where(['id'=>$va])->find();
                    if ($value['value'] == '500万以下') {
                        $size_str .= 'or.invest_scale<500 and ';
                    }elseif ($value['value'] == '500-1999万') {
                        $size_str .= '(or.invest_scale>=500 && pr.financing_scale<=1999) and ';
                    }elseif ($value['value'] == '2000-3999万') {
                        $size_str .= '(or.invest_scale>=2000 && pr.financing_scale<=3999) and ';
                    }elseif ($value['value'] == '4000-6999万') {
                        $size_str .= '(or.invest_scale>=4000 && pr.financing_scale<=6999) and ';
                    }elseif ($value['value'] == '7000-9999万') {
                        $size_str .= '(or.invest_scale>=7000 && pr.financing_scale<=9999) and ';
                    }elseif ($value['value'] == '1亿以上 ') {
                        $size_str .= 'or.invest_scale>=10000 and ';
                    }
                }
            }
            
            $sql  = "";
            $sql1 = "";
            $sql2 = "";
            $sql3 = "";
            //行业一级分类为股票与新三板
            $sql1 = "SELECT DISTINCT aa.or_id as id,
                    		m.userPhoto as url,
                    		m.realName as name,
                    		aa.name as top,
                    		aa.business_des as bottom,
                    		aa.view_num,
                    		aa.type,
                    		aa.create_time,
                    		aa.rank,
                    		aa.is_top,
                    		aa.is_care,
                            aa.validity_period
                            from fic_organize_require aa
                            LEFT JOIN fic_member m on aa.uid=m.uid LEFT JOIN fic_organize_require_dict ord on aa.or_id=ord.or_id
		                    where aa.is_show = 1 and aa.`status` = 1 and ord.dict_fid  in (250) ";
            
            //匹配业务类型二级标签
            if(!empty($service_type_son)){
                $sql1 .= "and ord.dict_id in (".implode(',', $service_type_son).") ";
            }
            if($size_str!==""){
                $sql1 .= " and ".$size_str;//投资规模
            }
            
            //匹配所属行业未选中二级的一级标签
            if(!empty($industry_type_son_par)){
                $sql2 = "SELECT DISTINCT tt.id,
                            	tt.url,
                            	tt.name,
                            	tt.top,
                            	tt.bottom,
                            	tt.view_num,
                            	tt.type,
                            	tt.create_time,
                            	tt.rank,
                            	tt.is_top,
                            	tt.is_care,
                            	tt.validity_period from ( ".$sql1." ) as tt ";
                $sql2 .= "LEFT JOIN  fic_organize_require_dict ttord on tt.id=ttord.or_id
                          where ttord.dict_id  in (".implode(',', $industry_type_son_par).") ";
                
                if(!empty($to_province)){
                    $sql3 = "SELECT * from (".$sql2.") as zz LEFT JOIN  fic_organize_require_dict zzord on zz.id=zzord.or_id where  zzord.dict_id in ( ".implode(',', $to_province).") ";
                    $sql = $sql3;
                }else{
                    $sql = $sql2;
                }
            }else{
                if(!empty($to_province)){
                    $sql2 = "SELECT DISTINCT tt.id,
                            	tt.url,
                            	tt.name,
                            	tt.top,
                            	tt.bottom,
                            	tt.view_num,
                            	tt.type,
                            	tt.create_time,
                            	tt.rank,
                            	tt.is_top,
                            	tt.is_care,
                            	tt.validity_period from ( ".$sql1." ) as tt ";
                    $sql2 .= "LEFT JOIN  fic_organize_require_dict ttord on tt.id=ttord.or_id
                          where ttord.dict_id  in ( ".implode(',', $to_province).") ";
                    $sql = $sql2;
                }else{
                    $sql = $sql1;
                }
            }
            $sql .= " ORDER BY is_top asc,is_care asc,rank desc,view_num desc,create_time desc ";
            $all_result      = $this->query($sql);
        }
        //处理数据(添加标签,类型标签)
        $dict = model('Dict');
        $ord  = model('OrganizeRequireDict');
        $dt   = model('DictType');
        foreach ($all_result as $k => $v) {
            
            $is_vaild = prorStatus($v['create_time'],$v['validity_period']);
            if ($is_vaild) {
                $label = [];
                $all_business = $ord->alias('ord')
                ->join('DictType dt','ord.dict_type_id=dt.dt_id')
                ->join('Dict d','ord.dict_id=d.id')
                ->where(['dt.sign'=>'service_type'])
                ->where(['ord.or_id'=>$v['id']])
                ->field('d.value')
                ->select()
                ->toArray();
                if (!empty($all_business)) {
                    $label = array_column($all_business, 'value');
                }
                
                if (count($label)>3) {
                    $label = array_slice($label,0,3);
                }
                $all_result[$k]['bottom']         = subStrLen($v['bottom'],23);
                $all_result[$k]['label']          = $label;
                if ($v['is_care'] == 1) {
                    $all_result[$k]['type_label'] = '精选资金';
                } else {
                    $all_result[$k]['type_label'] = '资金';
                }
            } else {
                unset($all_result[$k]);
            }
            
        }
        $all_result = array_slice($all_result,$data['offset'],$data['length']);
        if (empty($all_result)) {
            $this->code = 201;
            $this->msg = '没有更多债权类的资金';
            return $this->result('',$this->code,$this->msg);
        }
        
        return $this->result($all_result); 
        
    }
    
    
    //市场-创业栏-资金
    public function getChuangYeRightOrganizeRequireMarketList($data = [])
    {
        
        $dl = model('DictLabel');
        $dl = $dl->getDictLabelApi($data['uid']);
        
        $dict_fid = "";
        //用户无标签
        if(empty($dl)){
            
            $sql1 = "SELECT DISTINCT aa.or_id as id,
                    		m.userPhoto as url,
                    		m.realName as name,
                    		aa.name as top,
                    		aa.business_des as bottom,
                    		aa.view_num,
                    		aa.type,
                    		aa.create_time,
                    		aa.rank,
                    		aa.is_top,
                    		aa.is_care,
                            aa.validity_period
                            from fic_organize_require aa
                            LEFT JOIN fic_member m on aa.uid=m.uid LEFT JOIN fic_organize_require_dict ord on aa.or_id=ord.or_id
		                    where aa.is_show = 1 and aa.`status` = 1 and ord.dict_fid  in (270) ";
            $dict_fid = "329,332,339,344,351,355,366,369,373,375,384,388,396,402,408,410,412,414,418,420,448,474";
            $sql = "SELECT DISTINCT tt.id,
                            	tt.url,
                            	tt.name,
                            	tt.top,
                            	tt.bottom,
                            	tt.view_num,
                            	tt.type,
                            	tt.create_time,
                            	tt.rank,
                            	tt.is_top,
                            	tt.is_care,
                            	tt.validity_period from ( ".$sql1." ) as tt ";
            $sql .= "LEFT JOIN  fic_organize_require_dict ttord on tt.id=ttord.or_id
                          where ttord.dict_id  in (".$dict_fid.") ";
            
            $sql .= " ORDER BY is_top asc,is_care asc,rank desc,view_num desc,create_time desc ";
            $all_result      = $this->query($sql);
        }else{
            $dt = model('DictType');
            $service_type_son       = [];//当前用户选择的业务类型所有的2级标签(用来匹配的)
            $industry_type_son_par  = [];//当前用户选中行业的二级标签所属的一级标签(用来匹配的)
            $size                   = [];//当前用户选择的投融资规模(用来匹配的)
            $to_province            = [];//当前用户选择的所在省份(用来匹配的)
            $province_all           = 0;
            
            foreach ($dl as $k => $v) {
                $sign = $dt->field('sign')->where(['dt_id'=>$v['dict_type_id']])->find();
                
                //业务类型下的二级标签
                if($sign['sign'] == 'service_type'){
                    if($v['dict_fid'] > 0){
                        $service_type_son[] = $v['dict_id'];
                    }
                }
                
                //所属行业下的一级标签
                if($sign['sign'] == 'industry'){
                    if($v['dict_fid'] > 0){
                        $industry_type_son_par[] = $v['dict_fid'];
                    }
                }
                
                //投融资规模
                if($sign['sign'] == 'size'){
                    $size[] = $v['dict_id'];
                }
                
                //所在省份
                if($sign['sign'] == 'to_province'){
                    if($v['dict_id']==328){
                        $province_all = 1;
                    }
                    $to_province[] = $v['dict_id'];
                }
            }
            //标签为全国的时候无需区别
            if($province_all==1){
                $to_province       = [];
            }
            $model = model('Dict');
            $size_str = "";
            if(!empty($size)){
                foreach ($size as $va) {
                    $value = $model->field('value')->where(['id'=>$va])->find();
                    if ($value['value'] == '500万以下') {
                        $size_str .= 'or.invest_scale<500 and ';
                    }elseif ($value['value'] == '500-1999万') {
                        $size_str .= '(or.invest_scale>=500 && pr.financing_scale<=1999) and ';
                    }elseif ($value['value'] == '2000-3999万') {
                        $size_str .= '(or.invest_scale>=2000 && pr.financing_scale<=3999) and ';
                    }elseif ($value['value'] == '4000-6999万') {
                        $size_str .= '(or.invest_scale>=4000 && pr.financing_scale<=6999) and ';
                    }elseif ($value['value'] == '7000-9999万') {
                        $size_str .= '(or.invest_scale>=7000 && pr.financing_scale<=9999) and ';
                    }elseif ($value['value'] == '1亿以上 ') {
                        $size_str .= 'or.invest_scale>=10000 and ';
                    }
                }
            }
            
            $sql  = "";
            $sql1 = "";
            $sql2 = "";
            $sql3 = "";
            //行业一级分类为股票与新三板
            $sql1 = "SELECT DISTINCT aa.or_id as id,
                    		m.userPhoto as url,
                    		m.realName as name,
                    		aa.name as top,
                    		aa.business_des as bottom,
                    		aa.view_num,
                    		aa.type,
                    		aa.create_time,
                    		aa.rank,
                    		aa.is_top,
                    		aa.is_care,
                            aa.validity_period
                            from fic_organize_require aa
                            LEFT JOIN fic_member m on aa.uid=m.uid LEFT JOIN fic_organize_require_dict ord on aa.or_id=ord.or_id
		                    where aa.is_show = 1 and aa.`status` = 1 and ord.dict_fid  in (270) ";
            
            //匹配业务类型二级标签
            if(!empty($service_type_son)){
                $sql1 .= "and ord.dict_id in (".implode(',', $service_type_son).") ";
            }
            if($size_str!==""){
                $sql1 .= " and ".$size_str;//投资规模
            }
            
            //匹配所属行业未选中二级的一级标签
            if(!empty($industry_type_son_par)){
                $sql2 = "SELECT DISTINCT tt.id,
                            	tt.url,
                            	tt.name,
                            	tt.top,
                            	tt.bottom,
                            	tt.view_num,
                            	tt.type,
                            	tt.create_time,
                            	tt.rank,
                            	tt.is_top,
                            	tt.is_care,
                            	tt.validity_period from ( ".$sql1." ) as tt ";
                $sql2 .= "LEFT JOIN  fic_organize_require_dict ttord on tt.id=ttord.or_id
                          where ttord.dict_id  in (".implode(',', $industry_type_son_par).") ";
                
                if(!empty($to_province)){
                    $sql3 = "SELECT * from (".$sql2.") as zz LEFT JOIN  fic_organize_require_dict zzord on zz.id=zzord.or_id where  zzord.dict_id in ( ".implode(',', $to_province).") ";
                    $sql = $sql3;
                }else{
                    $sql = $sql2;
                }
            }else{
                if(!empty($to_province)){
                    $sql2 = "SELECT DISTINCT tt.id,
                            	tt.url,
                            	tt.name,
                            	tt.top,
                            	tt.bottom,
                            	tt.view_num,
                            	tt.type,
                            	tt.create_time,
                            	tt.rank,
                            	tt.is_top,
                            	tt.is_care,
                            	tt.validity_period from ( ".$sql1." ) as tt ";
                    $sql2 .= "LEFT JOIN  fic_organize_require_dict ttord on tt.id=ttord.or_id
                          where ttord.dict_id  in ( ".implode(',', $to_province).") ";
                    $sql = $sql2;
                }else{
                    $sql = $sql1;
                }
            }
            $sql .= " ORDER BY is_top asc,is_care asc,rank desc,view_num desc,create_time desc ";
            $all_result      = $this->query($sql);
        }
        //处理数据(添加标签,类型标签)
        $dict = model('Dict');
        $ord  = model('OrganizeRequireDict');
        $dt   = model('DictType');
        foreach ($all_result as $k => $v) {
            
            $is_vaild = prorStatus($v['create_time'],$v['validity_period']);
            if ($is_vaild) {
                $label = [];
                $all_business = $ord->alias('ord')
                ->join('DictType dt','ord.dict_type_id=dt.dt_id')
                ->join('Dict d','ord.dict_id=d.id')
                ->where(['dt.sign'=>'service_type'])
                ->where(['ord.or_id'=>$v['id']])
                ->field('d.value')
                ->select()
                ->toArray();
                if (!empty($all_business)) {
                    $label = array_column($all_business, 'value');
                }
                
                if (count($label)>3) {
                    $label = array_slice($label,0,3);
                }
                $all_result[$k]['bottom']         = subStrLen($v['bottom'],23);
                $all_result[$k]['label']          = $label;
                if ($v['is_care'] == 1) {
                    $all_result[$k]['type_label'] = '精选资金';
                } else {
                    $all_result[$k]['type_label'] = '资金';
                }
            } else {
                unset($all_result[$k]);
            }
            
        }
        $all_result = array_slice($all_result,$data['offset'],$data['length']);
        if (empty($all_result)) {
            $this->code = 201;
            $this->msg = '没有更多创业孵化的资金';
            return $this->result('',$this->code,$this->msg);
        }
        
        return $this->result($all_result); 
    }

    //市场-上市栏-资金列表
    public function getListRightOrganizeRequireMarketList($data = [])
    {
        $dl = model('DictLabel');
        $dl = $dl->getDictLabelApi($data['uid']);
        
        $dict_fid = "";
        //用户无标签
        if(empty($dl)){
            $sql1 = "SELECT DISTINCT aa.or_id as id,
                    		m.userPhoto as url,
                    		m.realName as name,
                    		aa.name as top,
                    		aa.business_des as bottom,
                    		aa.view_num,
                    		aa.type,
                    		aa.create_time,
                    		aa.rank,
                    		aa.is_top,
                    		aa.is_care,
                            aa.validity_period
                            from fic_organize_require aa
                            LEFT JOIN fic_member m on aa.uid=m.uid LEFT JOIN fic_organize_require_dict ord on aa.or_id=ord.or_id
		                    where aa.is_show = 1 and aa.`status` = 1 and ord.dict_fid  in (262) ";
            $dict_fid = "329,332,339,344,351,355,366,369,373,375,384,388,396,402,408,410,412,414,418,420,448,474";
            $sql = "SELECT DISTINCT tt.id,
                            	tt.url,
                            	tt.name,
                            	tt.top,
                            	tt.bottom,
                            	tt.view_num,
                            	tt.type,
                            	tt.create_time,
                            	tt.rank,
                            	tt.is_top,
                            	tt.is_care,
                            	tt.validity_period from ( ".$sql1." ) as tt ";
            $sql .= "LEFT JOIN  fic_organize_require_dict ttord on tt.id=ttord.or_id
                          where ttord.dict_id  in (".$dict_fid.") ";
            
            $sql .= " ORDER BY is_top asc,is_care asc,rank desc,view_num desc,create_time desc ";
            $all_result      = $this->query($sql);
        }else{
            $dt = model('DictType');
            $service_type_son       = [];//当前用户选择的业务类型所有的2级标签(用来匹配的)
            $industry_type_son_par  = [];//当前用户选中行业的二级标签所属的一级标签(用来匹配的)
            $size                   = [];//当前用户选择的投融资规模(用来匹配的)
            $to_province            = [];//当前用户选择的所在省份(用来匹配的)
            $province_all           = 0;
            
            foreach ($dl as $k => $v) {
                $sign = $dt->field('sign')->where(['dt_id'=>$v['dict_type_id']])->find();
                
                //业务类型下的二级标签
                if($sign['sign'] == 'service_type'){
                    if($v['dict_fid'] > 0){
                        $service_type_son[] = $v['dict_id'];
                    }
                }
                
                //所属行业下的一级标签
                if($sign['sign'] == 'industry'){
                    if($v['dict_fid'] > 0){
                        $industry_type_son_par[] = $v['dict_fid'];
                    }
                }
                
                //投融资规模
                if($sign['sign'] == 'size'){
                    $size[] = $v['dict_id'];
                }
                
                //所在省份
                if($sign['sign'] == 'to_province'){
                    if($v['dict_id']==328){
                        $province_all = 1;
                    }
                    $to_province[] = $v['dict_id'];
                }
            }
            //标签为全国的时候无需区别
            if($province_all==1){
                $to_province       = [];
            }
            $model = model('Dict');
            $size_str = "";
            if(!empty($size)){
                foreach ($size as $va) {
                    $value = $model->field('value')->where(['id'=>$va])->find();
                    if ($value['value'] == '500万以下') {
                        $size_str .= 'or.invest_scale<500 and ';
                    }elseif ($value['value'] == '500-1999万') {
                        $size_str .= '(or.invest_scale>=500 && pr.financing_scale<=1999) and ';
                    }elseif ($value['value'] == '2000-3999万') {
                        $size_str .= '(or.invest_scale>=2000 && pr.financing_scale<=3999) and ';
                    }elseif ($value['value'] == '4000-6999万') {
                        $size_str .= '(or.invest_scale>=4000 && pr.financing_scale<=6999) and ';
                    }elseif ($value['value'] == '7000-9999万') {
                        $size_str .= '(or.invest_scale>=7000 && pr.financing_scale<=9999) and ';
                    }elseif ($value['value'] == '1亿以上 ') {
                        $size_str .= 'or.invest_scale>=10000 and ';
                    }
                }
            }
            
            $sql  = "";
            $sql1 = "";
            $sql2 = "";
            $sql3 = "";
            //行业一级分类为股票与新三板
            $sql1 = "SELECT DISTINCT aa.or_id as id,
                    		m.userPhoto as url,
                    		m.realName as name,
                    		aa.name as top,
                    		aa.business_des as bottom,
                    		aa.view_num,
                    		aa.type,
                    		aa.create_time,
                    		aa.rank,
                    		aa.is_top,
                    		aa.is_care,
                            aa.validity_period
                            from fic_organize_require aa
                            LEFT JOIN fic_member m on aa.uid=m.uid LEFT JOIN fic_organize_require_dict ord on aa.or_id=ord.or_id
		                    where aa.is_show = 1 and aa.`status` = 1 and ord.dict_fid  in (262) ";
            
            //匹配业务类型二级标签
            if(!empty($service_type_son)){
                $sql1 .= "and ord.dict_id in (".implode(',', $service_type_son).") ";
            }
            if($size_str!==""){
                $sql1 .= " and ".$size_str;//投资规模
            }
            
            //匹配所属行业未选中二级的一级标签
            if(!empty($industry_type_son_par)){
                $sql2 = "SELECT DISTINCT tt.id,
                            	tt.url,
                            	tt.name,
                            	tt.top,
                            	tt.bottom,
                            	tt.view_num,
                            	tt.type,
                            	tt.create_time,
                            	tt.rank,
                            	tt.is_top,
                            	tt.is_care,
                            	tt.validity_period from ( ".$sql1." ) as tt ";
                $sql2 .= "LEFT JOIN  fic_organize_require_dict ttord on tt.id=ttord.or_id
                          where ttord.dict_id  in (".implode(',', $industry_type_son_par).") ";
                
                if(!empty($to_province)){
                    $sql3 = "SELECT * from (".$sql2.") as zz LEFT JOIN  fic_organize_require_dict zzord on zz.id=zzord.or_id where  zzord.dict_id in ( ".implode(',', $to_province).") ";
                    $sql = $sql3;
                }else{
                    $sql = $sql2;
                }
            }else{
                if(!empty($to_province)){
                    $sql2 = "SELECT DISTINCT tt.id,
                            	tt.url,
                            	tt.name,
                            	tt.top,
                            	tt.bottom,
                            	tt.view_num,
                            	tt.type,
                            	tt.create_time,
                            	tt.rank,
                            	tt.is_top,
                            	tt.is_care,
                            	tt.validity_period from ( ".$sql1." ) as tt ";
                    $sql2 .= "LEFT JOIN  fic_organize_require_dict ttord on tt.id=ttord.or_id
                          where ttord.dict_id  in ( ".implode(',', $to_province).") ";
                    $sql = $sql2;
                }else{
                    $sql = $sql1;
                }
            }
            $sql .= " ORDER BY is_top asc,is_care asc,rank desc,view_num desc,create_time desc ";
            $all_result      = $this->query($sql);
        }
        //处理数据(添加标签,类型标签)
        $dict = model('Dict');
        $ord  = model('OrganizeRequireDict');
        $dt   = model('DictType');
        foreach ($all_result as $k => $v) {
            
            $is_vaild = prorStatus($v['create_time'],$v['validity_period']);
            if ($is_vaild) {
                $label = [];
                $all_business = $ord->alias('ord')
                ->join('DictType dt','ord.dict_type_id=dt.dt_id')
                ->join('Dict d','ord.dict_id=d.id')
                ->where(['dt.sign'=>'service_type'])
                ->where(['ord.or_id'=>$v['id']])
                ->field('d.value')
                ->select()
                ->toArray();
                if (!empty($all_business)) {
                    $label = array_column($all_business, 'value');
                }
                
                if (count($label)>3) {
                    $label = array_slice($label,0,3);
                }
                $all_result[$k]['bottom']         = subStrLen($v['bottom'],23);
                $all_result[$k]['label']          = $label;
                if ($v['is_care'] == 1) {
                    $all_result[$k]['type_label'] = '精选资金';
                } else {
                    $all_result[$k]['type_label'] = '资金';
                }
            } else {
                unset($all_result[$k]);
            }
            
        }
        $all_result = array_slice($all_result,$data['offset'],$data['length']);
        if (empty($all_result)) {
            $this->code = 201;
            $this->msg = '没有更多上市公司类的资金';
            return $this->result('',$this->code,$this->msg);
        }
        
        return $this->result($all_result); 
    }

    //市场-其他栏-资金列表
    public function getOtherRightOrganizeRequireMarketList($data = [])
    {

        $dl = model('DictLabel');
        $dl = $dl->getDictLabelApi($data['uid']);
        
        $dict_fid = "";
        //用户无标签
        if(empty($dl)){
            $sql1 = "SELECT DISTINCT aa.or_id as id,
                    		m.userPhoto as url,
                    		m.realName as name,
                    		aa.name as top,
                    		aa.business_des as bottom,
                    		aa.view_num,
                    		aa.type,
                    		aa.create_time,
                    		aa.rank,
                    		aa.is_top,
                    		aa.is_care,
                            aa.validity_period
                            from fic_organize_require aa
                            LEFT JOIN fic_member m on aa.uid=m.uid LEFT JOIN fic_organize_require_dict ord on aa.or_id=ord.or_id
		                    where aa.is_show = 1 and aa.`status` = 1 and ord.dict_fid  in (280) ";
            $dict_fid = "329,332,339,344,351,355,366,369,373,375,384,388,396,402,408,410,412,414,418,420,448,474";
            $sql = "SELECT DISTINCT tt.id,
                            	tt.url,
                            	tt.name,
                            	tt.top,
                            	tt.bottom,
                            	tt.view_num,
                            	tt.type,
                            	tt.create_time,
                            	tt.rank,
                            	tt.is_top,
                            	tt.is_care,
                            	tt.validity_period from ( ".$sql1." ) as tt ";
            $sql .= "LEFT JOIN  fic_organize_require_dict ttord on tt.id=ttord.or_id
                          where ttord.dict_id  in (".$dict_fid.") ";
            
            $sql .= " ORDER BY is_top asc,is_care asc,rank desc,view_num desc,create_time desc ";
            $all_result      = $this->query($sql);
        }else{
            $dt = model('DictType');
            $service_type_son       = [];//当前用户选择的业务类型所有的2级标签(用来匹配的)
            $industry_type_son_par  = [];//当前用户选中行业的二级标签所属的一级标签(用来匹配的)
            $size                   = [];//当前用户选择的投融资规模(用来匹配的)
            $to_province            = [];//当前用户选择的所在省份(用来匹配的)
            $province_all           = 0;
            
            foreach ($dl as $k => $v) {
                $sign = $dt->field('sign')->where(['dt_id'=>$v['dict_type_id']])->find();
                
                //业务类型下的二级标签
                if($sign['sign'] == 'service_type'){
                    if($v['dict_fid'] > 0){
                        $service_type_son[] = $v['dict_id'];
                    }
                }
                
                //所属行业下的一级标签
                if($sign['sign'] == 'industry'){
                    if($v['dict_fid'] > 0){
                        $industry_type_son_par[] = $v['dict_fid'];
                    }
                }
                
                //投融资规模
                if($sign['sign'] == 'size'){
                    $size[] = $v['dict_id'];
                }
                
                //所在省份
                if($sign['sign'] == 'to_province'){
                    if($v['dict_id']==328){
                        $province_all = 1;
                    }
                    $to_province[] = $v['dict_id'];
                }
            }
            //标签为全国的时候无需区别
            if($province_all==1){
                $to_province       = [];
            }
            $model = model('Dict');
            $size_str = "";
            if(!empty($size)){
                foreach ($size as $va) {
                    $value = $model->field('value')->where(['id'=>$va])->find();
                    if ($value['value'] == '500万以下') {
                        $size_str .= 'or.invest_scale<500 and ';
                    }elseif ($value['value'] == '500-1999万') {
                        $size_str .= '(or.invest_scale>=500 && pr.financing_scale<=1999) and ';
                    }elseif ($value['value'] == '2000-3999万') {
                        $size_str .= '(or.invest_scale>=2000 && pr.financing_scale<=3999) and ';
                    }elseif ($value['value'] == '4000-6999万') {
                        $size_str .= '(or.invest_scale>=4000 && pr.financing_scale<=6999) and ';
                    }elseif ($value['value'] == '7000-9999万') {
                        $size_str .= '(or.invest_scale>=7000 && pr.financing_scale<=9999) and ';
                    }elseif ($value['value'] == '1亿以上 ') {
                        $size_str .= 'or.invest_scale>=10000 and ';
                    }
                }
            }
            
            $sql  = "";
            $sql1 = "";
            $sql2 = "";
            $sql3 = "";
            //行业一级分类为股票与新三板
            $sql1 = "SELECT DISTINCT aa.or_id as id,
                    		m.userPhoto as url,
                    		m.realName as name,
                    		aa.name as top,
                    		aa.business_des as bottom,
                    		aa.view_num,
                    		aa.type,
                    		aa.create_time,
                    		aa.rank,
                    		aa.is_top,
                    		aa.is_care,
                            aa.validity_period
                            from fic_organize_require aa
                            LEFT JOIN fic_member m on aa.uid=m.uid LEFT JOIN fic_organize_require_dict ord on aa.or_id=ord.or_id
		                    where aa.is_show = 1 and aa.`status` = 1 and ord.dict_fid  in (280) ";
            
            //匹配业务类型二级标签
            if(!empty($service_type_son)){
                $sql1 .= "and ord.dict_id in (".implode(',', $service_type_son).") ";
            }
            if($size_str!==""){
                $sql1 .= " and ".$size_str;//投资规模
            }
            
            //匹配所属行业未选中二级的一级标签
            if(!empty($industry_type_son_par)){
                $sql2 = "SELECT DISTINCT tt.id,
                            	tt.url,
                            	tt.name,
                            	tt.top,
                            	tt.bottom,
                            	tt.view_num,
                            	tt.type,
                            	tt.create_time,
                            	tt.rank,
                            	tt.is_top,
                            	tt.is_care,
                            	tt.validity_period from ( ".$sql1." ) as tt ";
                $sql2 .= "LEFT JOIN  fic_organize_require_dict ttord on tt.id=ttord.or_id
                          where ttord.dict_id  in (".implode(',', $industry_type_son_par).") ";
                
                if(!empty($to_province)){
                    $sql3 = "SELECT * from (".$sql2.") as zz LEFT JOIN  fic_organize_require_dict zzord on zz.id=zzord.or_id where  zzord.dict_id in ( ".implode(',', $to_province).") ";
                    $sql = $sql3;
                }else{
                    $sql = $sql2;
                }
            }else{
                if(!empty($to_province)){
                    $sql2 = "SELECT DISTINCT tt.id,
                            	tt.url,
                            	tt.name,
                            	tt.top,
                            	tt.bottom,
                            	tt.view_num,
                            	tt.type,
                            	tt.create_time,
                            	tt.rank,
                            	tt.is_top,
                            	tt.is_care,
                            	tt.validity_period from ( ".$sql1." ) as tt ";
                    $sql2 .= "LEFT JOIN  fic_organize_require_dict ttord on tt.id=ttord.or_id
                          where ttord.dict_id  in ( ".implode(',', $to_province).") ";
                    $sql = $sql2;
                }else{
                    $sql = $sql1;
                }
            }
            $sql .= " ORDER BY is_top asc,is_care asc,rank desc,view_num desc,create_time desc ";
            $all_result      = $this->query($sql);
        }
        //处理数据(添加标签,类型标签)
        $dict = model('Dict');
        $ord  = model('OrganizeRequireDict');
        $dt   = model('DictType');
        foreach ($all_result as $k => $v) {
            
            $is_vaild = prorStatus($v['create_time'],$v['validity_period']);
            if ($is_vaild) {
                $label = [];
                $all_business = $ord->alias('ord')
                ->join('DictType dt','ord.dict_type_id=dt.dt_id')
                ->join('Dict d','ord.dict_id=d.id')
                ->where(['dt.sign'=>'service_type'])
                ->where(['ord.or_id'=>$v['id']])
                ->field('d.value')
                ->select()
                ->toArray();
                if (!empty($all_business)) {
                    $label = array_column($all_business, 'value');
                }
                
                if (count($label)>3) {
                    $label = array_slice($label,0,3);
                }
                $all_result[$k]['bottom']         = subStrLen($v['bottom'],23);
                $all_result[$k]['label']          = $label;
                if ($v['is_care'] == 1) {
                    $all_result[$k]['type_label'] = '精选资金';
                } else {
                    $all_result[$k]['type_label'] = '资金';
                }
            } else {
                unset($all_result[$k]);
            }
            
        }
        $all_result = array_slice($all_result,$data['offset'],$data['length']);
        if (empty($all_result)) {
            $this->code = 201;
            $this->msg = '没有更多金融产品类的资金';
            return $this->result('',$this->code,$this->msg);
        }
        
        return $this->result($all_result); 
    }
}