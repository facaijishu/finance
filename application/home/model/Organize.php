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
        $info               = $this->where(['org_id' => $id])->find();
        $erweima            = db("qrcode_direction")->where(['id' => $info['qr_code']])->find();
        $info['qr_code']    = $erweima['qrcode_path'];
        
        $ml                 = model("material_library");
        $top_img            = $ml->getMaterialInfoById($info['top_img']);
        $info['top_img']    = $top_img['url'];
        
       /*$scs                = model('SelfCustomerService');
        $service            = $scs->getCustomerServiceById($info['customer_service_id']);
        $info['scs_id']     = $service['scs_id'];
        $info['kf_account'] = $service['kf_account'];
        $info['kf_nick']    = $service['kf_nick'];    */
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
        
     
       $list = $this->alias('o')
                    //->join('Dict d','o.unit = d.id')
                    //->join('MaterialLibrary ml','o.top_img=ml.ml_id')
                    ->field([
                        'o.org_id'                            =>'id',
                        'o.org_short_name'                    =>'top',
                        'concat(o.contacts," | ",o.position)' =>'bottom',
                        //'o.inc_industry'                      =>'label',
                        'o.view_num',
                        'o.inc_industry',
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
       }else{
           foreach ($list as &$v) {
               $model      = model("Dict");
               $inc        = $model->getTopDictList($v['inc_industry'] );
               $str_inc = "";
               foreach ($inc as &$w) {
                   if($str_inc==""){
                       $str_inc = $w['dict_name'];
                   }else{
                       $str_inc = $str_inc.",".$w['dict_name'];
                   }
               }
               
               $v['label'] = explode(',', $str_inc);
               
           }
           
           $result_info['code'] = 200;
           $result_info['msg'] = '';
           $result_info['data'] = $list;
           return $result_info;
       }
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
        /*$url = model('MaterialLibrary')->where(['ml_id'=>$detail['top_img']])->find();
        if (empty($url)) {
            $org_info['url'] ='';
        } else {
            $org_info['url'] =$url['url'];
        }*/
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
            $org_info['is_collect']    = '收藏';
        }
        //机构全称
        $org_info['org_name'] = $detail['org_name'];
 
        //投资方向
        $model                  = model("Dict");
        $result                 = $model->getSecDictList($detail['inc_industry']);
        $org_info['dict_list']  = $result;
        
        //所属区域
        $area                   = $model->getDictStr($detail['inc_area']);
        $org_info['area']       = $area;
        
        //投资阶段
        $stage                  = $model->getDictStr($detail['invest_stage']);
        $org_info['stage']      = $stage;
        
        //业务类型
        $type                   = $model->getDictStr($detail['inc_type']);
        $org_info['inc_type']   = $type;
        
        //已投项目
        $org_info['inc_target'] = $detail['inc_target'];
       
        //查看人数
        $org_info['view_num']   = $detail['view_num'];
        
        //类型
        $org_info['type']       = $detail['type'];
        
        //分享内容
        //$desc = implode('，', $org_info['inc_industry']);
        $desc = '';
        $org_info['share']['title']  = $org_info['org_short_name'];
        $org_info['share']['desc']   = "寻".$desc;
        //$org_info['share']['imgUrl'] = config('view_replace_str')['__DOMAIN__'].$org_info['url']; 
        $org_info['share']['imgUrl'] = "/static/frontend/images/default.png";
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
    
    //获取简单的机构信息
    public function getOrganizeInfoByTel($tel)
    {
        if(empty($tel)){
            $this->error = '该机构ID不存在';
            return false;
        }
        
        $info = $this->where(['contact_tel' => $tel])->find();
        if(empty($info)){
            $this->error = '该机构ID不存在';
            return false;
        }else{
            return $info;
        }
    }
    
    
    /**
     * 编辑
     * @param unknown $data
     * @return boolean|unknown
     */
    public function edit($data){
        if(empty($data)){
            $this->error = '项目信息不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $data['last_time']      = time();
            
            $this->allowField(true)->save($data,['org_id' => $data['org_id']]);
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