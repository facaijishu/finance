<?php 
namespace app\home\model;
use think\Db;
class ProjectRequire extends BaseModel
{
	//首页智能推荐(游客/资金方)前八条展示
    public function getProjectRequireLimitListApi($role_type = 0,$uid = 0,$length = 8)
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
        //用户无标签
        if(empty($dl)){
            $all_result  = $this->alias('pr')
                                ->join('Member m','pr.uid=m.uid')
                                ->join('Dict d','pr.top_dict_id=d.id')
                                ->join('Dict d2','pr.dict_id=d2.id')
                                ->join('Dict d3','pr.top_industry_id=d3.id')
                                ->field([
                                    'pr.pr_id'          => 'id',
                                    'm.userPhoto'       => 'url',
                                    'm.realName'        => 'name',
                                    'pr.name'           => 'top',
                                    'concat_ws(",",d.value,d2.value,d3.value)'=>'label',
                                    'pr.business_des'   =>'bottom',
                                    'pr.view_num',
                                    'pr.type',
                                ])
                                ->where([
                                    'pr.is_show' => 1,
                                    'pr.status'  => 1,
                                ])
                                ->order('pr.is_top asc,pr.is_care asc,pr.rank desc,pr.view_num desc,pr.create_time desc')
                                ->limit($length)
                                ->select()
                                ->toArray();
        }else{
            $dt = model('DictType');
            $service_type_son       = [];//当前用户选择的业务类型所有的2级标签(用来匹配的)
            $service_type_son_par   = [];//当前用户选择的业务类型所有的2级标签(用来匹配的)
            $industry_type_son      = [];//当前用户选中行业的二级标签所属的一级标签(用来匹配的)
            $industry_type_son_par  = [];//当前用户选中行业的二级标签所属的一级标签(用来匹配的)
            $size                   = [];//当前用户选择的投融资规模(用来匹配的)
            $to_province            = [];//当前用户选择的所在省份(用来匹配的)
            $province_all           = 0;
            foreach ($dl as $k => $v) {
                $sign = $dt->field('sign')->where(['dt_id'=>$v['dict_type_id']])->find();
                
                //业务类型下的标签
                if($sign['sign'] == 'service_type'){
                    if($v['dict_fid'] > 0){
                        $service_type_son[]     = $v['dict_id'];
                        $service_type_son_par[] = $v['dict_fid'];
                    }
                }
                
                
                //所属行业下的一级标签
                if($sign['sign'] == 'industry'){
                    if($v['dict_fid'] > 0){
                        $industry_type_son[]     = $v['dict_id'];
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
            
            $match_str = '';
            
            
            //匹配业务类型一级标签
            if(!empty($service_type_son_par)){
                $match_str .= 'pr.top_dict_id in ('.implode(',', $service_type_son_par).') and ';
            }
            //匹配业务类型二级标签
            if(!empty($service_type_son)){
                $match_str .= 'pr.dict_id in ('.implode(',', $service_type_son).') and ';
            }
            
            //匹配所属行业未选中二级的一级标签
            if(!empty($industry_type_son_par)){
                $match_str .= 'pr.top_industry_id in ('.implode(',', $industry_type_son_par).') and ';
            }
            
            //匹配所属行业未选中二级的二级标签
            if(!empty($industry_type_son)){
                $match_str .= 'pr.industry_id in ('.implode(',', $industry_type_son).') and ';
            }
            
            //匹配所在省份
            if(!empty($to_province)){
                $match_str .= 'pr.province_id in ('.implode(',', $to_province).') and  ';
            }
            
            $model = model('Dict');
            if(!empty($size)){
                foreach ($size as $va) {
                    $value = $model->field('value')->where(['id'=>$va])->find();
                    if ($value['value'] == '500万以下') {
                        $match_str .= 'pr.financing_scale<500 and ';
                    }elseif ($value['value'] == '500-1999万') {
                        $match_str .= '(pr.financing_scale>=500 && pr.financing_scale<=1999) and ';
                    }elseif ($value['value'] == '2000-3999万') {
                        $match_str .= '(pr.financing_scale>=2000 && pr.financing_scale<=3999) and ';
                    }elseif ($value['value'] == '4000-6999万') {
                        $match_str .= '(pr.financing_scale>=4000 && pr.financing_scale<=6999) and ';
                    }elseif ($value['value'] == '7000-9999万') {
                        $match_str .= '(pr.financing_scale>=7000 && pr.financing_scale<=9999) and ';
                    }elseif ($value['value'] == '1亿以上 ') {
                        $match_str .= 'pr.financing_scale>=10000 and ';
                    }
                }
            }
            
            
            $match_str   = rtrim($match_str,' and ');
            $all_result  = $this->alias('pr')
                                ->join('Member m','pr.uid=m.uid')
                                ->join('Dict d','pr.top_dict_id=d.id')
                                ->join('Dict d2','pr.dict_id=d2.id')
                                ->join('Dict d3','pr.top_industry_id=d3.id')
                                ->field([
                                    'pr.pr_id'          => 'id',
                                    'm.userPhoto'       => 'url',
                                    'm.realName'        => 'name',
                                    'pr.name'           => 'top',
                                    'concat_ws(",",d.value,d2.value,d3.value)'=>'label',
                                    'pr.business_des'   =>'bottom',
                                    'pr.view_num',
                                    'pr.type',
                                    'pr.create_time',
                                    'pr.rank',
                                    'pr.is_top',
                                    'pr.is_care',
                                    'pr.validity_period',
                                ])
                                ->where([
                                    'pr.is_show' => 1,
                                    'pr.status'  => 1,
                                ])
                                ->where($match_str)
                                ->order('pr.is_top asc,pr.is_care asc,pr.rank desc,pr.view_num desc,pr.create_time desc')
                                ->limit($length)
                                ->select()
                                ->toArray();
        }
  
        if(empty($all_result)){
            $this->code = 201;
            $this->msg = '数据不存在';
            return $this->result('',$this->code,$this->msg);
        }
        
        foreach ($all_result as &$v) {
            $v['label'] = explode(',', $v['label']);
            $v['type_label'] = '项目';
            
        }
        return $this->result($all_result); 
        
    }

    //执行发布项目
    public function addProjectRequireApi($data)
    {
       
       $this->startTrans();
       try {
          $this->allowField(true)->save($data);
          //每发布一条项目,member对应的业务数量+1
          model('Member')->where(['uid'=>$data['uid']])->setInc('service_num');
          //同时更新到session中
          $info = model('Member')->where(['uid'=>$data['uid']])->find()->toArray();
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
    
    //获取发布项目的详情
    public function getProjectRequireDetail($data = [])
    {
        $white  = model("whiteUid");
        if($white->getWhite($data['uid'])){
            //查看人数+1
            $this->where(['pr_id'=>$data['id'],'is_show'=>1,'status'=>1])->setInc('view_num');
        }
        //根据id检验项目是否存在
        $re = $this->where(['pr_id'=>$data['id'],'is_show'=>1,'status'=>1])->find();

        if (empty($re)) {
           $this->code  = 256;
           $this->msg   = '详情不存在';
           return $this->result('',$this->code,$this->msg);
        }
        
        //查询发布该项目的用户的信息
        $userInfo = model('Member')->where(['uid'=>$re['uid']])->find();
        if (empty($userInfo)) {
           $this->code  = 208;
           $this->msg   = '缺少用户id参数';
           return $this->result('',$this->code,$this->msg);  
        }

        //查询该项目所属的一级,二级业务类型,一级,二级行业类型(二级行业可为空),省份名称
        $top_dict = model('Dict')->field('value')->where(['id'=>$re['top_dict_id']])->find();
        if (empty($top_dict)) {
            $top_dict['value'] = '';
        }
        $dict = model('Dict')->field('value')->where(['id'=>$re['dict_id']])->find();
        if (empty($dict)) {
            $dict['value'] = '';
        }

        $top_industry = model('Dict')->field('value')
                                     ->where(['id'=>$re['top_industry_id']])
                                     ->find();                                                      
        $industry = model('Dict')->field('value')->where(['id'=>$re['industry_id']])->find();

        //如果只有一级行业就展示一级,如果有二级,就展示二级
        if (!empty($top_industry) && empty($industry)) {
            $hangye = $top_industry['value'];
        }elseif (!empty($top_industry) && !empty($industry)) {
            $hangye = $industry['value'];
        }else{
            $hangye = '';
        }
        
        $province = model('Dict')->field('value')->where(['id'=>$re['province_id']])->find();
        if (empty($province)) {
            $province['value'] = '';
        }
        
        //存放所有详情信息  
        $result = [];
        $org_info['is_follow']= session('FINANCE_USER.is_follow');
        //存放发布该项目的用户信息和登录用户的关注状态 
        $user['uid']                = $userInfo['uid'];
        $user['userPhone']          = $userInfo['userPhone'];
        $user['userPhoto']          = $userInfo['userPhoto'];
        $user['realName']           = $userInfo['realName'];
        $user['company_jc']         = $userInfo['company_jc'];
        $user['position']           = $userInfo['position'];
        $user['contact_status']     = contact_status($re['contact_status']);
        $user['service_num']        = $userInfo['service_num'];
        $user['require_num']        = $userInfo['require_num'];
        
        $userFollow = model('Member')->where(['uid'=>$data['uid']])->find();
        $user['follow_flg']         = $userFollow['is_follow'];
        if (empty($userInfo)) {
            $this->code  = 208;
            $this->msg   = '缺少用户id参数';
            return $this->result('',$this->code,$this->msg);
        }
        
        //查询发布该项目的用户的有效业务的条数
        $now        = time();
        $pr_c       = model("ProjectRequire")->where("is_show = 1 and deadline >=$now and uid = ".$userInfo['uid'])->count('pr_id');
        $or_c       = model("OrganizeRequire")->where("is_show = 1 and deadline >=$now and uid = ".$userInfo['uid'])->count('or_id');
        $total_num  = $pr_c+$or_c;
        $user['total_num']        = $total_num;
        
        //定义分享
        $result['share'] = [];
        $result['share']['imgUrl']  = $user['userPhoto']; 
        //当前登录用户是否关注该项目发布人
        $fu = model('FollowUser')->where(['from_uid'=>$data['uid'],'to_uid'=>$userInfo['uid']])->find();
            if (empty($fu)) {
                //未关注
                $user['is_follow'] = '关注';
            }else{
                if ($fu['is_del'] == 1) {
                    //未关注
                    $user['is_follow'] = '关注';
                } 
               
                if ($fu['is_del'] == 2) {
                    //已关注
                    $user['is_follow'] = '已关注';
                } 
            }
        $result['owner'] = $user;

        //判断一级类型和二级类型,返回对应的详情
        switch ($top_dict['value']) {
          case '新三板':
            //1
            $part = [];
            $name = '项目名称:'.$re['name'];//项目名称
            if ($re['is_public'] == 1) {
                $name.='('.$re['stock_code'].')';//项目名称+股票代码
            }
            $part[] = $name;
            $part[] = $top_dict['value'].'-'.$dict['value'];//项目标签
            $part[] = $province['value'];//所在省份
            $part[] = $hangye;//所属行业
            if ($re['is_pre_ipo'] == 1) {
              $part[] = 'Pre-IPO';//是否pre-ipo  
            }
            if ($re['is_coach'] == 1) {
              $part[] = '报辅';
            }
    
            $part[] = '业务简介:'.$re['business_des'];
            $result['one'] = $part;
            
            
            //分享链接的内容
            $result['share']['title'] = $re['name'];
            $result['share']['desc']  = $userInfo['realName']."向你推荐";
            
            if ($dict['value'] == '定向增发') {
               
                //2
                $part2      = [];
                $part2[]    = '融资需求';
                $part2[]    = '融资规模:'.$re['financing_scale'].'万元';
                $part2[]    = '融资期限:'.getFinancingPeriod($re['period']);
                if ($re['price']) {
                   $part2[] = '定增价格:'.$re['price'].'元/股';
                }
                $part2[]    = '所属层级:'.level($re['level']);
                $part2[]    = '交易方式:'.trans_mode($re['trans_mode']);
                $result['two'] = $part2;
                //3
                $part3  = [];
                $part3[] = '盈利能力';
                $part3[] = '去年净利润:'.$re['ly_net_profit'].'万元';
                if ($re['ty_net_profit']) {
                    $part3[] = '今年净利润:'.$re['ty_net_profit'].'万元';
                }

                if ($re['pe_ratio']) {
                    $part3[] = '市盈率:'.$re['pe_ratio'];
                }
               
                $result['three'] = $part3;
                //4
                $part4 = [];
                $part4[] = '项目说明';
                $part4[] = $re['pro_highlight'];
                $result['four'] = $part4;
                
                $part5 = [];
                if ($re['financing_purpose']) {
                   //5
                   
                   $part5[] = '融资说明';
                   $part5[] = $re['financing_purpose'];
                   
                }
                $result['five'] = $part5;
               
            }elseif ($dict['value'] == '大宗交易') {
                //2
                $part2 = [];
                $part2[] = '融资需求';
                $part2[] = '总股本:'.$re['general_capital'].'万股';
                $part2[] = '可转让股数:'.$re['trans_stock_num'].'万股';
                if ($re['price']) {
                   $part2['price'] = '转让价格:'.$re['price'].'元/股';
                }
                $part2['is_bargain'] = '是否可议价:'.YesOrNo($re['is_bargain']);
                $part2['level'] = '所属层级:'.level($re['level']);
                $part2['trans_mode'] = '交易方式:'.trans_mode($re['trans_mode']);
                $result['two'] = $part2;
                //3
                $part3 = [];
                $part3['title'] = '盈利能力';
                $part3['ly_net_profit'] = '去年净利润:'.$re['ly_net_profit'].'万元';
                if ($re['ty_net_profit']) {
                   $part3['ty_net_profit'] = '今年净利润:'.$re['ty_net_profit'].'万元';
                }
                
                if ($re['pe_ratio']) {
                    $part3['pe_ratio'] = '市盈率:'.$re['pe_ratio'];
                }
               
                $result['three'] = $part3;
                //4
                $part4 = [];
                $part4['title'] = '项目说明';
                $part4['pro_highlight'] = $re['pro_highlight'];
                $result['four'] = $part4;

               
            }elseif ($dict['value'] == '股权质押') {
                //2
                $part2 = [];
                $part2[] = '融资需求';
                $part2[] = '融资规模:'.$re['financing_scale'].'万元';
                $part2[] = '融资期限:'.getFinancingPeriod($re['period']);
                $part2[] = '是否为大股东:'.YesOrNo($re['is_stockholder']);
                $part2[] = '所属层级:'.level($re['level']);
                $part2[] = '交易方式:'.trans_mode($re['trans_mode']);
                $result['two'] = $part2;
                //3
                $part3 = [];
                $part3[] = '盈利能力';
                $part3[] = '去年净利润:'.$re['ly_net_profit'].'万元';
                if ($re['ty_net_profit']) {
                   $part3[] = '今年净利润:'.$re['ty_net_profit'].'万元';
                   $part3[] = '质押率:'.$re['ltv_ratio'];   
                   $part3[] = '市盈率:'.$re['pe_ratio'];
                }
              
                $result['three'] = $part3;
                //4
                $part4 = [];
                $part4[] = '项目说明';
                $part4[] = $re['pro_highlight'];
                $result['four'] = $part4;
            }
            /*elseif ($dict['value'] == '资产抵押') {
                 //2
                $part2 = [];
                $part2[] = '融资需求';
                $part2[] = '融资规模:'.$re['financing_scale'].'万元';
                $part2[] = '融资期限:'.getFinancingPeriod($re['period']);
                $part2[] = '所属层级:'.level($re['level']);
                $part2[] = '交易方式:'.trans_mode($re['trans_mode']);
                $result['two'] = $part2;
                //3
                $part3 = [];
                $part3[] = '盈利能力';
                $part3[] = '去年净利润:'.$re['ly_net_profit'].'万元';
                if ($re['ty_net_profit']) {
                   $part3[] = '今年净利润:'.$re['ty_net_profit'].'万元'; 
                   $part3[] = '市盈率:'.$re['pe_ratio'];
                }
              
                $result['three'] = $part3;
                //4
                $part4 = [];
                $part4[] = '资产说明';
                $part4[] = $re['pro_highlight'];
                $result['four'] = $part4;
            }elseif ($dict['value'] == 'Pre-IPO') {
                //2
                $part2 = [];
                $part2[] = '融资需求';
                $part2[] = '融资规模:'.$re['financing_scale'].'万元';
                $part2[] = '融资期限:'.getFinancingPeriod($re['period']);
                $part2[] = '所属层级:'.level($re['level']);
                $part2[] = '交易方式:'.trans_mode($re['trans_mode']);
                $result['two'] = $part2;
                //3
                $part3 = [];
                $part3[] = '盈利能力';
                $part3[] = '去年净利润:'.$re['ly_net_profit'].'万元';
                if ($re['ty_net_profit']) {
                   $part3[] = '今年净利润:'.$re['ty_net_profit'].'万元'; 
                   $part3[] = '市盈率:'.$re['pe_ratio'];
                }
              
                $result['three'] = $part3;
                //4
                $part4 = [];
                $part4[] = '项目说明';
                $part4[] = $re['pro_highlight'];
                $result['four'] = $part4;
                //5
                $part5 = [];
                if ($re['financing_purpose']) {
                   $part5[] = '融资说明';
                   $part5[] = $re['financing_purpose'];
                   
                }
                $result['five'] = $part5;
            }*/
            
            elseif ($dict['value'] == '壳交易') {
                //2
                $part2 = [];
                $part2[] = '融资需求';
                $part2[] = '总股本:'.$re['general_capital'].'万股';
                $part2[] = '可出售股数:'.$re['trans_stock_num'].'万股';
                $part2[] = '出售价格:'.$re['price'].'万元';
                $part2[] = '所属层级:'.level($re['level']);
                $part2[] = '交易方式:'.trans_mode($re['trans_mode']);               
                $result['two'] = $part2;
                //3
                $part3 = [];
                $part3[] = '盈利能力';
                $part3[] = '去年净利润:'.$re['ly_net_profit'].'万元';
                if ($re['ty_net_profit']) {
                   $part3[] = '今年净利润:'.$re['ty_net_profit'].'万元'; 
                   $part3[] = '市盈率:'.$re['pe_ratio'];
                }
                $result['three'] = $part3;
                //4
                $part4 = [];
                $part4[] = '项目说明';
                $part4[] = $re['pro_highlight'];
                $result['four'] = $part4;

            }
            /*elseif ($dict['value'] == '并购') {
                //2
                $part2 = [];
                $part2[] = '融资需求';
                $part2[] = '拟出让股份:'.$re['sell_stock_min'].'-'.$re['sell_stock_min'].'(%)';
                $part2[] = '所属层级:'.level($re['level']);
                $part2[] = '交易方式:'.trans_mode($re['trans_mode']);
                $part2[] = '资金方偏好:'.investor_preference($re['investor_preference']);                              
                $result['two'] = $part2;
                //3
                $part3 = [];
                $part3[] = '盈利能力';
                $part3[] = '去年净利润:'.$re['ly_net_profit'].'万元';
                if ($re['ty_net_profit']) {
                   $part3[] = '今年净利润:'.$re['ty_net_profit'].'万元'; 
                  
                }
                $result['three'] = $part3;
                //4
                $part4 = [];
                $part4[] = '项目说明';
                $part4[] = $re['pro_highlight'];
                $result['four'] = $part4;
            }*/
            elseif ($dict['value'] == '其他') {
                 //2
                $part2 = [];
                $part2[] = '融资需求';
                $part2[] = '融资规模:'.$re['financing_scale'].'万元';
                $part2[] = '融资期限:'.$re['period'];
                $part2[] = '所属层级:'.level($re['level']);
                $part2[] = '交易方式:'.trans_mode($re['trans_mode']);                              
                $result['two'] = $part2;
                //3
                $part3 = [];
                $part3[] = '盈利能力';
                $part3[] = '去年净利润:'.$re['ly_net_profit'].'万元';
                if ($re['ty_net_profit']) {
                   $part3[] = '今年净利润:'.$re['ty_net_profit'].'万元'; 
                   $part3[] = '市盈率:'.$re['pe_ratio'];
                }
                $result['three'] = $part3;
                //4
                $part4 = [];
                $part4[] = '项目说明';
                $part4[] = $re['pro_highlight'];
                $result['four'] = $part4;
                //5
                $part5 = [];
                if ($re['financing_purpose']) {
                   
                   $part5[] = '融资说明';
                   $part5[] = $re['financing_purpose'];
                   
                }
                $result['five'] = $part5;
            }else{
                $this->code = 216;
                $this->msg = '标签不存在';
                return $this->result('',$this->code,$this->msg); 
            }    
        
            break;
          case '股权':
                //1
                $part = [];
                $name = '项目名称:'.$re['name'];//项目名称
                if ($re['is_public'] == 1) {
                    $name.='('.$re['stock_code'].')';//项目名称+股票代码
                }
                $part[] = $name;
                $part[] = $top_dict['value'].'-'.$dict['value'];//项目标签
                $part[] = $province['value'];//所在省份
                $part[] = $hangye;//所属行业
                $part[] = '业务简介:'.$re['business_des'];
                $result['one'] = $part;
                //分享链接的内容
                $result['share']['title'] = $re['name'];
                $result['share']['desc']  = $userInfo['realName']."向你推荐";
                
                
                /*if ($dict['value'] == '科创板') {
                    //2
                    $part2 = [];
                    $part2[] = '融资需求';
                    $part2[] = '融资规模:'.$re['financing_scale'].'万元';
                    $part2[] = '拟出让股份:'.$re['sell_stock_min'].'-'.$re['sell_stock_min'].'(%)';
                    $part2[] = '资金方偏好:'.investor_preference($re['investor_preference']);
                    $result['two'] = $part2;
                    //3
                    $part3 = [];
                    $part3[] = '盈利能力';
                    $part3[] = '去年净利润:'.$re['ly_net_profit'].'万元';
                    if ($re['ty_net_profit']) {
                        $part3[] = '今年净利润:'.$re['ty_net_profit'].'万元';
                    }
                    $result['three'] = $part3;
                    //4
                    $part4 = [];
                    $part4[] = '项目说明';
                    $part4[] = $re['pro_highlight'];
                    $result['four'] = $part4;
                    
                }*/
                if ($dict['value'] == '老股转让') {
                    //2
                    $part2 = [];
                    $part2[] = '融资需求';
                    $part2[] = '转让价格:'.$re['price'].'元/股';
                    $part2[] = '可转让股数:'.$re['trans_stock_num'].'万股';
                    if ($re['price']) {
                        $part2['price'] = '转让价格:'.$re['price'].'元/股';
                    }
                    $part2[] = '本轮估值:'.$re['valuation'].'万元';
                    $part2[] = '资金方偏好:'.investor_preference($re['investor_preference']);                          
                    $result['two'] = $part2;
                    //3
                    $part3 = [];
                    $part3[] = '盈利能力';
                    $part3[] = '去年净利润:'.$re['ly_net_profit'].'万元';
                    if ($re['ty_net_profit']) {
                       $part3[] = '今年净利润:'.$re['ty_net_profit'].'万元'; 
                    }
                    $result['three'] = $part3;
                    
                    //4
                    $part4 = [];
                    $part4[] = '项目说明';
                    $part4[] = $re['pro_highlight'];
                    $result['four'] = $part4;
                    

              }elseif ($dict['value'] == 'VC') {
                    //2
                    $part2 = [];
                    $part2[] = '融资需求';
                    $part2[] = '融资规模:'.$re['financing_scale'].'万元';
                    $part2[] = '融资期限:'.getFinancingPeriod($re['period']);
                    $part2[] = '资金方偏好:'.investor_preference($re['investor_preference']);                          
                    $result['two'] = $part2;
                    //3
                    $part3 = [];
                    $part3[] = '盈利能力';
                    $part3[] = '去年净利润:'.$re['ly_net_profit'].'万元';
                    if ($re['ty_net_profit']) {
                       $part3[] = '今年净利润:'.$re['ty_net_profit'].'万元';

                    }
                    if ($re['pe_ratio']) {
                       $part3[] = '市盈率:'.$re['pe_ratio'];
                    }
               
                    $result['three'] = $part3;
                    //4
                    $part4 = [];
                    $part4[] = '投资亮点';
                    $part4[] = $re['pro_highlight'];
                    $result['four'] = $part4;
                    //5
                    $part5 = [];
                    if ($re['financing_purpose']) {
                       
                       $part5[] = '融资说明';
                       $part5[] = $re['financing_purpose'];
                       
                    }
                    $result['five'] = $part5;  
              }elseif ($dict['value'] == 'PE') {
                    //2
                    $part2 = [];
                    $part2[] = '融资需求';
                    $part2[] = '融资规模(约):'.$re['financing_scale'].'万元';
                    $part2[] = '融资期限:'.getFinancingPeriod($re['period']);
                    $part2[] = '投资者偏好:'.investor_preference($re['investor_preference']);
                    $result['two'] = $part2;
                    //3
                    $part3 = [];
                    $part3[] = '盈利能力';
                    $part3[] = '去年净利润:'.$re['ly_net_profit'].'万元';
                    if ($re['ty_net_profit']) {
                       $part3[] = '今年净利润:'.$re['ty_net_profit'].'万元';

                    }
                    if ($re['pe_ratio']) {
                       $part3[] = '市盈率:'.$re['pe_ratio'];
                    }
               
                    $result['three'] = $part3;
                    //4
                    $part4 = [];
                    $part4[] = '投资亮点';
                    $part4[] = $re['pro_highlight'];
                    $result['four'] = $part4;
                    //5
                    $part5 = [];
                    if ($re['financing_purpose']) {   
                       $part5[] = '融资用途';
                       $part5[] = $re['financing_purpose'];
                       
                    }
                    $result['five'] = $part5;
              }elseif ($dict['value'] == 'Pre-IPO') {
                    //2
                    $part2 = [];
                    $part2[] = '融资需求';
                    $part2[] = '融资规模:'.$re['financing_scale'].'万元';
                    $part2[] = '融资期限:'.getFinancingPeriod($re['period']);
                    $part2[] = '资金方偏好:'.investor_preference($re['investor_preference']);                          
                    $result['two'] = $part2;
                    //3
                    $part3 = [];
                    $part3[] = '盈利能力';
                    $part3[] = '去年净利润:'.$re['ly_net_profit'].'万元';
                    if ($re['ty_net_profit']) {
                       $part3[] = '今年净利润:'.$re['ty_net_profit'].'万元';

                    }
                    if ($re['pe_ratio']) {
                       $part3[] = '市盈率:'.$re['pe_ratio'];
                    }
               
                    $result['three'] = $part3;
                    //4
                    $part4 = [];
                    $part4[] = '投资亮点';
                    $part4[] = $re['pro_highlight'];
                    $result['four'] = $part4;
                    //5
                    $part5 = [];
                    if ($re['financing_purpose']) {
                       
                       $part5[] = '融资说明';
                       $part5[] = $re['financing_purpose'];
                       
                    }
                    $result['five'] = $part5;
              }elseif ($dict['value'] == '并购') {
                    //2
                    $part2 = [];
                    $part2[] = '融资需求';
                    $part2[] = '交易规模（约）'.$re['financing_scale'].'万元';
                    $part2[] = '资金方偏好:'.investor_preference($re['investor_preference']);                          
                    $result['two'] = $part2;
                    //3
                    $part3 = [];
                    $part3[] = '盈利能力';
                    $part3[] = '去年净利润:'.$re['ly_net_profit'].'万元';
                    if ($re['ty_net_profit']) {
                       $part3[] = '今年净利润:'.$re['ty_net_profit'].'万元';

                    }
               
                    $result['three'] = $part3;
                    //4
                    $part4 = [];
                    $part4[] = '投资亮点';
                    $part4[] = $re['pro_highlight'];
                    $result['four'] = $part4;

              }elseif ($dict['value'] == '其他') {
                    //2
                    $part2 = [];
                    $part2[] = '融资需求';
                    $part2[] = '融资规模:'.$re['financing_scale'].'万元';
                    $part2[] = '拟出让股份:'.$re['sell_stock_min'].'-'.$re['sell_stock_min'].'(%)';
                    $part2[] = '资金方偏好:'.investor_preference($re['investor_preference']);                          
                    $result['two'] = $part2;
                    //3
                    $part3 = [];
                    $part3[] = '盈利能力';
                    $part3[] = '去年净利润:'.$re['ly_net_profit'].'万元';
                    if ($re['ty_net_profit']) {
                       $part3[] = '今年净利润:'.$re['ty_net_profit'].'万元'; 
                    }
                    $result['three'] = $part3;
                    //4
                    $part4 = [];
                    $part4[] = '项目说明';
                    $part4[] = $re['pro_highlight'];
                    $result['four'] = $part4;

              }else{
                  $this->code = 216;
                  $this->msg = '标签不存在';
                  return $this->result('',$this->code,$this->msg); 
              }
            break;
          case '债权':
                //1
                $part = [];
                $name = '项目名称:'.$re['name'];//项目名称
                if ($re['is_public'] == 1) {
                    $name.='('.$re['stock_code'].')';//项目名称+股票代码
                }
                $part[] = $name;
                $part[] = $top_dict['value'].'-'.$dict['value'];//项目标签
                $part[] = $province['value'];//所在省份
                $part[] = $hangye;//所属行业
                $part[] = '业务简介:'.$re['business_des'];
                $result['one'] = $part;
                //分享链接的内容
            
            $result['share']['title'] = $re['name'];
            $result['share']['desc']  = $userInfo['realName']."向你推荐";
            if ($dict['value'] == '信贷') {
                 //2
                $part2 = [];
                $part2[] = '融资需求';
                $part2[] = '融资规模:'.$re['financing_scale'].'万元';
                $part2[] = '融资期限:'.getFinancingPeriod($re['period']);
                if ($re['rate_cap']) {
                    $part2[] = '融资利率上限:'.$re['rate_cap'].'%';
                }
                if ($re['inc_credit_way']) {
                    $part2[] = '增信方式:'.inc_credit_way($re['inc_credit_way']);
                }                      
                $result['two'] = $part2;

                //3
                $part3 = [];
                if ($re['financing_purpose']) {
                    
                    $part3[] = '融资说明';
                    $part3[] = $re['financing_purpose'];
                    
                }
                $result['three'] = $part3;
                //4
                $part4 = [];
                $part4[] = '还款来源';
                $part4[] = $re['pay_source'];
                $result['four'] = $part4;
                
            }
            /*elseif ($dict['value'] == '信托') {
                 //2
                $part2 = [];
                $part2[] = '融资需求';
                $part2[] = '融资规模:'.$re['financing_scale'].'万元';
                $part2[] = '融资期限:'.getFinancingPeriod($re['period']);
                if ($re['rate_cap']) {
                    $part2[] = '融资利率上限:'.$re['rate_cap'].'%';
                }
                if ($re['inc_credit_way']) {
                    $part2[] = '增信方式:'.inc_credit_way($re['inc_credit_way']);
                }                      
                $result['two'] = $part2;
                //3
                $part3 = [];
                if ($re['financing_purpose']) {
                    
                    $part3[] = '融资说明';
                    $part3[] = $re['financing_purpose'];
                    
                }
                $result['three'] = $part3;
                //4
                $part4 = [];
                $part4[] = '还款来源';
                $part4[] = $re['pay_source'];
                $result['four'] = $part4;
            }elseif ($dict['value'] == '资管') {
                 //2
                $part2 = [];
                $part2[] = '融资需求';
                $part2[] = '融资规模:'.$re['financing_scale'].'万元';
                $part2[] = '融资期限:'.getFinancingPeriod($re['period']);
                if ($re['rate_cap']) {
                    $part2[] = '融资利率上限:'.$re['rate_cap'].'%';
                }
                if ($re['inc_credit_way']) {
                    $part2[] = '增信方式:'.inc_credit_way($re['inc_credit_way']);
                }                      
                $result['two'] = $part2;
                //3
                $part3 = [];
                if ($re['financing_purpose']) {
                    
                    $part3[] = '融资说明';
                    $part3[] = $re['financing_purpose'];
                    
                }
                $result['three'] = $part3;
                //4
                $part4 = [];
                $part4[] = '还款来源';
                $part4[] = $re['pay_source'];
                $result['four'] = $part4;
            }elseif ($dict['value'] == '可转债/可交债') {
                 //2
                $part2 = [];
                $part2[] = '融资需求';
                $part2[] = '融资规模:'.$re['financing_scale'].'万元';
                $part2[] = '融资期限:'.getFinancingPeriod($re['period']);
                if ($re['rate_cap']) {
                    $part2[] = '融资利率上限:'.$re['rate_cap'].'%';
                }
                if ($re['inc_credit_way']) {
                    $part2[] = '增信方式:'.inc_credit_way($re['inc_credit_way']);
                }                      
                $result['two'] = $part2;
                //3
                $part3 = [];
                if ($re['financing_purpose']) {
                    
                    $part3[] = '融资说明';
                    $part3[] = $re['financing_purpose'];
                    
                }
                $result['three'] = $part3;
                //4
                $part4 = [];
                $part4[] = '还款来源';
                $part4[] = $re['pay_source'];
                $result['four'] = $part4;
            }*/
            elseif ($dict['value'] == '融资租赁') {
                 //2
                $part2 = [];
                $part2[] = '融资需求';
                $part2[] = '融资规模:'.$re['financing_scale'].'万元';
                $part2[] = '融资期限:'.getFinancingPeriod($re['period']);
                if ($re['rate_cap']) {
                    $part2[] = '融资利率上限:'.$re['rate_cap'].'%';
                }
                if ($re['inc_credit_way']) {
                    $part2[] = '增信方式:'.inc_credit_way($re['inc_credit_way']);
                }                      
                $result['two'] = $part2;
                //3
                $part3 = [];
                $part3[] = '盈利能力';
                $part3[] = '营业收入:'.$re['business_income'].'万元';
                if ($re['debt_ratio']) {
                    $part3[] = '负债率:'.$re['debt_ratio'].'%';
                }
                //4
                $part4 = [];
                if ($re['financing_purpose']) {
                    
                    $part4[] = '融资说明';
                    $part4[] = $re['financing_purpose'];
                    
                }
                $result['four'] = $part4;
                //5
                $part5 = [];
                $part5[] = '还款来源';
                $part5[] = $re['pay_source'];
                $result['five'] = $part5;
            }
            /*elseif ($dict['value'] == '股权质押') {
                 //2
                $part2 = [];
                $part2[] = '融资需求';
                $part2[] = '融资规模:'.$re['financing_scale'].'万元';
                $part2[] = '融资期限:'.getFinancingPeriod($re['period']);
                $part2[] = '是否为大股东:'.YesOrNo($re['is_stockholder']);
                $result['two'] = $part2;
                //3
                $part3 = [];
                $part3[] = '盈利能力';
                $part3[] = '去年净利润:'.$re['ly_net_profit'].'万元';
                if ($re['ty_net_profit']) {
                    $part3[] = '今年净利润:'.$re['ty_net_profit'].'万元';
                }
                $part3[] = '质押率:'.$re['ltv_ratio'].'%';
                if ($re['pe_ratio']) {
                    $part3[] = '市盈率:'.$re['pe_ratio'].'%';
                }

                //4
                $part4 = [];
                if ($re['financing_purpose']) {
                    
                    $part4[] = '融资说明';
                    $part4[] = $re['financing_purpose'];
                    
                }
                $result['four'] = $part4;
                //5
                $part5 = [];
                $part5[] = '还款来源';
                $part5[] = $re['pay_source'];
                $result['five'] = $part5;
            }*/
            elseif ($dict['value'] == '资产证券化') {
                 //2
                $part2 = [];
                $part2[] = '融资需求';
                $part2[] = '融资规模:'.$re['financing_scale'].'万元';
                $part2[] = '融资期限:'.getFinancingPeriod($re['period']);
                if ($re['rate_cap']) {
                    $part2[] = '融资利率上限:'.$re['rate_cap'].'%';
                }
                if ($re['inc_credit_way']) {
                    $part2[] = '增信方式:'.inc_credit_way($re['inc_credit_way']);
                }                      
                $result['two'] = $part2;
                //3
                $part3 = [];
                if ($re['financing_purpose']) {
                    
                    $part3[] = '融资说明';
                    $part3[] = $re['financing_purpose'];
                    
                }
                $result['three'] = $part3;
                //4
                $part4 = [];
                $part4[] = '还款来源';
                $part4[] = $re['pay_source'];
                $result['four'] = $part4;
            }elseif ($dict['value'] == '商业保理') {
                 //2
                $part2 = [];
                $part2[] = '融资需求';
                $part2[] = '融资规模:'.$re['financing_scale'].'万元';
                $part2[] = '融资期限:'.getFinancingPeriod($re['period']);
                if ($re['rate_cap']) {
                    $part2[] = '融资利率上限:'.$re['rate_cap'].'%';
                }
                if ($re['inc_credit_way']) {
                    $part2[] = '增信方式:'.inc_credit_way($re['inc_credit_way']);
                }                      
                $result['two'] = $part2;
                //3
                $part3 = [];
                $part3[] = '盈利能力';
                $part3[] = '营业收入:'.$re['business_income'].'万元';
                if ($re['debt_ratio']) {
                    $part3[] = '负债率:'.$re['debt_ratio'].'%';
                }
                //4
                $part4 = [];
                if ($re['financing_purpose']) {
                    
                    $part4[] = '融资说明';
                    $part4[] = $re['financing_purpose'];
                    
                }
                $result['four'] = $part4;
                //5
                $part5 = [];
                $part5[] = '还款来源';
                $part5[] = $re['pay_source'];
                $result['five'] = $part5;
            }elseif ($dict['value'] == '票据业务') {
                //2
                $part2 = [];
                $part2[] = '票据详情';
                $part2[] = '承兑人:'.acceptor($re['acceptor']);
                $part2[] = '票据类型:'.bill_type($re['bill_type']);
                $part2[] = '票面金额:'.$re['coupon_finance'].'万元';
                $part2[] = '剩余天数:'.$re['days_remain'].'天';
                $part2[] = '最低售票价格:'.$re['price'].'元';
                $result['two'] = $part2;
               
            }elseif($dict['value'] == '其他'){
                 //2
                $part2 = [];
                $part2[] = '融资需求';
                $part2[] = '融资规模:'.$re['financing_scale'].'万元';
                $part2[] = '融资期限:'.getFinancingPeriod($re['period']);
                if ($re['rate_cap']) {
                    $part2[] = '融资利率上限:'.$re['rate_cap'].'%';
                }
                if ($re['inc_credit_way']) {
                    $part2[] = '增信方式:'.inc_credit_way($re['inc_credit_way']);
                }                      
                $result['two'] = $part2;
                //3
                $part3 = [];
                if ($re['financing_purpose']) {
                    
                    $part3[] = '融资说明';
                    $part3[] = $re['financing_purpose'];
                    
                }
                $result['three'] = $part3;
                //4
                $part4 = [];
                $part4[] = '还款来源';
                $part4[] = $re['pay_source'];
                $result['four'] = $part4;
            }else{
                $this->code = 216;
                $this->msg = '标签不存在';
                return $this->result('',$this->code,$this->msg); 
            }

          break;
          case '创业孵化':
               //if ($dict['value'] == '影视文娱' || $dict['value'] == '资产交易' || $dict['value'] == '合伙创业' || $dict['value'] == '公益爱心' || $dict['value'] == '其他') {
               if ($dict['value'] == '资产交易' ||  $dict['value'] == '项目落户' || $dict['value'] == '其他') {
                    //1
                    $part = [];
                    $name = '项目名称:'.$re['name'];//项目名称
                    if ($re['is_public'] == 1) {
                        $name.='('.$re['stock_code'].')';//项目名称+股票代码
                    }
                    $part[] = $name;
                    $part[] = $top_dict['value'].'-'.$dict['value'];//项目标签
                    $part[] = $province['value'];//所在省份
                    $part[] = $hangye;//所属行业
                    $part[] = '业务简介:'.$re['business_des'];
                    $result['one'] = $part;
                    //分享链接的内容
                    $result['share']['title'] = $re['name'];
                    $result['share']['desc']  = $userInfo['realName']."向你推荐";
                    //2
                    $part2 = [];
                    $part2[] = '融资需求';
                    $part2[] = '所需资金:'.$re['financing_scale'].'万元';
                    if ($re['remain_period']) {
                        $part2[] = '剩余期限:'.$re['remain_period'].'月';
                    }
                    if ($re['expected_return']) {
                        $part2[] = '预计收益:'.$re['expected_return'].'万元';
                    }
                    $result['two'] = $part2;
                    //3
                    $part3 = [];
                    $part3[] = '项目说明';
                    $part3[] = $re['pro_highlight'];
                    $result['three'] = $part3;

               } else {
                    $this->code = 216;
                    $this->msg = '标签不存在';
                    return $this->result('',$this->code,$this->msg); 
               }
               
            break;
          case '上市公司':
             //1
            $part = [];
            $name = '项目名称:'.$re['name'];//项目名称
            if ($re['is_public'] == 1) {
                $name.='('.$re['stock_code'].')';//项目名称+股票代码
            }
            $part[] = $name;
            $part[] = $top_dict['value'].'-'.$dict['value'];//项目标签
            $part[] = $province['value'];//所在省份
            $part[] = $hangye;//所属行业
            $part[] = '上市公司类型:'.lc_type($re['lc_type']);
            $part[] = '业务简介:'.$re['business_des'];
            $result['one'] = $part;
            //分享链接的内容
            $result['share']['title'] = $re['name'];
            $result['share']['desc']  = $userInfo['realName']."向你推荐";
            if ($dict['value'] == '定向增发') {
                //2
                $part2 = [];
                $part2[] = '融资需求';
                $part2[] = '融资规模:'.$re['financing_scale'].'万元';
                $part2[] = '融资期限:'.getFinancingPeriod($re['period']);
                $part2[] = '定增价格:'.$re['price'].'元/股';
                $result['two'] = $part2;
                //3
                $part3 = [];
                $part3[] = '盈利能力';
                $part3[] = '去年净利润:'.$re['ly_net_profit'].'万元';
                if ($re['ty_net_profit']) {
                    $part3[] = '今年净利润:'.$re['ty_net_profit'].'万元';
                }
                if ($re['pe_ratio']) {
                    $part3[] = '市盈率:'.$re['pe_ratio'];
                }
                $result['three'] = $part3;
                //4
                $part4 = [];
                $part4[] = '项目说明';
                $part4[] = $re['pro_highlight'];
                $result['four'] = $part4;
                //5
                $part5 = [];
                if ($re['financing_purpose']) {
                    
                    $part5[] = '融资用途';
                    $part5[] = $re['financing_purpose'];
                    
                }
                $result['five'] = $part5;
            }elseif ($dict['value'] == '大宗交易') {
                //2
                $part2 = [];
                $part2[] = '融资需求';
                $part2[] = '交易规模:'.$re['financing_scale'].'万元';
                if ($re['price']) {
                   $part2[] = '交易价格:'.$re['price'].'元/股';
                }
                
                $result['two'] = $part2;
                //3
                $part3 = [];
                $part3[] = '盈利能力';
                $part3[] = '去年净利润:'.$re['ly_net_profit'].'万元';
                if ($re['ty_net_profit']) {
                    $part3[] = '今年净利润:'.$re['ty_net_profit'].'万元';
                }
                $part3[] = '折扣率:'.$re['discount_rate'].'%';
                if ($re['pe_ratio']) {
                    $part3[] = '市盈率:'.$re['pe_ratio'];
                }
                $result['three'] = $part3;
                //4
                $part4 = [];
                $part4[] = '项目说明';
                $part4[] = $re['pro_highlight'];
                $result['four'] = $part4;
                
                
            }elseif ($dict['value'] == '股权质押') {
                //2
                $part2 = [];
                $part2[] = '融资需求';
                $part2[] = '交易规模:'.$re['financing_scale'].'万元';
                if ($re['price']) {
                   $part2[] = '交易价格:'.$re['price'].'元/股';
                }
                
                $result['two'] = $part2;
                //3
                $part3 = [];
                $part3[] = '盈利能力';
                $part3[] = '去年净利润:'.$re['ly_net_profit'].'万元';
                if ($re['ty_net_profit']) {
                    $part3[] = '今年净利润:'.$re['ty_net_profit'].'万元';
                }
                $part3[] = '折扣率:'.$re['discount_rate'].'%';
                if ($re['pe_ratio']) {
                    $part3[] = '市盈率:'.$re['pe_ratio'];
                }
                $result['three'] = $part3;
                //4
                $part4 = [];
                $part4[] = '项目说明';
                $part4[] = $re['pro_highlight'];
                $result['four'] = $part4;

            }
            /*
            elseif ($dict['value'] == '壳交易') {
                //2
                $part2 = [];
                $part2[] = '融资需求';
                $part2[] = '交易规模:'.$re['financing_scale'].'万元';
                $part2[] = '拟出让股份:'.$re['sell_stock_min'].'-'.$re['sell_stock_max'].'(%)';
                $result['two'] = $part2;
                //3
                $part3 = [];
                $part3[] = '盈利能力';
                $part3[] = '去年净利润:'.$re['ly_net_profit'].'万元';
                if ($re['ty_net_profit']) {
                    $part3[] = '今年净利润:'.$re['ty_net_profit'].'万元';
                }

                if ($re['pe_ratio']) {
                    $part3[] = '市盈率:'.$re['pe_ratio'];
                }
                $result['three'] = $part3;
                //4
                $part4 = [];
                $part4[] = '项目说明';
                $part4[] = $re['pro_highlight'];
                $result['four'] = $part4;
            }
            */
            elseif ($dict['value'] == '市值管理') {
                 //2
                $part2 = [];
                $part2[] = '融资需求';
                $part2[] = '交易规模:'.$re['financing_scale'].'万元';
                $part2[] = '管理期限:'.period($re['period']);
                $result['two'] = $part2;
                //3
                $part3 = [];
                $part3[] = '盈利能力';
                $part3[] = '去年净利润:'.$re['ly_net_profit'].'万元';
                if ($re['ty_net_profit']) {
                    $part3[] = '今年净利润:'.$re['ty_net_profit'].'万元';
                }

                if ($re['pe_ratio']) {
                    $part3[] = '市盈率:'.$re['pe_ratio'];
                }
                $result['three'] = $part3;
                //4
                $part4 = [];
                $part4[] = '项目说明';
                $part4[] = $re['pro_highlight'];
                $result['four'] = $part4;
            }elseif ($dict['value'] == '并购重组') {
                 //2
                $part2 = [];
                $part2[] = '融资需求';
                $part2[] = '拟出让股份:'.$re['sell_stock_min'].'-'.$re['sell_stock_max'].'(%)';
                $part2[] = '资金方偏好:'.investor_preference($re['investor_preference']);
                $result['two'] = $part2;
                //3
                $part3 = [];
                $part3[] = '盈利能力';
                $part3[] = '去年净利润:'.$re['ly_net_profit'].'万元';
                if ($re['ty_net_profit']) {
                    $part3[] = '今年净利润:'.$re['ty_net_profit'].'万元';
                }

                $result['three'] = $part3;
                //4
                $part4 = [];
                $part4[] = '项目说明';
                $part4[] = $re['pro_highlight'];
                $result['four'] = $part4;
            }elseif ($dict['value'] == '其他') {
                 //2
                $part2 = [];
                $part2[] = '融资需求';
                $part2[] = '融资规模:'.$re['financing_scale'].'万元';
                $part2[] = '融资期限:'.getFinancingPeriod($re['period']);

                $result['two'] = $part2;
                //3
                $part3 = [];
                $part3[] = '盈利能力';
                $part3[] = '去年净利润:'.$re['ly_net_profit'].'万元';
                if ($re['ty_net_profit']) {
                    $part3[] = '今年净利润:'.$re['ty_net_profit'].'万元';
                }
                if ($re['pe_ratio']) {
                    $part3[] = '市盈率:'.$re['pe_ratio'];
                }
                $result['three'] = $part3;
                //4
                $part4 = [];
                $part4[] = '项目说明';
                $part4[] = $re['pro_highlight'];
                $result['four'] = $part4;
                
            }else{
                 $this->code = 216;
                 $this->msg = '标签不存在';
                 return $this->result('',$this->code,$this->msg); 
            }
            break;
            case '金融产品':
                 //if ($dict['value'] == '私募基金' || $dict['value'] == '信托产品' || $dict['value'] == '券商集合理财' || $dict['value'] == '期货理财' || $dict['value'] == '基金公司及其子公司专户' || $dict['value'] == '保险产品' || $dict['value'] == '其他') {
                if($dict['value'] == 'S基金'){
                    //1
                    $part = [];
                    $name = '产品名称:'.$re['name'];
                    $part[] = $name;//产品名称
                    $part[] = $top_dict['value'].'-'.$dict['value'];//项目标签
                    $part[] = $province['value'];//所在省份
                    $part[] = $hangye;//所属行业
                    $part[] = '产品简介:'.$re['business_des'];
                    $result['one'] = $part;
                    //分享链接的内容
                    $result['share']['title'] = $re['name'];
                    $result['share']['desc']  = $userInfo['realName']."向你推荐";
                    //2
                    $part2 = [];
                    $part2[] = '产品介绍';
                    $part2[] = '发行机构:'.$re['product_issuer'];
                    $part2[] = '是否备案:'.YesOrNo($re['is_record']);
                    $part2[] = '认购金额:'.$re['sub_amount'].'万元';
                    $part2[] = '产品期限:'.period($re['period']);
                    $part2[] = '产品规模:'.$re['financing_scale'].'万元';
                    $part2[] = '产品状态:'.product_status($re['product_status']);
                    $result['two'] = $part2;
                    //3
                    $part3 = [];
                    $part3[] = '产品说明';
                    $part3[] = $re['pro_highlight'];
                    $result['three'] = $part3;
                    
                    //4
                    $part4 = [];
                    $part4[] = '融资说明';
                    if ($re['price']) {
                        $part4['price'] = '转让价格:'.$re['price'].'元/股';
                    }
                    $part4[] = '本轮估值:'.$re['valuation'].'万元';
                    
                
                }elseif ($dict['value'] == '私募基金'  || $dict['value'] == '其他') {
                         //1
                        $part = [];
                        $name = '产品名称:'.$re['name'];
                        $part[] = $name;//产品名称
                        $part[] = $top_dict['value'].'-'.$dict['value'];//项目标签
                        $part[] = $province['value'];//所在省份
                        $part[] = $hangye;//所属行业
                        $part[] = '产品简介:'.$re['business_des'];
                        $result['one'] = $part;
                        //分享链接的内容
                        $result['share']['title'] = $re['name'];
                        $result['share']['desc']  = $userInfo['realName']."向你推荐";
                        //2
                        $part2 = [];
                        $part2[] = '产品介绍';
                        $part2[] = '发行机构:'.$re['product_issuer'];
                        $part2[] = '是否备案:'.YesOrNo($re['is_record']);
                        $part2[] = '认购金额:'.$re['sub_amount'].'万元';
                        $part2[] = '产品期限:'.period($re['period']);
                        $part2[] = '产品规模:'.$re['financing_scale'].'万元';
                        $part2[] = '产品状态:'.product_status($re['product_status']);
                        $result['two'] = $part2;
                        //3
                        $part3 = [];
                        $part3[] = '产品说明';
                        $part3[] = $re['pro_highlight'];
                        $result['three'] = $part3;

                 } else {
                      $this->code = 216;
                     $this->msg = '标签不存在';
                     return $this->result('',$this->code,$this->msg);
                 }
                 
            break;
            case '配资业务':
                 if ($dict['value'] == '股票配资' || $dict['value'] == '期货配资' || $dict['value'] == '其他' ) {
                        //1
                        $part = [];
                        $name = '项目名称:'.$re['name'];
                        $part[] = $name;//产品名称
                        $part[] = $top_dict['value'].'-'.$dict['value'];//项目标签
                        $part[] = $province['value'];//所在省份
                        $part[] = '业务简介:'.$re['business_des'];
                        $result['one'] = $part;
                        //分享链接的内容
                        $result['share']['title'] = $re['name'];
                        $result['share']['desc']  = $userInfo['realName']."向你推荐";
                        //2
                        $part2 = [];
                        $part2[] = '融资需求';
                        $part2[] = '投入本金:'.$re['financing_scale'].'万元';
                        $part2[] = '配资比例:'.$re['allocation_ratio'].'倍';
                        $part2[] = '总操盘金额:'.$re['total_trade_num'].'万元';
                        $part2[] = '操盘周期:'.$re['trade_period'].'月';
                        $part2[] = '月利率上限:'.$re['mirc'];
                        $result['two'] = $part2;
                        //3
                        $part3 = [];
                        $part3[] = '产品说明';
                        $part3[] = $re['pro_highlight'];
                        $result['three'] = $part3;

                 } else {
                      $this->code = 216;
                     $this->msg = '标签不存在';
                     return $this->result('',$this->code,$this->msg);
                 }
            break;
          default:
                 $this->code = 216;
                 $this->msg = '标签不存在';
                 return $this->result('',$this->code,$this->msg);
            break;
        }
        //上传的图片
        $map['ml_id'] =  array('in',$re['img_id']);
        $ml = model('MaterialLibrary')->field('url')->where($map)->select()->toArray();
        $all_img  = [];
        if (!empty($ml)) {

            foreach ($ml as &$each_ml) {
                $each_ml['url'] = config('view_replace_str')['__DOMAIN__'].$each_ml['url'];
            }
            $all_img = array_column($ml,'url');
        }
        $result['img'] = $all_img;
        //业务截止日期
        $result['deadline'] = deadline($re['create_time'],$re['validity_period']);
        //查看人数
        $result['view_num'] = $re['view_num'].'人查看';
        //收藏状态
        $cpr = $data['collection_project_require'];
        if (!empty($cpr)) {
            $cpr_arr = explode(',', $cpr);
            if (in_array($re['pr_id'], $cpr_arr)) {
                $result['is_collect'] = '已收藏';
            } else {
                $result['is_collect'] = '收藏';
            }
            
        }else{
            $result['is_collect'] = '收藏';
        }
        return $this->result($result);
    }

    /**
     * 个人中心-业务列表
     * @param 发布业务的用户UID $uid
     * @param 登录查询业务的UID $login_uid
     * @param 显示页数  $page
     * @param 每页列表的条数   $length
     * @return unknown
     */
    public function getMyBusinessApi($uid=0,$login_uid=0,$page=0,$length=4)
    {
        if (empty($uid)) {
            $this->code = 208;
            $this->msg  = '缺少用户id参数';
            return $this->result('',$this->code,$this->msg);
        }
        //偏移量
        $offset = (intval($page)-1) * intval($length);
        $now    = time();
        if ($uid == $login_uid) {
            $or_where = "or.uid = $uid and or.is_show = 1 and or.status = 1 and ord.is_del = 2 and d.status = 1 and (dt.sign='service_type' or dt.sign='industry')";
            $pr_where = "pr.uid = $uid and pr.is_show = 1 and pr.status = 1";
        }else{
            $or_where = "or.uid = $uid and or.deadline >= $now and or.is_show = 1 and or.status = 1 and ord.is_del = 2 and d.status = 1 and (dt.sign='service_type' or dt.sign='industry')";
            $pr_where = "pr.uid = $uid and pr.is_show = 1 and pr.status = 1 and pr.deadline >= $now";
        }
        //用户发布资金,发布项目
        $or = model('OrganizeRequire');
        //用户发布资金
        $or_sql = $or->alias('or')
                     ->join('Member m','m.uid=or.uid')
                     ->join('OrganizeRequireDict ord','ord.or_id=or.or_id')
                     ->join('Dict d','ord.dict_id=d.id')
                     ->join('DictType dt','ord.dict_type_id=dt.dt_id')
                     //->where(['or.uid'=>$uid,'or.is_show'=>1,'or.status'=>1,'ord.is_del'=>2,'d.status'=>1])
                     ->where($or_where)
                     ->where("dt.sign='service_type' or dt.sign='industry'")
                     ->field([
                      'or.or_id'=>'id',
                      'm.userPhoto',
                      'm.realName',
                      'or.name'=>'name',
                      'group_concat(d.value separator ",")'=>'tag_name',
                      'or.business_des'=>'des',
                      'or.type',
                      'or.view_num',
                      'or.create_time',
                      'or.validity_period',
                      'or.deadline',
                     ])
                     ->group('or.or_id')
                     ->buildSql();
        //用户发布项目
        $this->alias('pr')
              ->join('Member m','pr.uid=m.uid')
              ->join('Dict d','pr.top_dict_id=d.id')
              ->join('Dict d2','pr.dict_id=d2.id')
              ->join('Dict d3','pr.top_industry_id=d3.id','left')
              ->field([
                'pr.pr_id'=>'id',
                'm.userPhoto',
                'm.realName',
                'pr.name',
                'concat_ws(",",d.value,d2.value,d3.value)'=>'tag_name',
                'pr.business_des'=>'des',
                'pr.type',
                'pr.view_num',
                'pr.create_time',
                'pr.validity_period',
                'pr.deadline',
               ])
              //->where(['pr.uid'=>$uid])
              //->where(['pr.is_show'=>1,'pr.status'=>1])
              ->where($pr_where)
              ->union($or_sql,true);
              
        //如果是当前登录用户的业务,则展示投递项目
        if ($uid == $login_uid) {
            $pc     = model('ProjectClue');
            //用户投递项目sql
            $pc_sql = $pc->alias('pc')
                         ->join('Member m','pc.create_uid=m.uid')
                         ->join('Dict d','d.id=pc.capital_plan')
                         ->where(['pc.create_uid'=>$uid,'d.status'=>1])
                         ->field([
                            'pc.clue_id'=>'id',
                            'm.userPhoto',
                            'm.realName',
                            'pc.pro_name'=>'name',
                            'd.value'=>'tag_name',
                            'pc.introduction'=>'des',
                            'pc.type',
                            'pc.view_num',
                            'pc.create_time',
                            'pc.validity_period',
                             '0 as deadline',
                         ])
                         ->buildSql();
            $this->union($pc_sql,true);
        }
        $pr_list = $this->select(false);
        
        $all_sql = "($pr_list)";
        $all_list = Db::table($all_sql.' as a')->field([
            'a.id',
            'a.userPhoto',
            'a.realName',
            'a.name',
            'a.tag_name',
            'a.des',
            'a.type',
            'a.view_num',
            'a.create_time',
            'a.validity_period',
            'a.deadline',
            ]) 
              ->order('a.create_time desc')
              ->limit($offset, $length)
              ->select();
        if (empty($all_list)) {
            $this->code = 201;
            $this->msg = '数据不存在';
            return $this->result('',$this->code,$this->msg);       
        }

        //判断状态:进行中,已结束
        foreach ($all_list as &$value) {
            $value['expire'] = prorStatus($value['create_time'],$value['validity_period']);
        }
        return $this->result($all_list);   
    }

    //个人中心-删除一条业务(发布项目)
    public function delMyProjectRequireApi($id=0,$uid=0)
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
            //删除一条项目
            $this->where(['pr_id'=>$id])->update(['is_show'=>3]);
            //每删除一条项目,member对应的业务数量-1
            model('Member')->where(['uid'=>$uid])->setDec('service_num');
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

    //用户查看发布项目,该项目查看人数+1
    public function editViewNumApi($id=0)
    {
        $re = $this->where(['pr_id'=>$id])->find()->toArray();
        if (empty($re)) {
            $this->code = 255;
            $this->msg = '项目不存在';
            return $this->result('',$this->code,$this->msg);
        }
        $result = $this->where(['pr_id'=>$id])->setInc('view_num');
        if ($result) {
            $this->code = 267;
            $this->msg = '更新查看人数成功';
            return $this->result('',$this->code,$this->msg);            
        }

        $this->code = 268;
        $this->msg = '更新查看人数失败';
        return $this->result('',$this->code,$this->msg);
    }
    //用户电话联系发布项目拥有者,该项目电话联系次数+1
    public function editTelNumApi($id=0)
    {
        $re = $this->where(['pr_id'=>$id])->find()->toArray();
        if (empty($re)) {
            $this->code = 255;
            $this->msg = '项目不存在';
            return $this->result('',$this->code,$this->msg);
        }
        $result = $this->where(['pr_id'=>$id])->setInc('tel_num');
        if ($result) {
            $this->code = 200;
            $this->msg = '更新电话联系次数成功';
            return $this->result('',$this->code,$this->msg);            
        }

        $this->code = 270;
        $this->msg = '更新电话联系次数失败';
        return $this->result('',$this->code,$this->msg);
    }

	/**
     * 市场精选项目列表（融资中可显示的）
     * @param array $data
     * @return 数组
     */
    public function getCareProjectAndProjectRequireMarketList($data = [])
    {
        
        //查询当前登录用户添加的偏好标签
        $dl = model('DictLabel');
        $dl = $dl->getDictLabelApi($data['uid']);
        //每页显示数
        $length = $data['length'];
        //计算偏移量
        $offset = $data['offset'];
        
        //用户未设置偏好标签时
        if (empty($dl)) {
            //平台精选项目sql
            $project_list = model('Project')->alias('p')
            ->join('Dict d','p.inc_top_sign=d.id')
            ->join('Dict d2','p.inc_sign=d2.id')
            ->join('Dict d3','p.inc_top_industry=d3.id')
            ->join('MaterialLibrary ml','p.top_img=ml.ml_id')
            ->field([
                'p.pro_id'=>'id',
                'ml.url',
                'p.pro_name'=>'name',
                'p.company_name'=>'top',
                'concat_ws(",",d.value,d2.value,d3.value)'=>'label',
                'p.introduction'=>'bottom',
                'p.view_num',
                'p.type',
            ])
            ->where(['p.status' => 2 , 'p.flag' => 1])
            ->order("p.list_order desc,p.view_num desc,p.pro_id desc")
            ->select()
            ->toArray();
            
            if (!empty($project_list)) {
                foreach ($project_list as &$v) {
                    $v['type_label']    = '精选项目';
                    $v['bottom']        = '简介:'.$v['bottom'];
                    $v['label']         = explode(',', $v['label']);
                    $v['url']           = config('view_replace_str')['__DOMAIN__'].$v['url'];
                }
            }
            $all_project   = array_merge($project_list);
            $final_project = array_slice($all_project,$offset,$length);
            return $this->result($final_project);
        }
        
        //用户已设置偏好标签时
        $dt = model('DictType');
        $service_type_all       = [];//当前用户选择的业务类型所有一二级标签
        $service_type_son_par   = [];//当前用户选中的业务类型的二级标签所属的一级标签
        $service_type_son       = [];//当前用户选择的业务类型所有的2级标签(用来匹配的)
        
        $industry_type_all      = [];//当前用户选择的行业的所有一二级标签
        $industry_type_son_par  = [];//当前用户选中行业的二级标签所属的一级标签
        $industry_type_son      = [];//当前用户选择的行业所有的2级标签(用来匹配的)
        
        $size                   = [];//当前用户选择的投融资规模(用来匹配的)
        $to_province            = [];//当前用户选择的所在省份(用来匹配的)
        foreach ($dl as $k => $v) {
            $sign = $dt->field('sign')->where(['dt_id'=>$v['dict_type_id']])->find();
            //业务类型下的标签
            if($sign['sign'] == 'service_type'){
                $service_type_all[]             = $v['dict_id'];
                if($v['dict_fid'] > 0){
                    $service_type_son[]         = $v['dict_id'];
                    $service_type_son_par[]     = $v['dict_fid'];
                }
            }
            //所属行业下的标签
            if($sign['sign'] == 'industry'){
                $industry_type_all[]            = $v['dict_id'];
                if($v['dict_fid'] > 0){
                    $industry_type_son[]        = $v['dict_id'];
                    $industry_type_son_par[]    = $v['dict_fid'];
                }
            }
            //投融资规模
            if($sign['sign'] == 'size'){
                $size[]                         = $v['dict_id'];
            }
            //所在省份
            if($sign['sign'] == 'to_province'){
                $to_province[]                  = $v['dict_id'];
            }
        }
        
        //得到当前用户选中二级标签所属的一级标签和未选中二级标签的一级标签(业务类型)
        $service_type_all_par   = array_diff($service_type_all, $service_type_son);
        //得到当前用户未选中二级标签的一级标签(用来匹配的条件)(业务类型)
        $service_type_null_par  = array_diff($service_type_all_par,$service_type_son_par);
        
        //得到当前用户选中二级标签所属的一级标签和未选中二级标签的一级标签(所属行业)
        $industry_type_all_par  = array_diff($industry_type_all, $industry_type_son);
        //得到当前用户未选中二级标签的一级标签(用来匹配的条件)(所属行业)
        $industry_type_null_par = array_diff($industry_type_all_par,$industry_type_son_par);
        
        //开始匹配project表融资中且展示在前台的精选项目
        $match_str = '';
        $match_str2 = '';
        $match_str3 = '';
        $match_str4 = '';
        
        //匹配精品项目
        if(!empty($service_type_son)){
            //匹配精品
            $match_str .= 'p.inc_sign in ('.implode(',', $service_type_son).') or ';
        }
        
        //匹配业务类型未选中二级的一级标签
        if(!empty($service_type_null_par)){
            //匹配精品
            $match_str .= 'p.inc_top_sign in ('.implode(',', $service_type_null_par).') or ';
        }
        
        //匹配精品所属行业二级标签
        if(!empty($industry_type_son)){
            //匹配精品
            $match_str .= 'p.inc_industry in ('.implode(',', $industry_type_son).') or ';
        }
        
        //匹配精品所属行业未选中二级的一级标签
        if(!empty($industry_type_null_par)){
            //匹配精品
            $match_str .= 'p.inc_top_industry in ('.implode(',', $industry_type_null_par).') or ';
        }
        //匹配所在省份
        if(!empty($to_province)){
            //精品项目
            $match_str .= 'p.inc_area in ('.implode(',', $to_province).') or ';
        }
        //匹配投融资规模(根据选中的标签id匹配每一条的数值(万元))
        //标签描述:500万以下          不等式: <500
        //        500-1999万                >=500 && <=1999
        //        2000-3999万               >=2000 && <=3999
        //        4000-6999万               >=4000 && <=6999
        //        7000-9999万               >=7000 && <=9999
        //        1亿以上                    >=10000
        $model = model('Dict');
        if(!empty($size)){
            foreach ($size as $va) {
                $value = $model->field('value')->where(['id'=>$va])->find();
                if ($value['value'] == '500万以下') {
                    //精选
                    $match_str .= 'p.financing_amount<500 or ';
                }
                if ($value['value'] == '500-1999万') {
                    //精选
                    $match_str .= '(p.financing_amount>=500 && p.financing_amount<=1999) or ';
                }
                
                if ($value['value'] == '2000-3999万') {
                    //精选
                    $match_str .= '(p.financing_amount>=2000 && p.financing_amount<=3999) or ';
                }
                
                if ($value['value'] == '4000-6999万') {
                    //精选
                    $match_str .= '(p.financing_amount>=4000 && financing_amount<=6999) or ';
                }
                
                if ($value['value'] == '7000-9999万') {
                    //精选
                    $match_str .= '(p.financing_amount>=7000 && financing_amount<=9999) or ';
                }
                
                if ($value['value'] == '1亿以上 ') {
                    //精选
                    $match_str .= 'p.financing_amount>=10000 or ';
                }
            }
        }
        //精选
        $match_str = rtrim($match_str,' or ');
        
        //平台精选项目sql
        $project_list = model('Project')->alias('p')
        ->join('Dict d','p.inc_top_sign=d.id')
        ->join('Dict d2','p.inc_sign=d2.id')
        ->join('Dict d3','p.inc_top_industry=d3.id')
        ->join('MaterialLibrary ml','p.top_img=ml.ml_id')
        ->field([
            'p.pro_id'              => 'id',
            'ml.url',
            'p.pro_name'            => 'name',
            'p.company_name'        => 'top',
            'concat_ws(",",d.value,d2.value,d3.value)'=>'label',
            'p.introduction'        => 'bottom',
            'p.view_num',
            'p.type',
        ])
        ->where(['p.status' => 2 , 'p.flag' => 1])
        ->where($match_str)
        ->order("p.list_order desc,p.pro_id desc")
        ->select()
        ->toArray();
        if(empty($project_list)) {
            $this->code = 201;
            $this->msg  = '没有更多项目或资金';
            return $this->result('',$this->code,$this->msg);
        }
        
        if (!empty($project_list)) {
            foreach ($project_list as &$v) {
                $v['type_label'] = '精选项目';
                $v['bottom'] = '简介:'.$v['bottom'];
                $v['label'] = explode(',', $v['label']);
                $v['url'] = config('view_replace_str')['__DOMAIN__'].$v['url'];
            }
        }
        //精准匹配后的所有结果
        $all_project    = array_merge($project_list);
        //分页显示
        $final_project  = array_slice($all_project,$offset,$length);
        return $this->result($final_project);
    }


    /**
     * 市场-股权栏列表展示的是一级业务为新三板+股权
     */
    public function getStockRightProjectRequireMarketList($data = [])
    {
        
        $dl = model('DictLabel');
        $dl = $dl->getDictLabelApi($data['uid']);
        
        //用户无标签
        if(empty($dl)){
            //$pr_map['d.value'] = array('in','股权,新三板');
            $pr_map['pr.top_dict_id'] = array('in','234,243');
            $all_result  = $this->alias('pr')
                                ->join('Member m','pr.uid=m.uid')
                                ->join('Dict d','pr.top_dict_id=d.id')
                                ->join('Dict d2','pr.dict_id=d2.id')
                                ->join('Dict d3','pr.top_industry_id=d3.id')
                                ->field([
                                    'pr.pr_id'          => 'id',
                                    'm.userPhoto'       => 'url',
                                    'm.realName'        => 'name',
                                    'pr.name'           => 'top',
                                    'concat_ws(",",d.value,d2.value,d3.value)'=>'label',
                                    'pr.business_des'   =>'bottom',
                                    'pr.view_num',
                                    'pr.type',
                                    'pr.create_time',
                                    'pr.rank',
                                    'pr.is_top',
                                    'pr.is_care',
                                    'pr.validity_period',
                                ])
                                ->where([
                                    'pr.is_show' => 1,
                                    'pr.status' => 1,
                                ])
                                ->where($pr_map)
                                ->order('pr.is_top asc,pr.is_care asc,pr.rank desc,pr.view_num desc,pr.create_time desc')
                                ->select()
                                ->toArray();
        }else{
            $dt = model('DictType');
            $service_type_son       = [];//当前用户选择的业务类型所有的2级标签(用来匹配的)
            $industry_type_son_par  = [];//当前用户选中行业的二级标签所属的一级标签(用来匹配的)
            $size                   = [];//当前用户选择的投融资规模(用来匹配的)
            $to_province            = [];//当前用户选择的所在省份(用来匹配的)
            $province_all           = 0;
            foreach ($dl as $k => $v) {
                $sign = $dt->field('sign')->where(['dt_id'=>$v['dict_type_id']])->find();
                
                //业务类型下的标签
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
            
            $match_str = '';
            
            //匹配业务类型二级标签
            if(!empty($service_type_son)){
                $match_str .= 'pr.dict_id in ('.implode(',', $service_type_son).') and ';
            }
            
            //匹配所属行业未选中二级的一级标签
            if(!empty($industry_type_son_par)){
                $match_str .= 'pr.top_industry_id in ('.implode(',', $industry_type_son_par).') and ';
            }
            
            //匹配所在省份
            if(!empty($to_province)){
                $match_str .= 'pr.province_id in ('.implode(',', $to_province).') and  ';
            }
            
            $model = model('Dict');
            if(!empty($size)){
                foreach ($size as $va) {
                    $value = $model->field('value')->where(['id'=>$va])->find();
                    if ($value['value'] == '500万以下') {
                        $match_str .= 'pr.financing_scale<500 and ';
                    }elseif ($value['value'] == '500-1999万') {
                        $match_str .= '(pr.financing_scale>=500 && pr.financing_scale<=1999) and ';
                    }elseif ($value['value'] == '2000-3999万') {
                        $match_str .= '(pr.financing_scale>=2000 && pr.financing_scale<=3999) and ';
                    }elseif ($value['value'] == '4000-6999万') {
                        $match_str .= '(pr.financing_scale>=4000 && pr.financing_scale<=6999) and ';
                    }elseif ($value['value'] == '7000-9999万') {
                        $match_str .= '(pr.financing_scale>=7000 && pr.financing_scale<=9999) and ';
                    }elseif ($value['value'] == '1亿以上 ') {
                        $match_str .= 'pr.financing_scale>=10000 and ';
                    }
                }
            }
            
            
            $match_str   = rtrim($match_str,' and ');
            $pr_map['pr.top_dict_id'] = array('in','234,243');
            $all_result  = $this->alias('pr')
                                ->join('Member m','pr.uid=m.uid')
                                ->join('Dict d','pr.top_dict_id=d.id')
                                ->join('Dict d2','pr.dict_id=d2.id')
                                ->join('Dict d3','pr.top_industry_id=d3.id')
                                ->field([
                                    'pr.pr_id'          => 'id',
                                    'm.userPhoto'       => 'url',
                                    'm.realName'        => 'name',
                                    'pr.name'           => 'top',
                                    'concat_ws(",",d.value,d2.value,d3.value)'=>'label',
                                    'pr.business_des'   =>'bottom',
                                    'pr.view_num',
                                    'pr.type',
                                    'pr.create_time',
                                    'pr.rank',
                                    'pr.is_top',
                                    'pr.is_care',
                                    'pr.validity_period',
                                ])
                                ->where([
                                    'pr.is_show' => 1,
                                    'pr.status'  => 1,
                                ])
                                ->where($pr_map)
                                ->where($match_str)
                                ->order('pr.is_top asc,pr.is_care asc,pr.rank desc,pr.view_num desc,pr.create_time desc')
                                ->select()
                                ->toArray();
        }
        //处理数据(添加标签,类型标签)
        foreach ($all_result as $k => $v) {
            
            //项目(1个一级业务标签+1个二级业务标签+1个一级行业标签)
            //置精项目和非置精项目
              $is_vaild = prorStatus2($v['create_time'],$v['validity_period']);
               if ($is_vaild) {
                   $all_result[$k]['label'] = explode(',', $v['label']);
                    if ($v['is_care'] == 1) {
                       $all_result[$k]['type_label'] = '精选项目'; 
                    } else {
                       $all_result[$k]['type_label'] = '项目';
                    } 
               } else {
                   unset($all_result[$k]);
               }    
        }
        $all_result = array_slice($all_result,$data['offset'],$data['length']);
        if (empty($all_result)) {
                  $this->code = 201;
                  $this->msg = '没有更多股权或新三板的项目';
                  return $this->result('',$this->code,$this->msg);
        }
        return $this->result($all_result); 
    }

    /**
     * 市场-债权栏列表展示的是一级业务为新三板+股权
     */
    public function getClaimsProjectRequireMarketList($data = [])
    {

        $dl = model('DictLabel');
        $dl = $dl->getDictLabelApi($data['uid']);
        
        //用户无标签
        if(empty($dl)){
            $all_result  = $this->alias('pr')
                                ->join('Member m','pr.uid=m.uid')
                                ->join('Dict d','pr.top_dict_id=d.id')
                                ->join('Dict d2','pr.dict_id=d2.id')
                                ->join('Dict d3','pr.top_industry_id=d3.id')
                                ->field([
                                    'pr.pr_id'          => 'id',
                                    'm.userPhoto'       => 'url',
                                    'm.realName'        => 'name',
                                    'pr.name'           => 'top',
                                    'concat_ws(",",d.value,d2.value,d3.value)'=>'label',
                                    'pr.business_des'   =>'bottom',
                                    'pr.view_num',
                                    'pr.type',
                                    'pr.create_time',
                                    'pr.rank',
                                    'pr.is_top',
                                    'pr.is_care',
                                    'pr.validity_period',
                                ])
                                ->where([
                                    'pr.is_show'     => 1,
                                    'pr.status'      => 1,
                                    'pr.top_dict_id' => 250,//债权
                                ])
                                ->order('pr.is_top asc,pr.is_care asc,pr.rank desc,pr.view_num desc,pr.create_time desc')
                                ->select()
                                ->toArray();
        }else{
            $dt = model('DictType');
            $service_type_son       = [];//当前用户选择的业务类型所有的2级标签(用来匹配的)
            $industry_type_son_par  = [];//当前用户选中行业的二级标签所属的一级标签(用来匹配的)
            $size                   = [];//当前用户选择的投融资规模(用来匹配的)
            $to_province            = [];//当前用户选择的所在省份(用来匹配的)
            $province_all           = 0;
            foreach ($dl as $k => $v) {
                $sign = $dt->field('sign')->where(['dt_id'=>$v['dict_type_id']])->find();
                
                //业务类型下的标签
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
            
            $match_str = '';
            
            //匹配业务类型二级标签
            if(!empty($service_type_son)){
                $match_str .= 'pr.dict_id in ('.implode(',', $service_type_son).') and ';
            }
            
            //匹配所属行业未选中二级的一级标签
            if(!empty($industry_type_son_par)){
                $match_str .= 'pr.top_industry_id in ('.implode(',', $industry_type_son_par).') and ';
            }
            
            //匹配所在省份
            if(!empty($to_province)){
                $match_str .= 'pr.province_id in ('.implode(',', $to_province).') and  ';
            }
            
            $model = model('Dict');
            if(!empty($size)){
                foreach ($size as $va) {
                    $value = $model->field('value')->where(['id'=>$va])->find();
                    if ($value['value'] == '500万以下') {
                        $match_str .= 'pr.financing_scale<500 and ';
                    }elseif ($value['value'] == '500-1999万') {
                        $match_str .= '(pr.financing_scale>=500 && pr.financing_scale<=1999) and ';
                    }elseif ($value['value'] == '2000-3999万') {
                        $match_str .= '(pr.financing_scale>=2000 && pr.financing_scale<=3999) and ';
                    }elseif ($value['value'] == '4000-6999万') {
                        $match_str .= '(pr.financing_scale>=4000 && pr.financing_scale<=6999) and ';
                    }elseif ($value['value'] == '7000-9999万') {
                        $match_str .= '(pr.financing_scale>=7000 && pr.financing_scale<=9999) and ';
                    }elseif ($value['value'] == '1亿以上 ') {
                        $match_str .= 'pr.financing_scale>=10000 and ';
                    }
                }
            }
            
            
            $match_str   = rtrim($match_str,' and ');
            $all_result  = $this->alias('pr')
                                ->join('Member m','pr.uid=m.uid')
                                ->join('Dict d','pr.top_dict_id=d.id')
                                ->join('Dict d2','pr.dict_id=d2.id')
                                ->join('Dict d3','pr.top_industry_id=d3.id')
                                ->field([
                                    'pr.pr_id'          => 'id',
                                    'm.userPhoto'       => 'url',
                                    'm.realName'        => 'name',
                                    'pr.name'           => 'top',
                                    'concat_ws(",",d.value,d2.value,d3.value)'=>'label',
                                    'pr.business_des'   =>'bottom',
                                    'pr.view_num',
                                    'pr.type',
                                    'pr.create_time',
                                    'pr.rank',
                                    'pr.is_top',
                                    'pr.is_care',
                                    'pr.validity_period',
                                ])
                                ->where([
                                    'pr.is_show' => 1,
                                    'pr.status'  => 1,
                                    'pr.top_dict_id' =>250,
                                ])
                                ->where($match_str)
                                ->order('pr.is_top asc,pr.is_care asc,pr.rank desc,pr.view_num desc,pr.create_time desc')
                                ->select()
                                ->toArray();
        }
        //处理数据(添加标签,类型标签)
        foreach ($all_result as $k => $v) {
            //项目(1个一级业务标签+1个二级业务标签+1个一级行业标签)
            //置精项目和非置精项目
            $is_vaild = prorStatus2($v['create_time'],$v['validity_period']);
            if ($is_vaild) {
                $all_result[$k]['label'] = explode(',', $v['label']);
                if ($v['is_care'] == 1) {
                    $all_result[$k]['type_label'] = '精选项目';
                } else {
                    $all_result[$k]['type_label'] = '项目';
                }
            } else {
                unset($all_result[$k]);
            }
        }
        $all_result = array_slice($all_result,$data['offset'],$data['length']);
        if (empty($all_result)) {
            $this->code = 201;
            $this->msg = '没有更多债权项目';
            return $this->result('',$this->code,$this->msg);
        }
        return $this->result($all_result); 
    }
    
    /**
     * 市场-创业栏列表展示的是一级业务为新三板+股权
     */
    public function getChuangYeProjectRequireMarketList($data = [])
    {

        $dl = model('DictLabel');
        $dl = $dl->getDictLabelApi($data['uid']);
        
        //用户无标签
        if(empty($dl)){
            $all_result  = $this->alias('pr')
                                ->join('Member m','pr.uid=m.uid')
                                ->join('Dict d','pr.top_dict_id=d.id')
                                ->join('Dict d2','pr.dict_id=d2.id')
                                ->join('Dict d3','pr.top_industry_id=d3.id')
                                ->field([
                                    'pr.pr_id'          => 'id',
                                    'm.userPhoto'       => 'url',
                                    'm.realName'        => 'name',
                                    'pr.name'           => 'top',
                                    'concat_ws(",",d.value,d2.value,d3.value)'=>'label',
                                    'pr.business_des'   =>'bottom',
                                    'pr.view_num',
                                    'pr.type',
                                    'pr.create_time',
                                    'pr.rank',
                                    'pr.is_top',
                                    'pr.is_care',
                                    'pr.validity_period',
                                ])
                                ->where([
                                    'pr.is_show'     => 1,
                                    'pr.status'      => 1,
                                    'pr.top_dict_id' => 270,//创业孵化
                                ])
                                ->order('pr.is_top asc,pr.is_care asc,pr.rank desc,pr.view_num desc,pr.create_time desc')
                                ->select()
                                ->toArray();
        }else{
            $dt = model('DictType');
            $service_type_son       = [];//当前用户选择的业务类型所有的2级标签(用来匹配的)
            $industry_type_son_par  = [];//当前用户选中行业的二级标签所属的一级标签(用来匹配的)
            $size                   = [];//当前用户选择的投融资规模(用来匹配的)
            $to_province            = [];//当前用户选择的所在省份(用来匹配的)
            $province_all           = 0;
            foreach ($dl as $k => $v) {
                $sign = $dt->field('sign')->where(['dt_id'=>$v['dict_type_id']])->find();
                
                //业务类型下的标签
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
            
            $match_str = '';
            
            //匹配业务类型二级标签
            if(!empty($service_type_son)){
                $match_str .= 'pr.dict_id in ('.implode(',', $service_type_son).') and ';
            }
            
            //匹配所属行业未选中二级的一级标签
            if(!empty($industry_type_son_par)){
                $match_str .= 'pr.top_industry_id in ('.implode(',', $industry_type_son_par).') and ';
            }
            
            //匹配所在省份
            if(!empty($to_province)){
                $match_str .= 'pr.province_id in ('.implode(',', $to_province).') and  ';
            }
            
            $model = model('Dict');
            if(!empty($size)){
                foreach ($size as $va) {
                    $value = $model->field('value')->where(['id'=>$va])->find();
                    if ($value['value'] == '500万以下') {
                        $match_str .= 'pr.financing_scale<500 and ';
                    }elseif ($value['value'] == '500-1999万') {
                        $match_str .= '(pr.financing_scale>=500 && pr.financing_scale<=1999) and ';
                    }elseif ($value['value'] == '2000-3999万') {
                        $match_str .= '(pr.financing_scale>=2000 && pr.financing_scale<=3999) and ';
                    }elseif ($value['value'] == '4000-6999万') {
                        $match_str .= '(pr.financing_scale>=4000 && pr.financing_scale<=6999) and ';
                    }elseif ($value['value'] == '7000-9999万') {
                        $match_str .= '(pr.financing_scale>=7000 && pr.financing_scale<=9999) and ';
                    }elseif ($value['value'] == '1亿以上 ') {
                        $match_str .= 'pr.financing_scale>=10000 and ';
                    }
                }
            }
            
            
            $match_str   = rtrim($match_str,' and ');
            $all_result  = $this->alias('pr')
                                ->join('Member m','pr.uid=m.uid')
                                ->join('Dict d','pr.top_dict_id=d.id')
                                ->join('Dict d2','pr.dict_id=d2.id')
                                ->join('Dict d3','pr.top_industry_id=d3.id')
                                ->field([
                                    'pr.pr_id'          => 'id',
                                    'm.userPhoto'       => 'url',
                                    'm.realName'        => 'name',
                                    'pr.name'           => 'top',
                                    'concat_ws(",",d.value,d2.value,d3.value)'=>'label',
                                    'pr.business_des'   =>'bottom',
                                    'pr.view_num',
                                    'pr.type',
                                    'pr.create_time',
                                    'pr.rank',
                                    'pr.is_top',
                                    'pr.is_care',
                                    'pr.validity_period',
                                ])
                                ->where([
                                    'pr.is_show' => 1,
                                    'pr.status'  => 1,
                                    'pr.top_dict_id' =>270,
                                ])
                                ->where($match_str)
                                ->order('pr.is_top asc,pr.is_care asc,pr.rank desc,pr.view_num desc,pr.create_time desc')
                                ->select()
                                ->toArray();
        }
        //处理数据(添加标签,类型标签)
        foreach ($all_result as $k => $v) {
            //项目(1个一级业务标签+1个二级业务标签+1个一级行业标签)
            //置精项目和非置精项目
            $is_vaild = prorStatus2($v['create_time'],$v['validity_period']);
            if ($is_vaild) {
                $all_result[$k]['label'] = explode(',', $v['label']);
                if ($v['is_care'] == 1) {
                    $all_result[$k]['type_label'] = '精选项目';
                } else {
                    $all_result[$k]['type_label'] = '项目';
                }
            } else {
                unset($all_result[$k]);
            }
        }
        $all_result = array_slice($all_result,$data['offset'],$data['length']);
        if (empty($all_result)) {
            $this->code = 201;
            $this->msg = '没有更多创业项目';
            return $this->result('',$this->code,$this->msg);
        }
        return $this->result($all_result); 
    }

    
    /**
     * 
     * 市场-上市栏-项目列表
     */
    public function getListProjectRequireMarketList($data = [])
    {

        $dl = model('DictLabel');
        $dl = $dl->getDictLabelApi($data['uid']);
        
        //用户无标签
        if(empty($dl)){
            $all_result  = $this->alias('pr')
                                ->join('Member m','pr.uid=m.uid')
                                ->join('Dict d','pr.top_dict_id=d.id')
                                ->join('Dict d2','pr.dict_id=d2.id')
                                ->join('Dict d3','pr.top_industry_id=d3.id')
                                ->field([
                                    'pr.pr_id'          => 'id',
                                    'm.userPhoto'       => 'url',
                                    'm.realName'        => 'name',
                                    'pr.name'           => 'top',
                                    'concat_ws(",",d.value,d2.value,d3.value)'=>'label',
                                    'pr.business_des'   =>'bottom',
                                    'pr.view_num',
                                    'pr.type',
                                    'pr.create_time',
                                    'pr.rank',
                                    'pr.is_top',
                                    'pr.is_care',
                                    'pr.validity_period',
                                ])
                                ->where([
                                    'pr.is_show'     => 1,
                                    'pr.status'      => 1,
                                    'pr.top_dict_id' => 262,//上市
                                ])
                                ->order('pr.is_top asc,pr.is_care asc,pr.rank desc,pr.view_num desc,pr.create_time desc')
                                ->select()
                                ->toArray();
        }else{
            $dt = model('DictType');
            $service_type_son       = [];//当前用户选择的业务类型所有的2级标签(用来匹配的)
            $industry_type_son_par  = [];//当前用户选中行业的二级标签所属的一级标签(用来匹配的)
            $size                   = [];//当前用户选择的投融资规模(用来匹配的)
            $to_province            = [];//当前用户选择的所在省份(用来匹配的)
            $province_all           = 0;
            foreach ($dl as $k => $v) {
                $sign = $dt->field('sign')->where(['dt_id'=>$v['dict_type_id']])->find();
                
                //业务类型下的标签
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
            
            $match_str = '';
            
            //匹配业务类型二级标签
            if(!empty($service_type_son)){
                $match_str .= 'pr.dict_id in ('.implode(',', $service_type_son).') and ';
            }
            
            //匹配所属行业未选中二级的一级标签
            if(!empty($industry_type_son_par)){
                $match_str .= 'pr.top_industry_id in ('.implode(',', $industry_type_son_par).') and ';
            }
            
            //匹配所在省份
            if(!empty($to_province)){
                $match_str .= 'pr.province_id in ('.implode(',', $to_province).') and  ';
            }
            
            $model = model('Dict');
            if(!empty($size)){
                foreach ($size as $va) {
                    $value = $model->field('value')->where(['id'=>$va])->find();
                    if ($value['value'] == '500万以下') {
                        $match_str .= 'pr.financing_scale<500 and ';
                    }elseif ($value['value'] == '500-1999万') {
                        $match_str .= '(pr.financing_scale>=500 && pr.financing_scale<=1999) and ';
                    }elseif ($value['value'] == '2000-3999万') {
                        $match_str .= '(pr.financing_scale>=2000 && pr.financing_scale<=3999) and ';
                    }elseif ($value['value'] == '4000-6999万') {
                        $match_str .= '(pr.financing_scale>=4000 && pr.financing_scale<=6999) and ';
                    }elseif ($value['value'] == '7000-9999万') {
                        $match_str .= '(pr.financing_scale>=7000 && pr.financing_scale<=9999) and ';
                    }elseif ($value['value'] == '1亿以上 ') {
                        $match_str .= 'pr.financing_scale>=10000 and ';
                    }
                }
            }
            
            
            $match_str   = rtrim($match_str,' and ');
            $all_result  = $this->alias('pr')
                                ->join('Member m','pr.uid=m.uid')
                                ->join('Dict d','pr.top_dict_id=d.id')
                                ->join('Dict d2','pr.dict_id=d2.id')
                                ->join('Dict d3','pr.top_industry_id=d3.id')
                                ->field([
                                    'pr.pr_id'          => 'id',
                                    'm.userPhoto'       => 'url',
                                    'm.realName'        => 'name',
                                    'pr.name'           => 'top',
                                    'concat_ws(",",d.value,d2.value,d3.value)'=>'label',
                                    'pr.business_des'   =>'bottom',
                                    'pr.view_num',
                                    'pr.type',
                                    'pr.create_time',
                                    'pr.rank',
                                    'pr.is_top',
                                    'pr.is_care',
                                    'pr.validity_period',
                                ])
                                ->where([
                                    'pr.is_show' => 1,
                                    'pr.status'  => 1,
                                    'pr.top_dict_id' =>262,
                                ])
                                ->where($match_str)
                                ->order('pr.is_top asc,pr.is_care asc,pr.rank desc,pr.view_num desc,pr.create_time desc')
                                ->select()
                                ->toArray();
        }
        //处理数据(添加标签,类型标签)
        foreach ($all_result as $k => $v) {
            //项目(1个一级业务标签+1个二级业务标签+1个一级行业标签)
            //置精项目和非置精项目
            $is_vaild = prorStatus2($v['create_time'],$v['validity_period']);
            if ($is_vaild) {
                $all_result[$k]['label'] = explode(',', $v['label']);
                if ($v['is_care'] == 1) {
                    $all_result[$k]['type_label'] = '精选项目';
                } else {
                    $all_result[$k]['type_label'] = '项目';
                }
            } else {
                unset($all_result[$k]);
            }
        }
        $all_result = array_slice($all_result,$data['offset'],$data['length']);
        if (empty($all_result)) {
            $this->code = 201;
            $this->msg = '没有更多上市项目';
            return $this->result('',$this->code,$this->msg);
        }
        return $this->result($all_result); 
    }

    
    //市场-其他栏-项目列表(金融产品)
    public function getOtherProjectRequireMarketList($data = [])
    {

        $dl = model('DictLabel');
        $dl = $dl->getDictLabelApi($data['uid']);
        
        //用户无标签
        if(empty($dl)){
            $all_result  = $this->alias('pr')
                                ->join('Member m','pr.uid=m.uid')
                                ->join('Dict d','pr.top_dict_id=d.id')
                                ->join('Dict d2','pr.dict_id=d2.id')
                                ->join('Dict d3','pr.top_industry_id=d3.id')
                                ->field([
                                    'pr.pr_id'          => 'id',
                                    'm.userPhoto'       => 'url',
                                    'm.realName'        => 'name',
                                    'pr.name'           => 'top',
                                    'concat_ws(",",d.value,d2.value,d3.value)'=>'label',
                                    'pr.business_des'   =>'bottom',
                                    'pr.view_num',
                                    'pr.type',
                                    'pr.create_time',
                                    'pr.rank',
                                    'pr.is_top',
                                    'pr.is_care',
                                    'pr.validity_period',
                                ])
                                ->where([
                                    'pr.is_show'     => 1,
                                    'pr.status'      => 1,
                                    'pr.top_dict_id' => 280,//金融产品
                                ])
                                ->order('pr.is_top asc,pr.is_care asc,pr.rank desc,pr.view_num desc,pr.create_time desc')
                                ->select()
                                ->toArray();
        }else{
            $dt = model('DictType');
            $service_type_son       = [];//当前用户选择的业务类型所有的2级标签(用来匹配的)
            $industry_type_son_par  = [];//当前用户选中行业的二级标签所属的一级标签(用来匹配的)
            $size                   = [];//当前用户选择的投融资规模(用来匹配的)
            $to_province            = [];//当前用户选择的所在省份(用来匹配的)
            $province_all           = 0;
            foreach ($dl as $k => $v) {
                $sign = $dt->field('sign')->where(['dt_id'=>$v['dict_type_id']])->find();
                
                //业务类型下的标签
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
            
            $match_str = '';
            
            //匹配业务类型二级标签
            if(!empty($service_type_son)){
                $match_str .= 'pr.dict_id in ('.implode(',', $service_type_son).') and ';
            }
            
            //匹配所属行业未选中二级的一级标签
            if(!empty($industry_type_son_par)){
                $match_str .= 'pr.top_industry_id in ('.implode(',', $industry_type_son_par).') and ';
            }
            
            //匹配所在省份
            if(!empty($to_province)){
                $match_str .= 'pr.province_id in ('.implode(',', $to_province).') and  ';
            }
            
            $model = model('Dict');
            if(!empty($size)){
                foreach ($size as $va) {
                    $value = $model->field('value')->where(['id'=>$va])->find();
                    if ($value['value'] == '500万以下') {
                        $match_str .= 'pr.financing_scale<500 and ';
                    }elseif ($value['value'] == '500-1999万') {
                        $match_str .= '(pr.financing_scale>=500 && pr.financing_scale<=1999) and ';
                    }elseif ($value['value'] == '2000-3999万') {
                        $match_str .= '(pr.financing_scale>=2000 && pr.financing_scale<=3999) and ';
                    }elseif ($value['value'] == '4000-6999万') {
                        $match_str .= '(pr.financing_scale>=4000 && pr.financing_scale<=6999) and ';
                    }elseif ($value['value'] == '7000-9999万') {
                        $match_str .= '(pr.financing_scale>=7000 && pr.financing_scale<=9999) and ';
                    }elseif ($value['value'] == '1亿以上 ') {
                        $match_str .= 'pr.financing_scale>=10000 and ';
                    }
                }
            }
            
            
            $match_str   = rtrim($match_str,' and ');
            $all_result  = $this->alias('pr')
                                ->join('Member m','pr.uid=m.uid')
                                ->join('Dict d','pr.top_dict_id=d.id')
                                ->join('Dict d2','pr.dict_id=d2.id')
                                ->join('Dict d3','pr.top_industry_id=d3.id')
                                ->field([
                                    'pr.pr_id'          => 'id',
                                    'm.userPhoto'       => 'url',
                                    'm.realName'        => 'name',
                                    'pr.name'           => 'top',
                                    'concat_ws(",",d.value,d2.value,d3.value)'=>'label',
                                    'pr.business_des'   =>'bottom',
                                    'pr.view_num',
                                    'pr.type',
                                    'pr.create_time',
                                    'pr.rank',
                                    'pr.is_top',
                                    'pr.is_care',
                                    'pr.validity_period',
                                ])
                                ->where([
                                    'pr.is_show' => 1,
                                    'pr.status'  => 1,
                                    'pr.top_dict_id' =>280,
                                ])
                                ->where($match_str)
                                ->order('pr.is_top asc,pr.is_care asc,pr.rank desc,pr.view_num desc,pr.create_time desc')
                                ->select()
                                ->toArray();
        }
        //处理数据(添加标签,类型标签)
        foreach ($all_result as $k => $v) {
            //项目(1个一级业务标签+1个二级业务标签+1个一级行业标签)
            //置精项目和非置精项目
            $is_vaild = prorStatus2($v['create_time'],$v['validity_period']);
            if ($is_vaild) {
                $all_result[$k]['label'] = explode(',', $v['label']);
                if ($v['is_care'] == 1) {
                    $all_result[$k]['type_label'] = '精选项目';
                } else {
                    $all_result[$k]['type_label'] = '项目';
                }
            } else {
                unset($all_result[$k]);
            }
        }
        $all_result = array_slice($all_result,$data['offset'],$data['length']);
        if (empty($all_result)) {
            $this->code = 201;
            $this->msg = '没有更多上市金融产品项目';
            return $this->result('',$this->code,$this->msg);
        }
        return $this->result($all_result); 
    }
    
    //首页-搜索-业务
    public function getIndexSearchBusiness($keyword = '',$page = 1,$uid = 0,$length = 0,$offset=0)
    {
        $match_final = [];//存放最终匹配的所有结果
        $match_project = '';//精选项目匹配的所有条件
        $match_project_require = '';//发布项目匹配的所有条件
        $match_orgainze = '';//精选机构匹配的所有条件
        $match_orgainze_require = '';//发布机构匹配的所有条件
        $match_publish = '';//发现匹配的所有条件
        $match_label = '';//人脉匹配的所有条件
        //1.平台精选项目
        $project = model('Project')->alias('p')
                         ->join('Dict d','p.inc_top_sign=d.id','left')
                         ->join('Dict d2','p.inc_sign=d2.id','left')
                         ->join('Dict d3','p.inc_top_industry=d3.id','left')
                         ->join('MaterialLibrary ml','p.top_img=ml.ml_id','left')
                         ->field([
                            'p.pro_id',
                            'ml.url'        =>'img',
                            'p.pro_name'    =>'name',
                            'p.company_name'=>'pro_name',
                            'concat_ws(",",d.value,d2.value,d3.value)'=>'label',
                            'p.introduction'=>'des',
                            'p.view_num',
                            'p.type',
                        ])
                        ->where(['p.status' => 2 , 'p.flag' => 1]);
        //2.用户发布项目
        $pr = $this->alias('pr')
                        ->join('Dict d','pr.top_dict_id=d.id','left')
                        ->join('Dict d2','pr.dict_id=d2.id','left')
                        ->join('Dict d3','pr.top_industry_id=d3.id','left')
                        ->join('Member m','pr.uid=m.uid')
                        ->field([
                            'pr.pr_id'=>'pro_id',
                            'm.userPhoto'=>'img',
                            'm.realName'=>'name',
                            'pr.name'=>'pro_name',
                            'concat_ws(",",d.value,d2.value,d3.value)'=>'label',
                            'pr.business_des'=>'des',
                            'pr.view_num',
                            'pr.type',  
                        ])
                        ->where(['pr.is_show' => 1 , 'pr.status' => 1]); 
                        
        //3.平台精选资金(无业务类型)

        
        //4.用户发布资金
        $organize_require = model('OrganizeRequire');
        $or = $organize_require->alias('or')
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
        
        //5.发现
        $publish = model('Publish');
        $pb = $publish->alias('pb')
                ->join('Member m','pb.uid=m.uid','left')
                ->join('Through t','pb.uid=t.uid','left')
                ->field([
                    'pb.id',
                    'm.uid',
                    'm.userPhoto',
                    'm.realName',
                    'm.company_jc',
                    'm.position',
                    'pb.content',
                    'pb.img_id',
                    'pb.create_time',
                    'pb.point_num',
                    'pb.comment_num',
                    'pb.type',
                ])
                ->order('pb.rank desc,pb.id desc')
                ->where(['pb.is_del'=>2,'pb.status'=>1]);

        //6.人脉
        $through = model('Through');
        $tr = $through->alias('t')
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
            //筛选项目(精选/用户发布),资金(精选/用户发布)---匹配条件:(业务)标签
            //筛选发现,人脉----匹配条件:(业务)标签,(个性)标签
            $where = "dt.sign = 'service_type'";
            $dict = model('Dict');
            $dict_column = $dict->alias('d')
                                ->join('DictType dt','d.dt_id = dt.dt_id')
                                ->field('d.id')
                                ->where($where) 
                                ->where('d.value like "%'.$keyword.'%"')
                                ->select()
                                ->toArray();
            //如果根据关键字匹配到了service_typele的dict_id
            if(!empty($dict_column)){

                $dict_id =implode(',', array_column($dict_column,'id'));

                //组装精选项目偏好一二级service_type的dict_id
               $match_project .= 'p.inc_top_sign in ('.$dict_id.') or p.inc_sign in ('.$dict_id.') or ';

                //组装发布项目偏好一二级service_type的dict_id
               $match_project_require .= 'pr.top_dict_id in ('.$dict_id.') or pr.dict_id in ('.$dict_id.') or ';

            
                //创建查询用户发布项目的sql语句
                $project_require_list = $pr->where(rtrim($match_project_require,' or '))
                                           ->buildSql();                 
                //执行合并查询平台精选项目&&用户发布项目
                $project_list = $project->where(rtrim($match_project,' or '))
                                        ->union($project_require_list,true)
                                        ->select(false);
                //返回排序且分页后的合并的精选/用户发布项目
                $pro_sql = "($project_list)";
                $all_project = Db::table($pro_sql.' as a')->field(
                    ['a.pro_id',
                    'a.img',
                    'a.name',
                    'a.pro_name',
                    'a.label',
                    'a.des',
                    'a.view_num',
                    'a.type',
                   ]) 
                  ->order('a.pro_id desc')
                  ->limit($offset, $length)
                  ->select();

                if(!empty($all_project)){
                    foreach ($all_project as &$each_project) {
                          if (!empty($each_project['label'])) {
                              $each_project['label'] = explode(',', $each_project['label']);
                          } else {
                              $each_project['label'] = [];
                          }
                           
                       }
                    $match_final['project'] = $all_project;
                }
            

               
               //发布机构service_type标签条件
               $ord = model('OrganizeRequireDict');
               $ord_re = $ord->Distinct(true)
                             ->field('or_id')
                             ->where('dict_id in ('.$dict_id.')')
                             ->where(['is_del'=>2])
                             ->select()
                             ->toArray();
                 
                if(!empty($ord_re)){
                    
                    $or_id_column = implode(',',array_column($ord_re,'or_id'));
                    //组装发布机构service_type标签
                    $match_orgainze_require .= 'or.or_id in ('.$or_id_column.') or ';
                    $all_organize = $or->where(rtrim($match_orgainze_require,' or '))
                                       ->order("or.or_id desc")
                                       ->limit($offset,$length)
                                       ->select()
                                       ->toArray();

                    if(!empty($all_organize)){
                        foreach ($all_organize as &$each_organize) {
                            $label = explode(',', $each_organize['tag']);
                            if (count($label)>3) {
                             $label = array_slice($label,0,3);
                            }
                            $each_organize['tag'] = $label; 
                       
                            $each_organize['des'] = '简介:'.$each_organize['des'];
                         
                        }
                        $match_final['organize'] = $all_organize; 
                    }
                    

                }

               //人脉service_type标签
               $lab = model('DictLabel');
               $lab_re = $lab->Distinct(true)
                             ->field('uid')
                             ->where('dict_id in ('.$dict_id.')')
                             ->where(['is_del'=>2])
                             ->select()
                             ->toArray();
                if(!empty($lab_re)){

                   //组装人脉service_type标签条件
                   $match_label .= 't.uid in ('.implode(',',array_column($lab_re,'uid')).') or '; 
                }else{
                   $match_label .= 't.uid in ("") or '; 
                }
               
            }
        
        
            //根据模糊关键词匹配个性标签,返回对应的发布id
            $tp = model('TagsPublish');
            $pub_id = $tp->field('pub_id')
                         ->where('tag_name like "%'.$keyword.'%"')
                         ->select()
                         ->toArray();
            if(!empty($pub_id)){
                //组装发现个性标签条件
                $match_publish .= 'pb.id in ('.implode(',',array_column($pub_id,'pub_id')).') or ';

            }else{
                //组装发现个性标签条件
                $match_publish .= 'pb.id in ("") or ';
            }

            //执行查询发现
            $publish_list = $pb->where(rtrim($match_publish,' or '))
                               ->limit($offset,$length)
                               ->select()
                               ->toArray();

            if(!empty( $publish_list)){
                    //处理是否关注,图片,点赞,评论,个性标签展示
                        //当前用户是否关注查询的发现
                        //查询当前用户关注的合伙人(未取关)
                        $to_uid_arr = [];
                        // echo 1;die;
                        $to_uid = model('FollowUser')->field('to_uid')
                                                 ->where(['from_uid'=>$uid,'is_del'=>2])
                                                 ->select()
                                                 ->toArray();
                        // echo 222;die;
                        if (!empty($to_uid)) {
                            $to_uid_arr= array_column($to_uid, 'to_uid');
                        }

                        //图片
                        $ml = model('MaterialLibrary');
                        //添加个性标签的发现
                        $my = model('TagsPublish');
                        
                        foreach ($publish_list as &$v) {

                            //是否关注
                            if (in_array($v['uid'], $to_uid_arr)) {
                                $v['is_follow'] = '已关注';
                            }else{
                                $v['is_follow'] = '关注';
                            }
                            $v['img_url'] = [];
                            //是否添加了图片
                            if ($v['img_id']) {
                               $url = $ml->field('url')->where('ml_id in ('.$v['img_id'].')')->select()->toArray();
                               if (!empty($url)) {
                                    $img_url = array_column($url, 'url');
                                    foreach ($img_url as &$val) {
                                        $val = config('view_replace_str')['__DOMAIN__'].$val;
                                    }
                                    $v['img_url'] = $img_url;

                               }
                            }

                            //个性标签名
                            $my_tag = [];

         
                            //是否添加个性标签
                            $tag = $my->field('tag_name')
                                      ->where(['pub_id'=>$v['id']])
                                      ->select()
                                      ->toArray();
                            if (!empty($tag)) {
                                $my_tag =  array_column($tag,'tag_name');
                            }
                            
                            $v['all_tag'] = $my_tag;
                            $v['create_time'] = friend_date(strtotime($v['create_time']));
                            //当前用户点赞过哪些一键发布
                            $point = model('Point')->field('pub_id')
                                                   ->where(['uid'=>$uid])
                                                   ->select()
                                                   ->toArray();
                            if (!empty($point)) {
                                 $point_arr = array_column($point, 'pub_id');
                                if (in_array($v['id'],$point_arr)) {
                                    $v['is_point'] = 1;
                                }else{
                                    $v['is_point'] = 0;
                                }
                            }
                        } 
                    $match_final['publish'] = $publish_list;
            }

            
            
            //根据模糊关键词匹配tags_user表中的个性标签,返回对应的用户uid
            $tu = model('TagsUser');
            $uid_arr = $tu->field('uid')
                      ->where('tag_name like "%'.$keyword.'%"')
                      ->select()
                      ->toArray();
            if(!empty($uid_arr)){
                //组装人脉个性标签条件
                $match_label .= 't.uid in ('.implode(',',array_column($uid_arr,'uid')).') or ';
            }else{
                 $match_label .= 't.uid in ("") or ';
            }   
            // echo 333;die;   
            //执行查询人脉
            $through_list = $tr->where(rtrim($match_label,' or '))
                               ->limit($offset,$length)
                               ->select()
                               ->toArray();   
            if(!empty($through_list)){
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
                $match_final['person'] = $through_list;
            } 

            if (empty($match_final)) {
                $this->code = 214;
                $this->msg = '抱歉没有发现的相关内容';
                return $this->result('',$this->code,$this->msg);
            }
        return $this->result($match_final); 

    }

    //首页-搜索-项目
    public function getIndexSearchProject($keyword = '',$page = 1,$uid = 0,$length = 0,$offset = 0)
    {
       
        $match_project = '';//精选项目匹配的所有条件
        $match_project_require = '';//发布项目匹配的所有条件

        //1.平台精选项目
        $project = model('Project')->alias('p')
                         ->join('Dict d','p.inc_top_sign=d.id','left')
                         ->join('Dict d2','p.inc_sign=d2.id','left')
                         ->join('Dict d3','p.inc_top_industry=d3.id','left')
                         ->join('MaterialLibrary ml','p.top_img=ml.ml_id','left')
                         ->field([
                            'p.pro_id',
                            'ml.url'        =>'img',
                            'p.pro_name'    =>'name',
                            'p.company_name'=>'pro_name',
                            'concat_ws(",",d.value,d2.value,d3.value)'=>'label',
                            'p.introduction'=>'des',
                            'p.view_num',
                            'p.type',
                        ])
                        ->where(['p.status' => 2 , 'p.flag' => 1]);
        //2.用户发布项目
        $pr = $this->alias('pr')
                        ->join('Dict d','pr.top_dict_id=d.id','left')
                        ->join('Dict d2','pr.dict_id=d2.id','left')
                        ->join('Dict d3','pr.top_industry_id=d3.id','left')
                        ->join('Member m','pr.uid=m.uid')
                        ->field([
                            'pr.pr_id'=>'pro_id',
                            'm.userPhoto'=>'img',
                            'm.realName'=>'name',
                            'pr.name'=>'pro_name',
                            'concat_ws(",",d.value,d2.value,d3.value)'=>'label',
                            'pr.business_des'=>'des',
                            'pr.view_num',
                            'pr.type',  
                        ])
                        ->where(['pr.is_show' => 1 , 'pr.status' => 1]);
                //组装精选项目简称&&全称条件
                $match_project .= 'p.pro_name like "%'.$keyword.'%" or p.company_name like "%'.$keyword.'%" or ';

                //组装精选项目投融规模条件
                $match_project .= 'p.financing_amount like "%'.$keyword.'%" or ';
                
                //组装发布项目简称条件
                $match_project_require .= 'pr.name like "%'.$keyword.'%" or ';

                //组装发布项目投融规模条件
                $match_project_require .= 'pr.financing_scale like "%'.$keyword.'%" or ';

                //获取数据字典to_province类型的标签id
                $where = "dt.sign = 'to_province'";

                //查询dict表中模糊匹配到的to_province偏好dict_id
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

                    //组装精选项目省份条件
                    $match_project .= 'p.inc_area in ('.implode(',', array_column($dict_column,'id')).') or ';

                    //组装发布项目省份条件
                    $match_project_require .= 'pr.province_id in ('.implode(',', array_column($dict_column,'id')).') or ';
   
                } 
                
                    //创建查询用户发布项目的sql语句
                    $project_require_list = $pr->where(rtrim($match_project_require,' or '))
                                               ->buildSql();                 
                    //执行合并查询平台精选项目&&用户发布项目
                    $project_list = $project->where(rtrim($match_project,' or '))
                                            ->union($project_require_list,true)
                                            ->select(false);
                    //返回排序且分页后的合并的精选/用户发布项目
                    $pro_sql = "($project_list)";
                    $all_project = Db::table($pro_sql.' as a')->field([
                        'a.pro_id',
                        'a.img',
                        'a.name',
                        'a.pro_name',
                        'a.label',
                        'a.des',
                        'a.view_num',
                        'a.type',
                        ]) 
                      ->order('a.pro_id desc')
                      ->limit($offset, $length)
                      ->select();

                    if(!empty($all_project)){
                        foreach ($all_project as &$each_project) {
                          if (!empty($each_project['label'])) {
                              $each_project['label'] = explode(',', $each_project['label']);
                          } else {
                              $each_project['label'] = [];
                          }
                           
                       }
                   

                    }
                    if (empty($all_project)) {
                        $this->code = 214;
                        $this->msg = '抱歉没有发现的相关内容';
                        return $this->result('',$this->code,$this->msg);
                   }
                   return $this->result($all_project); 
    }
    //首页-搜索-行业
    public function getIndexSearchIndustry($keyword = '',$page = 1,$uid = 0,$length = 0,$offset = 0)
    {
        $match_final = [];//存放最终匹配的所有项目
        $match_project = '';//精选项目匹配的所有条件
        $match_project_require = '';//发布项目匹配的所有条件
        $match_orgainze = '';//精选机构匹配的所有条件
        $match_orgainze_require = '';//发布机构匹配的所有条件
        $match_publish = '';//发现匹配的所有条件
        $match_label = '';//人脉匹配的所有条件


        //1.平台精选项目
        $project = model('Project')->alias('p')
                         ->join('Dict d','p.inc_top_sign=d.id','left')
                         ->join('Dict d2','p.inc_sign=d2.id','left')
                         ->join('Dict d3','p.inc_top_industry=d3.id','left')
                         ->join('MaterialLibrary ml','p.top_img=ml.ml_id','left')
                         ->field([
                            'p.pro_id',
                            'ml.url'        =>'img',
                            'p.pro_name'    =>'name',
                            'p.company_name'=>'pro_name',
                            'concat_ws(",",d.value,d2.value,d3.value)'=>'label',
                            'p.introduction'=>'des',
                            'p.view_num',
                            'p.type',
                        ])
                        ->where(['p.status' => 2 , 'p.flag' => 1]);
        //2.用户发布项目
        $pr = $this->alias('pr')
                        ->join('Dict d','pr.top_dict_id=d.id','left')
                        ->join('Dict d2','pr.dict_id=d2.id','left')
                        ->join('Dict d3','pr.top_industry_id=d3.id','left')
                        ->join('Member m','pr.uid=m.uid')
                        ->field([
                            'pr.pr_id'=>'pro_id',
                            'm.userPhoto'=>'img',
                            'm.realName'=>'name',
                            'pr.name'=>'pro_name',
                            'concat_ws(",",d.value,d2.value,d3.value)'=>'label',
                            'pr.business_des'=>'des',
                            'pr.view_num',
                            'pr.type',  
                        ])
                        ->where(['pr.is_show' => 1 , 'pr.status' => 1]); 
                        
        //3.平台精选资金
        $organize = model('Organize');
        $o = $organize ->alias('o')
                 ->join('OrganizeDict od','o.org_id=od.org_id')
                 ->join('Dict d','od.id=d.id')
                 ->join('Dict d2','d.fid = d2.id')
                 ->join('Dict d3','o.unit = d3.id')
                 ->join('MaterialLibrary ml','o.top_img = ml.ml_id')
                 ->field([
                    'o.org_id',
                    'ml.url'=>'img_url',
                    'o.org_short_name'=>'name',
                    'concat_ws("|",o.contacts,o.position)'=>'top_name',
                    'group_concat(d2.value separator ",")'=>'tag',
                    'concat_ws(",",o.scale_min,o.scale_max,d3.value)'=>'des',
                    'o.view_num',
                    'o.type',
                  ])
                 ->group('o.org_id')
                 ->where(['o.status'=>2, 'o.flag'=>1 ]);
        
        //4.用户发布资金
        $organize_require = model('OrganizeRequire');
        $or = $organize_require->alias('or')
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
        
        //5.发现
        $publish = model('Publish');
        $pb = $publish->alias('pb')
                ->join('Member m','pb.uid=m.uid','left')
                ->join('Through t','pb.uid=t.uid','left')
                ->field([
                    'pb.id',
                    'm.uid',
                    'm.userPhoto',
                    'm.realName',
                    'm.company_jc',
                    'm.position',
                    'pb.content',
                    'pb.img_id',
                    'pb.create_time',
                    'pb.point_num',
                    'pb.comment_num',
                    'pb.type',
                ])
                ->order('pb.rank desc,pb.id desc')
                ->where(['pb.is_del'=>2,'pb.status'=>1]);

        //6.人脉
        $through = model('Through');
        $tr = $through->alias('t')
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
                //I.筛选项目(精选/用户发布)---匹配条件:1.行业标签
                //II.筛选资金(精选/用户发布)---匹配条件:1.行业标签
                //III.筛选发现---匹配条件:1.行业标签,2个性标签
                //IIII.筛选人脉---匹配条件:1.行业标签,2.个性标签
                
                //获取数据字典industry类型的标签id
                $where = "dt.sign = 'industry'";
                //查询dict表中模糊匹配到的industry偏好标签id
                $dict = model('Dict');
             
                $dict_column = $dict->alias('d')
                             ->join('DictType dt','d.dt_id = dt.dt_id')
                             ->field('d.id')
                             ->where($where) 
                             ->where('d.value like "%'.$keyword.'%"')
                             ->select()
                             ->toArray();

                //如果根据关键字匹配到industry偏好标签
                if(!empty($dict_column)){
                   $dict_id =implode(',', array_column($dict_column,'id'));
                   
                   //组装精选项目行业偏好标签条件
                   $match_project .= 'p.inc_top_industry in ('.$dict_id.') or p.inc_industry in ('.$dict_id.') or ';
                   //组装发布项目行业偏好标签条件
                   $match_project_require .= 'pr.top_industry_id in ('.$dict_id.') or pr.top_industry_id in ('.$dict_id.') or ';
                   //根据精选机构industry偏好标签条件得到精选机构id
                   $od = model('OrganizeDict');
                   $od_re = $od->Distinct(true)
                               ->field('org_id')
                               ->where('id in ('.$dict_id.')')
                               ->select()
                               ->toArray();
 
                   if(!empty($od_re)){

                      $org_id_column = implode(',',array_column($od_re,'org_id'));
                      //组装根据industry偏好标签匹配到的精选机构id的集合
                      $match_orgainze .= 'o.org_id in ('.$org_id_column.') or ';
                   }else{
                      $match_orgainze .= 'o.org_id in ("") or ';
                   }
                    //根据发布机构industry偏好标签条件得到发布机构id
                   $ord = model('OrganizeRequireDict');
                   $ord_re = $ord->Distinct(true)
                                 ->field('or_id')
                                 ->where('dict_id in ('.$dict_id.')')
                                 ->where(['is_del'=>2])
                                 ->select()
                                 ->toArray();
                   if(!empty($ord_re)){

                      $or_id_column = implode(',',array_column($ord_re,'or_id'));
                      //组装根据industry偏好标签匹配到的发布机构id的集合
                      $match_orgainze_require .= 'or.or_id in ('.$or_id_column.') or ';
                   }else{
                      $match_orgainze_require .= 'or.or_id in ("") or ';
                   }
                   
                   //人脉行业偏好标签
                   $lab = model('DictLabel');
                   //根据人脉industry偏好标签条件得到合伙人uid
                   $lab_re = $lab->Distinct(true)
                                 ->field('uid')
                                 ->where('dict_id in ('.$dict_id.')')
                                 ->where(['is_del'=>2])
                                 ->select()
                                 ->toArray();
                    if(!empty($lab_re)){

                       $uid_colum = implode(',',array_column($lab_re,'uid'));
                       //组装根据人脉industry偏好标签匹配到的合伙人uid的集合
                       $match_label .= 't.uid in ('.$uid_colum.') or '; 
                    }else{
                        $match_label .= 't.uid in ("") or ';  
                    }

                    //创建查询用户发布项目的sql语句
                
                    $project_require_list = $pr->where(rtrim($match_project_require,' or '))
                                               ->buildSql();
                    // dump($project_require_list);die;                 
                    //执行合并查询平台精选项目&&用户发布项目
                    $project_list = $project->where(rtrim($match_project,' or '))
                                            ->union($project_require_list,true)
                                            ->select(false);

                    //返回排序且分页后的合并的精选/用户发布项目
                    $pro_sql = "($project_list)";
                    $all_project = Db::table($pro_sql.' as a')->field([
                    'a.pro_id',
                    'a.img',
                    'a.name',
                    'a.pro_name',
                    'a.label',
                    'a.des',
                    'a.view_num',
                    'a.type',
                    ]) 
                      ->order('a.pro_id desc')
                      ->limit($offset, $length)
                      ->select();
                    
                    if(!empty($all_project)){
                        foreach ($all_project as &$each_project) {
                          if (!empty($each_project['label'])) {
                              $each_project['label'] = explode(',', $each_project['label']);
                          } else {
                              $each_project['label'] = [];
                          }
                           
                       }
                        $match_final['project'] = $all_project;
                    }
                    
                    //创建查询用户发布机构的sql语句
                    $organize_require_list = $or->where(rtrim($match_orgainze_require,' or '))
                                                ->buildSql();
                
                    //执行合并查询平台机构&&用户发布机构
                    $organize_list = $o->where(rtrim($match_orgainze,' or '))
                                       ->union($organize_require_list,true)
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

                    if (!empty($all_organize)) {
                        foreach ($all_organize  as &$value) {
                            $label = explode(',',$value['tag']);
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
                         $match_final['organize'] = $all_organize;
                     } 

                }

                //根据模糊关键词匹配tags_publish表中的个性标签,返回对应的发布id

                $tp = model('TagsPublish');
                $pub_id = $tp->field('pub_id')
                             ->where('tag_name like "%'.$keyword.'%"')
                             ->select()
                             ->toArray();
                if(!empty($pub_id)){
                    //组装根据i个性标签匹配到的发现id的集合
                    $match_publish .= 'pb.id in ('.implode(',',array_column($pub_id,'pub_id')).') or ';

                }else{
                    $match_publish .= 'pb.id in ("") or ';
                }
                //根据模糊关键词匹配tags_user表中的个性标签,返回对应的用户uid

                $tu = model('TagsUser');

                $uid_arr = $tu->field('uid')
                          ->where('tag_name like "%'.$keyword.'%"')
                          ->select()
                          ->toArray();

                if(!empty($uid_arr)){
                    //组装根据i个性标签匹配到的发现用户uid的集合
                    $match_label .= 't.uid in ('.implode(',',array_column($uid_arr,'uid')).') or ';
                }else{
                    $match_label .= 't.uid in ("") or ';
                }
                
                
            
                //执行查询发现
                $publish_list = $pb->where(rtrim($match_publish,' or '))
                                   ->limit($offset,$length)
                                   ->select()
                                   ->toArray();
                
                if (!empty($publish_list)) {
                        //处理是否关注,图片,点赞,评论,个性标签展示
                        //当前用户是否关注查询的发现
                        //查询当前用户关注的合伙人(未取关)
                        $to_uid_arr = [];
                        $to_uid = model('FollowUser')->field('to_uid')
                                                 ->where(['from_uid'=>$uid,'is_del'=>2])
                                                 ->select()
                                                 ->toArray();
                      
                        if (!empty($to_uid)) {
                            $to_uid_arr= array_column($to_uid, 'to_uid');
                        }

                        //图片
                        $ml = model('MaterialLibrary');
                        //添加个性标签的发现
                        $my = model('TagsPublish');
                        
                        foreach ($publish_list as &$v) {

                            //是否关注
                            if (in_array($v['uid'], $to_uid_arr)) {
                                $v['is_follow'] = '已关注';
                            }else{
                                $v['is_follow'] = '关注';
                            }
                            $v['img_url'] = [];
                            //是否添加了图片
                            if ($v['img_id']) {
                               $url = $ml->field('url')->where('ml_id in ('.$v['img_id'].')')->select()->toArray();
                               if (!empty($url)) {
                                    $img_url = array_column($url, 'url');
                                    foreach ($img_url as &$val) {
                                        $val = config('view_replace_str')['__DOMAIN__'].$val;
                                    }
                                    $v['img_url'] = $img_url;

                               }
                            }

                            //个性标签名
                            $my_tag = [];

         
                            //是否添加个性标签
                            $tag = $my->field('tag_name')
                                      ->where(['pub_id'=>$v['id']])
                                      ->select()
                                      ->toArray();
                            if (!empty($tag)) {
                                $my_tag =  array_column($tag,'tag_name');
                            }
                            
                            $v['all_tag'] = $my_tag;
                            $v['create_time'] = friend_date(strtotime($v['create_time']));
                            //当前用户点赞过哪些一键发布
                            $point = model('Point')->field('pub_id')
                                                   ->where(['uid'=>$uid])
                                                   ->select()
                                                   ->toArray();
                            if (!empty($point)) {
                                 $point_arr = array_column($point, 'pub_id');
                                if (in_array($v['id'],$point_arr)) {
                                    $v['is_point'] = 1;
                                }else{
                                    $v['is_point'] = 0;
                                }
                            }
                        } 
                        $match_final['publish'] = $publish_list;
                }

                //执行查询人脉
                $through_list = $tr->where(rtrim($match_label,' or '))
                                   ->limit($offset,$length)
                                   ->select()
                                   ->toArray();
                if (!empty($through_list)) {
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
                   $match_final['person'] = $through_list;
                }
                if (empty($match_final)) {
                    $this->code = 214;
                    $this->msg = '抱歉没有发现的相关内容';
                    return $this->result('',$this->code,$this->msg);
               }
               return $this->result($match_final);
    }
    
    /**
     * 首页-搜索-关键字搜索(包含模糊查询)
     */
    public function getRequireLikeKeyword($keyword = '',$page = 1,$uid = 0,$length = 0,$offset = 0)
    {   
        if($keyword === ''){
            $this->code = 213;
            $this->msg = '搜索关键字不能为空';
            return $this->result('',$this->code,$this->msg);
        }

        $match_final            = [];//存放最终匹配的所有项目
        $match_project          = '';//精选项目匹配的所有条件
        $match_project_require  = '';//发布项目匹配的所有条件
        $match_orgainze         = '';//精选机构匹配的所有条件
        $match_orgainze_require = '';//发布机构匹配的所有条件
        $match_publish          = '';//发现匹配的所有条件
        $match_label            = '';//人脉匹配的所有条件
        
        $now = time();
        
        //1.平台精选项目
        $project = model('Project')->alias('p')
                         ->join('Dict d','p.inc_top_sign=d.id','left')
                         ->join('Dict d2','p.inc_sign=d2.id','left')
                         ->join('Dict d3','p.inc_top_industry=d3.id','left')
                         ->join('MaterialLibrary ml','p.top_img=ml.ml_id','left')
                         ->field([
                            'p.pro_id',
                            'ml.url'        =>'img',
                            'p.pro_name'    =>'name',
                            'p.company_name'=>'pro_name',
                            'concat_ws(",",d.value,d2.value,d3.value)'=>'label',
                            'p.introduction'=>'des',
                            'p.view_num',
                            'p.type',
                        ])
                        ->where(['p.status' => 2 , 'p.flag' => 1]);
        //2.用户发布项目
        $pr = $this->alias('pr')
                        ->join('Dict d','pr.top_dict_id=d.id','left')
                        ->join('Dict d2','pr.dict_id=d2.id','left')
                        ->join('Dict d3','pr.top_industry_id=d3.id','left')
                        ->join('Member m','pr.uid=m.uid')
                        ->field([
                            'pr.pr_id'=>'pro_id',
                            'm.userPhoto'=>'img',
                            'm.realName'=>'name',
                            'pr.name'=>'pro_name',
                            'concat_ws(",",d.value,d2.value,d3.value)'=>'label',
                            'pr.business_des'=>'des',
                            'pr.view_num',
                            'pr.type',  
                        ])
                        ->where("pr.is_show =1 and pr.status =1 and pr.deadline >=$now"); 
                        
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
                            'concat(o.scale_min,"-",o.scale_max," ",d3.value)'=>'des',
                            'o.view_num',
                            'o.type',
                        ])
                       ->group('o.org_id')
                       ->where(['o.status'=>2, 'o.flag'=>1 ]);
        
        //4.用户发布资金
        $organize_require = model('OrganizeRequire');
        $sign_map['dt.sign'] = array('in','service_type,industry');
        $or = $organize_require->alias('or')
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
                               ->where($sign_map)
                               ->where("or.is_show = 1 and or.status = 1 and or.deadline>= $now and ord.dict_fid=0");
        
        //5.发现
        $publish = model('Publish');
        $pb = $publish->alias('pb')
                      ->join('Member m','pb.uid=m.uid')
                      ->field([
                        'pb.id',
                        'm.uid',
                        'm.userPhoto',
                        'm.realName',
                        'm.company_jc',
                        'm.position',
                        'pb.content',
                        'pb.img_id',
                        'pb.create_time',
                        'pb.point_num',
                        'pb.comment_num',
                        'pb.type',
                      ])
                      ->order('pb.rank desc,pb.id desc')
                      ->where(['pb.is_del'=>2,'pb.status'=>1]);

        //6.人脉
        $through = model('Through');
        $tr = $through->alias('t')
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
                       
                //I.筛选项目(精选/用户发布)---匹配条件:1.业务/行业标签,2.简称,3.全称
                //II.筛选资金(精选/用户发布)---匹配条件:1.业务/行业标签,2.简称,3.全称
                //III.筛选发现---匹配条件:1.业务/行业标签,2.用户名,3.个性标签
                //IIII.筛选人脉---匹配条件:1.业务/行业标签,2.用户名,3.个性标签
                
                //获取数据字典service_type,industry类型的标签id
                $where  = "dt.sign = 'service_type' or dt.sign = 'industry'";
                //查询dict表中模糊匹配到的service_type,industry标签的dict_id
                $dict   = model('Dict');
             
                $dict_column = $dict->alias('d')
                             ->join('DictType dt','d.dt_id = dt.dt_id')
                             ->field('d.id')
                             ->where($where) 
                             ->where('d.value like "%'.$keyword.'%"')
                             ->select()
                             ->toArray();
 
                //如果根据关键字匹配到service_type,industry偏好标签的dict_id
                if(!empty($dict_column)){
                   $dict_id =implode(',', array_column($dict_column,'id'));
                   
                   //组装精选项目service_type,industry偏好标签条件
                   $match_project .= 'p.inc_top_sign in ('.$dict_id.') or p.inc_sign in ('.$dict_id.') or p.inc_top_industry in ('.$dict_id.') or p.inc_industry in ('.$dict_id.') or ';
                   //组装发布项目service_type,industry偏好标签条件
                   $match_project_require .= 'pr.top_dict_id in ('.$dict_id.') or pr.dict_id in ('.$dict_id.') or pr.top_industry_id in ('.$dict_id.') or pr.top_industry_id in ('.$dict_id.') or ';

                   //根据service_type,industry标签dict_id匹配到精选机构org_id
                   $od = model('OrganizeDict');
                   $od_re = $od->Distinct(true)
                               ->field('org_id')
                               ->where('id in ('.$dict_id.')')
                               ->select()
                               ->toArray();
                   if(!empty($od_re)){
                      $org_id_column = implode(',',array_column($od_re,'org_id'));
                      //组装匹配到精选service_type,industry偏好标签条件 的org_id
                      $match_orgainze .= 'o.org_id in ('.$org_id_column.') or ';
                   }
                   
                   //根据service_type,industry标签dict_id匹配到发布机构or_id
                   $ord = model('OrganizeRequireDict');
                   $ord_re = $ord->Distinct(true)
                                 ->field('or_id')
                                 ->where('dict_id in ('.$dict_id.')')
                                 ->where(['is_del'=>2])
                                 ->select()
                                 ->toArray();
                    
                   if(!empty($ord_re)){
                      $or_id_column = implode(',',array_column($ord_re,'or_id'));

                      //组装匹配到发布资金service_type,industry偏好标签条件 的or_id
                      $match_orgainze_require .= 'or.or_id in ('.$or_id_column.') or ';

                   }

                  

                   //根据service_type,industry标签dict_id匹配到人脉uid
                   $lab = model('DictLabel');
                   $lab_re = $lab->Distinct(true)
                                 ->field('uid')
                                 ->where('dict_id in ('.$dict_id.')')
                                 ->where(['is_del'=>2])
                                 ->select()
                                 ->toArray();
                    if(!empty($lab_re)){

                       $uid_colum = implode(',',array_column($lab_re,'uid'));
                       //组装有service_type,industry标签dict_id 的合伙人uid集合
                       $match_label .= 't.uid in ('.$uid_colum.') or '; 
                    }

                }

                //根据模糊关键词匹配tags_publish表中的个性标签,返回对应的发布id
                $tp = model('TagsPublish');
                $pub_id = $tp->field('pub_id')
                             ->where('tag_name like "%'.$keyword.'%"')
                             ->select()
                             ->toArray();

                if(!empty($pub_id)){
                    //组装有个性标签的发布id集合
                    $match_publish .= 'pb.id in ('.implode(',',array_column($pub_id,'pub_id')).') or ';

                }
                //根据模糊关键词匹配tags_user表中的个性标签,返回对应的用户uid
                $tu = model('TagsUser');
                $uid_arr = $tu->field('uid')
                          ->where('tag_name like "%'.$keyword.'%"')
                          ->select()
                          ->toArray();

                if(!empty($uid_arr)){
                    //组装有个性标签的合伙人uid集合
                    $match_label .= 't.uid in ('.implode(',',array_column($uid_arr,'uid')).') or ';
                }

                //组装精选项目service_type,industry偏好标签&&简称&&全称条件
                $match_project .= 'p.pro_name like "%'.$keyword.'%" or p.company_name like "%'.$keyword.'%" or ';

                //组装发布项目service_type,industry偏好标签&&简称条件
                $match_project_require .= 'pr.name like "%'.$keyword.'%" or ';

                //组装精选机构service_type,industry偏好标签&&简称&&全称条件
                $match_orgainze .= 'o.org_short_name like "%'.$keyword.'%" or o.org_name like "%'.$keyword.'%" or ';

                //组装发布机构service_type,industry偏好标签&&简称条件
                $match_orgainze_require .= 'or.name like "%'.$keyword.'%" or ';
                // dump($match_orgainze_require);die;
                //组装发现service_type,industry偏好标签&&用户名&&个性标签条件
                $match_publish .= 'm.realName like "%'.$keyword.'%" or ';
                //组装人脉service_type,industry偏好标签&&用户名&&个性标签条件
                $match_label .= 't.realName like "%'.$keyword.'%" or '; 
                
                //创建查询用户发布项目的sql语句
                $project_require_list = $pr->where(rtrim($match_project_require,' or '))
                                           ->buildSql();                 
                //执行合并查询平台精选项目&&用户发布项目
                $project_list = $project->where(rtrim($match_project,' or '))
                                        ->union($project_require_list,true)
                                        ->select(false);
                //返回排序且分页后的合并的精选/用户发布项目
                $pro_sql = "($project_list)";
                $all_project = Db::table($pro_sql.' as a')->field([
                    'a.pro_id',
                    'a.img',
                    'a.name',
                    'a.pro_name',
                    'a.label',
                    'a.des',
                    'a.view_num',
                    'a.type',
                  ]) 
                  ->order('a.pro_id desc')
                  ->limit($offset, $length)
                  ->select();
                  
                
                   if(!empty($all_project)){
                        foreach ($all_project as &$each_project) {
                          if (!empty($each_project['label'])) {
                              $each_project['label'] = explode(',', $each_project['label']);
                              if($each_project['type']=="project"){
                                  $each_project['img']   = config('view_replace_str')['__DOMAIN__'].$each_project['img'];
                              }
                          } else {
                              $each_project['label'] = [];
                          }
                           
                       }
                       $match_final['project'] = $all_project;
                   }
                   
                //创建查询用户发布机构的sql语句
                $organize_require_list = $or->where(rtrim($match_orgainze_require,' or '))
                                            ->buildSql();
                
                //执行合并查询平台机构&&用户发布机构
                $organize_list = $o->where(rtrim($match_orgainze,' or '))
                                   ->union($organize_require_list,true)
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

                if (!empty($all_organize)) {
                    foreach ($all_organize as &$each_organize) {
                         $label = explode(',', $each_organize['tag']);
                         if (count($label)>3) {
                             $label = array_slice($label,0,3);
                         }
                         $each_organize['tag'] = $label; 
                         if ($each_organize['type'] == 'organize') {
                            $each_organize['des'] = '管理规模:'.$each_organize['des'].'元';
                            $each_organize['img_url'] = config('view_replace_str')['__DOMAIN__'].$each_organize['img_url'];
                        } else {
                           $each_organize['des'] = '简介:'.$each_organize['des'];
                        }
                    }

                    $match_final['organize'] = $all_organize;
                }

                //执行查询发现
                $publish_list = $pb->where(rtrim($match_publish,' or '))
                                   ->limit($offset,$length)
                                   ->select()
                                   ->toArray();
                
                if (!empty($publish_list)) {
                        //处理是否关注,图片,点赞,评论,个性标签展示
                        //当前用户是否关注查询的发现
                        //查询当前用户关注的合伙人(未取关)
                        $to_uid_arr = [];
                        $to_uid = model('FollowUser')->field('to_uid')
                                                 ->where(['from_uid'=>$uid,'is_del'=>2])
                                                 ->select()
                                                 ->toArray();
                      
                        if (!empty($to_uid)) {
                            $to_uid_arr= array_column($to_uid, 'to_uid');
                        }

                        //图片
                        $ml = model('MaterialLibrary');
                        //添加个性标签的发现
                        $my = model('TagsPublish');
                        
                        foreach ($publish_list as &$v) {

                            //是否关注
                            if (in_array($v['uid'], $to_uid_arr)) {
                                $v['is_follow'] = '已关注';
                            }else{
                                $v['is_follow'] = '关注';
                            }
                            $v['img_url'] = [];
                            //是否添加了图片
                            if ($v['img_id']) {
                               $url = $ml->field('url')->where('ml_id in ('.$v['img_id'].')')->select()->toArray();
                               if (!empty($url)) {
                                    $img_url = array_column($url, 'url');
                                    foreach ($img_url as &$val) {
                                        $val = config('view_replace_str')['__DOMAIN__'].$val;
                                    }
                                    $v['img_url'] = $img_url;

                               }
                            }

                            //个性标签名
                            $my_tag = [];

         
                            //是否添加个性标签
                            $tag = $my->field('tag_name')
                                      ->where(['pub_id'=>$v['id']])
                                      ->select()
                                      ->toArray();
                            if (!empty($tag)) {
                                $my_tag =  array_column($tag,'tag_name');
                            }
                            
                            $v['all_tag'] = $my_tag;
                            $v['create_time'] = friend_date(strtotime($v['create_time']));
                            //当前用户点赞过哪些一键发布
                            $point = model('Point')->field('pub_id')
                                                   ->where(['uid'=>$uid])
                                                   ->select()
                                                   ->toArray();
                            if (!empty($point)) {
                                 $point_arr = array_column($point, 'pub_id');
                                if (in_array($v['id'],$point_arr)) {
                                    $v['is_point'] = 1;
                                }else{
                                    $v['is_point'] = 0;
                                }
                            }
                        } 

                   $match_final['publish'] = $publish_list;
                }
                //执行查询人脉
                $through_list = $tr->where(rtrim($match_label,' or '))
                                   ->limit($offset,$length)
                                   ->select()
                                   ->toArray();
                
                if (!empty($through_list)) {
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
                        $match_final['person'] = $through_list;
                }
                
               
        
                if (empty($match_final)) {
                    $this->code = 214;
                    $this->msg = '抱歉没有发现的相关内容';
                    return $this->result('',$this->code,$this->msg);
                }
                return $this->result($match_final);
        
    }
}