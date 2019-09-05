<?php

namespace app\home\model;

use think\Db;

class Organize extends BaseModel
{   
    //获取简单的机构信息
    public function getOrganizeSimpleInfoById($id)
    {
        if(empty($id)){
            $this->error = '该机构ID不存在';
            return false;
        }
        $info = $this->where(['org_id' => $id])->find();
        $erweima = db("qrcode_direction")->where(['id' => $info['qr_code']])->find();
        $info['qr_code'] = $erweima['qrcode_path'];
        $ml = model("material_library");
        $top_img = $ml->getMaterialInfoById($info['top_img']);
        $info['top_img'] = $top_img['url'];
        $scs = model('SelfCustomerService');
        $service = $scs->getCustomerServiceById($info['customer_service_id']);
        $info['scs_id'] = $service['scs_id'];
        $info['kf_account'] =  $service['kf_account'];
        $info['kf_nick'] =  $service['kf_nick'];    
        return $info;
    }
    //获取分页信息
    public function getOrganizeLimitList($start = 0 , $length = 0)
    {
        $result = $this->where(['status' => 2 , 'flag' => 1])
                ->order('list_order desc, org_id desc')
                ->limit($start,$length)
                ->select();
        return $result;
    }
    
    //修改wx_uid属性
    public function editOrganizeWxuid($org_id,$wx_uid)
    {
        //展示微信头像并确认已经展示
        $re = $this->where(['org_id'=>$org_id])
             ->update(['wx_uid'=>$wx_uid,'show'=>1]);
        //查询原来是否上传了默认头像,如果没有上传,就把top_img字段设为-1
        $info = $this->where(['org_id'=>$org_id])->find();
        if($info['top_img'] == 0){
             $res = $this->where(['org_id'=>$org_id])
                 ->update(['top_img'=>-1]);
            if(!$res){
                return false;
            }
        }
        if($re){
            return true;
        }
        return false;
    }
    /**
     * FA財4.0API
     */
    //获取首页精选机构前几条
    public function getOrganizeLimitListApi($role_type = 0,$uid = 0,$length = 3)
    {   
        $result_info = [];
        if(!$uid){
          $result_info['code'] = 208;
          $result_info['msg'] = '缺少用户id参数';
          $result_info['data'] = ''; 
          return $result_info;
        }

        if(!isset($length) || !is_numeric($length)){
            $result_info['code'] = 207;
            $result_info['msg'] = '数据展示条数参数不正确';
            $result_info['data'] = ''; 
            return $result_info;
        }
        
        //当前用户身份为项目方,匹配对应的精选机构
        //互获取用户匹配的标签
        $dl = model('DictLabel');
        $dl = $dl->getDictLabelApi($uid);
        //用户未添加标签,则展示手动排序最前,最新de状态为(发布并展示)的机构
        if(empty($dl)){
           $list = $this->alias('o')
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
                        ->order('o.list_order desc,o.org_id desc')
                        ->limit($length)
                        ->select();
           if(empty($list)){
                $result_info['code'] = 201;
                $result_info['msg'] = '没有更多数据,请先往后台添加';
                $result_info['data'] = ''; 
                return $result_info;
           }
           $dict = model('Dict');

           foreach ($list as $k => &$v) {
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

                    $result_info['code'] = 211;
                    $result_info['msg'] = '数据字典中不存在该精选机构添加的行业标签';
                    $result_info['data'] = ''; 
                    return $result_info;
                }

                //如果存在,查询当前精选机构的每一个行业标签是否存在对应的一级行业标签,
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

            $result_info['code'] = 200;
            $result_info['msg'] = '';
            $result_info['data'] = $list; 
            return $result_info;

        }
        
        //用户添加标签的情况下(平台机构无业务类型标签)
        $dt = model('DictType'); 
            $industry_type_all = [];//当前用户选择的行业的所有一二级标签
            $industry_type_son_par = [];//当前用户选中行业的二级标签所属的一级标签
            $industry_type_son = [];//当前用户选择的行业所有的2级标签(用来匹配的)

            $size = [];//当前用户选择的投融资规模(用来匹配的)
            $to_province = [];//当前用户选择的所在省份(用来匹配的)
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
            $wan = $this->alias('o')
                        ->join('Dict d','o.unit = d.id')
                        ->field('o.org_id,o.scale_max,d.value as unit_name')
                        ->where(['o.status' => 2 , 'o.flag' => 1])
                        ->select();
            if(empty($wan)){

                 $result_info['code'] = 201;
                 $result_info['msg'] = '数据不存在';
                 $result_info['data'] = ''; 
                return $result_info;
            }

            foreach ($wan as &$v) {
                if($v['unit_name'] == '亿'){
                    $v['scale_max'] = $v['scale_max']*10000;
                    $v['unit_name'] ='万';
                }
            }
            
            $match_str = '';
            $match_org = $this->alias('o')
                              ->join('Dict d','o.unit=d.id')
                              ->join('MaterialLibrary ml','o.top_img=ml.ml_id','left')
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
                              ->where(['o.status' => 2 , 'o.flag' => 1]);

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
                $org_id = $od2->Distinct('true')
                              ->field('org_id')
                              ->where('to_province in ('.$where.')')
                              ->select()
                              ->toArray();
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
            //开始匹配
            $map_org_id['o.org_id'] = array('in',$org_id_column);
            $list = $match_org->where($map_org_id)
                              ->order("o.list_order desc,o.org_id desc")
                              ->limit($length)
                              ->select()
                              ->toArray();
            if(empty($list)){

                $result_info['code'] = 201;
                $result_info['msg'] = '未匹配到符合条件的精选资金';
                $result_info['data'] = ''; 
                return $result_info;
           }
            
            $dict = model('Dict');

           foreach ($list as $k => &$v) {
                if(!$v['label']){

                    $result_info['code'] = 211;
                     $result_info['msg'] = '请先往后台添加精选机构的行业标签';
                     $result_info['data'] = ''; 
                    return $result_info;
                }
                //查询每条精选机构添加的行业标签是否在数据字典中存在    
                $industry_dict = $dict->field('id,fid,value')
                                      ->where('id in('.$v['label'].')')
                                      ->select();
                if(empty($industry_dict)){

                    $result_info['code'] = 211;
                     $result_info['msg'] = '数据字典中不存在该精选机构添加的行业标签';
                     $result_info['data'] = ''; 
                    return $result_info;
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

             $result_info['code'] = 200;
             $result_info['msg'] = '';
             $result_info['data'] = $list; 
            return $result_info;
    } 

    //获取精选资金详情
    public function getOrganizeDetail($data = [])
    {
        $white  = model("whiteUid");
        if($white->getWhite($data['uid'])){
            //查看人数+1
            $this->where(['org_id'=>$data['id'],'status'=>2,'flag'=>1])->setInc('view_num');
        }
        $detail = $this->where(['org_id'=>$data['id'],'status'=>2,'flag'=>1])->find();
        if (empty($detail)) {
            $this->code = 256;
            $this->msg = '精选资金详情不存在';
            return $this->result('',$this->code,$this->msg);
        }
        
        $org_info = [];
        
        $userFollow = model('Member')->where(['uid'=>$data['uid']])->find();
        $org_info['follow_flg']= $userFollow['is_follow'];
        
        //精选资金唯一id
        $org_info['org_id'] = $detail['org_id'];
        //头像
        $url = model('MaterialLibrary')->where(['ml_id'=>$detail['top_img']])->find();
        if (empty($url)) {
            $org_info['url'] ='';
        } else {
            $org_info['url'] =$url['url'];
        }
        //精选资金简称
        $org_info['org_short_name'] = $detail['org_short_name'];
        //机构联系人
        $org_info['contacts']       = $detail['contacts'];
        //职位
        $org_info['position']       = $detail['position'];
        //机构联系人类型
        $org_info['role_type']      = '资金方';
        //当前用户是否收藏
        if (!empty($data['collection_organize'])) {
            $collection_organize = explode(',', $data['collection_organize']);
            if (in_array($org_info['org_id'], $collection_organize)) {
               $org_info['is_collect'] = '已收藏'; 
            } else {
               $org_info['is_collect'] = '收藏';
            }
                
            
        } else {
            $org_info['is_collect'] = '收藏';
        }
        //机构全称
        $org_info['org_name'] = $detail['org_name'];
        //资金类型
        if($detail['inc_capital']){
            $inc_capital_map['id'] = array('in',$detail['inc_capital']);
           $inc_capital = model('Dict')->field('value')->where($inc_capital_map)->select()->toArray();
           if (!empty($inc_capital)) {
               $org_info['inc_capital'] = array_column($inc_capital, 'value');
           } else {
               $org_info['inc_capital'] = [];    
           }
           
        }else{
           $org_info['inc_capital'] = [];
        }
        //管理规模(最大值-最小值,单位)
        $org_info['scale'] = $detail['scale_min'].'-'.$detail['scale_max']; 
        $unit = model('Dict')->field('value')->where(['id'=>$detail['unit']])->find();
        if (!empty($unit)) {
            $org_info['unit'] = $unit['value'].'元';
        } else {
            $org_info['unit'] = '';
        }
          
        //所属区域
        $map_inc_area['id'] = array('in',$detail['inc_area']);
        $area = model('Dict')->field('value')->where($map_inc_area)->select()->toArray();
        if (!empty($area)) {
           $org_info['inc_area'] = array_column($area,'value'); 
        } else {
           $org_info['inc_area'] = [];
        }
        //资金性质
        $map_inc_funds['id'] = array('in',$detail['inc_funds']);
        $inc_funds = model('Dict')->field('value')->where($map_inc_funds)->select()->toArray();
        if (!empty($inc_funds)) {
           $org_info['inc_funds'] = array_column($inc_funds,'value');     
        } else {
           $org_info['inc_funds'] = []; 
        }
        
        //投资方向
        $map_inc_industry['id'] = array('in',$detail['inc_industry']);
        $industry = model('Dict')->field('value')->where($map_inc_industry)->select()->toArray();
        if (!empty($industry)) {
           $org_info['inc_industry'] = array_column($industry,'value'); 
        } else {
           $org_info['inc_industry'] = [];
        }

        //已投项目
        $position = strpos($detail['inc_target'],'+');
        if ($position) {
            
            if (strpos($detail['inc_target'],'-')) {
                
                $target_arr = explode('-',$detail['inc_target']);
                $all_target = [];
                foreach ($target_arr as $value) {
                    $postion2 = strpos($value,'+');
                    $all_target[] = substr($value,0,$postion2);
                }
               $org_info['inc_target'] = $all_target;  
            } else {
               $org_info['inc_target'] = [substr($detail['inc_target'],0,$position)];
            }
            
        }else{
            $org_info['inc_target'] = [];
        }
        //查看人数
        $org_info['view_num'] = $detail['view_num'];
        //类型
        $org_info['type'] = $detail['type'];
        
        //分享内容
        $desc = implode('，', $org_info['inc_industry']);
        $org_info['share']['title']  = $org_info['org_short_name'];
        $org_info['share']['desc']   = "寻".$desc;
        $org_info['share']['imgUrl'] = config('view_replace_str')['__DOMAIN__'].$org_info['url']; 
        
        return $this->result($org_info);     
    }

    //个人中心-我的收藏-资金列表
    public function showMyCollectOrganizeApi($uid = 0,$page = 1,$length = 5)
    {
        if (empty($uid)) {
          $this->code = 208;
          $this->msg  = '缺少当前访问用户参数';
          return $this->result('',$this->code,$this->msg);
        }

        $offset = (intval($page) - 1) * intval($length);


        //查询当期访问用户收藏的平台精选资金/发布资金id
        $p_id = model('Member')->field('collection_organize,collection_organize_require')
                               ->where(['uid'=>$uid])
                               ->find();
                               
        
        //用户未收藏发布项目且未收藏平台精选资金/发布资金id
        if (!$p_id['collection_organize'] && !$p_id['collection_organize_require']) {

            $this->code = 263;
            $this->msg = '未收藏任何类型的资金';
            return $this->result('',$this->code,$this->msg);
        }
        

        //组装当前访问用户收藏的平台精选资金id条件
        if (!$p_id['collection_organize']) {
            $map['o.org_id'] = array('in',"");
        } else {
            $map['o.org_id'] = array('in',$p_id['collection_organize']);
        }
        
        //组装当前访问用户收藏的平台发布资金id条件
        if (!$p_id['collection_organize_require']) {
            $map2['or.or_id'] = array('in',"");
        } else {
            $map2['or.or_id'] = array('in',$p_id['collection_organize_require']);
        }

        //创建查询当前访问用户收藏的发布资金的sql语句
        $collection_organize_require_list = model('OrganizeRequire')->alias('or')
                                                 ->join('Member m','or.uid=m.uid')
                                                 ->join('OrganizeRequireDict ord','or.or_id=ord.or_id')
                                                 ->join('Dict d','ord.dict_id=d.id')
                                                 ->join('DictType dt','ord.dict_type_id=dt.dt_id')
                                                 ->field("
                                                     or.or_id as id,   
                                                     m.userPhoto as img,
                                                     m.realName as name,
                                                     or.name as top_name,
                                                     group_concat(d.value) as label,
                                                     or.business_des as bottom_data,
                                                     or.type,
                                                     or.view_num
                                                    ")
                                                 ->group('or.or_id')
                                                 ->where($map2)
                                                 ->where(['or.is_show'=>1,'or.status'=>1,'ord.is_del'=>2])
                                                 ->where("dt.sign='service_type' or dt.sign='industry'")
                                                 ->buildSql();

        
        //执行合并查询当前访问用户收藏的平台精选资金&&用户发布资金     
         $collection_organize_list = $this->alias('o')
                                          ->join('MaterialLibrary ml','o.top_img=ml.ml_id')
                                          ->join('OrganizeDict od','o.org_id=od.org_id')
                                          ->join('Dict d','od.id=d.id')
                                          ->join('Dict d2','o.unit=d2.id')
                                          ->field("
                                                o.org_id as id,
                                                ml.url as img,
                                                o.org_short_name as name,
                                                concat(o.contacts,'|',o.position) as top_name,
                                                group_concat(d.value) as label,
                                                concat(o.scale_min,'-',o.scale_max,d2.value) as bottom_data,
                                                o.type,
                                                o.view_num
                                            ")
                                          ->group('o.org_id')
                                          ->where($map)
                                          ->where(['o.status'=>2,'o.flag'=>1])
                                          ->union($collection_organize_require_list,true)
                                          ->select(false);
        //返回排序且分页后的合并的所有的收藏的精选/用户发布资金 
        $collect_sql = "($collection_organize_list)";
        $collect_all_organize = Db::table($collect_sql.' as a')->field('a.id,a.img,a.name,a.top_name,a.label,a.bottom_data,a.type,a.view_num') 
                                                               ->order('a.id desc')
                                                               ->limit($offset, $length)
                                                               ->select();
        
                      
        if (empty($collect_all_organize)) {
            $this->code = 201;
            $this->msg = '数据不存在';
            return $this->result('',$this->code,$this->msg);
        }else{
            foreach ($collect_all_organize  as &$value) {
                if($value['type']=="organize"){
                    $value['type_label']        = '精选资金';
                }else{
                    $value['type_label']        = '资金';
                }
                
                $value['label']         = explode(',', $value['label']);
                $value['img']           = config('view_replace_str')['__DOMAIN__'].$value['img'];
                $value['bottom_data']   = "管理规模：".$value['bottom_data'];
            }
            
            $this->code = 200;
            $this->msg  = '';
            $this->data = $collect_all_organize;
            return $this->result($this->data,$this->code,$this->msg);
        }
       
    }
    
}