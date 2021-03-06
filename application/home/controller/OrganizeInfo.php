<?php 
namespace app\home\controller;
class OrganizeInfo extends Base
{
    //预览机构信息
    public function index()
    {   
    $this->assign('title' , '机构中心 -');
    $this->assign('img' , '');
    $this->assign('des' , '这里有最具体的大宗转让!');
   // $org_id = $this->request->param('id');
    $org_id = input('id');
    if(!is_numeric($org_id)){
        return false;
    } 
    $info = model('Organize')->find($org_id);  
    $wx_uid = '';
    if($info['wx_uid']){
      $wx_uid = $info['wx_uid']; 
    } 
    $this->assign('wx_uid',$wx_uid);     
    $model = model("dict");
        //单位
        $unit = $model->getDictInfoById($info['unit']);
        $info['unit'] = $unit['value'];
        //投资企业+所属行业
        $arr_target = [];
        if(strpos($info['inc_target'], '-')){
            $inc_targets = explode('-', $info['inc_target']);
            foreach ($inc_targets as $key => $value) {
               
               $inc_targets2 = explode('+', $value);
               if(count($inc_targets2) == 2){
                    $arr_target[] = array(
                      'target_company'=>$inc_targets2[0],
                      'target_industry'=>$inc_targets2[1]
                   );
               }
               
                  
            }    
            
        }else{
            if(strpos($info['inc_target'],'+') !== false){
                $inc_targets2 = explode('+', $info['inc_target']);
                if(count($inc_targets2) == 2){
                    $arr_target[] = array(
                      'target_company'=>$inc_targets2[0],
                      'target_industry'=>$inc_targets2[1]
                   );
                }
            }else{

                $arr_target[] = array(
                      'target_company' =>'',
                      'target_industry'=>''
                   );
            }
        }
        $info['inc_target'] = $arr_target;
        //投资方向
        $industry = explode(',', $info['inc_industry']);
        $inc_industry = '';
        foreach ($industry as $key => $value) {
            $dict = $model->getDictInfoById($value);
            $inc_industry .= $dict['value'].'  ';
        }
        $info['inc_industry'] = trim($inc_industry , '  ');
        //所属区域
        $area = explode(',', $info['inc_area']);
        $inc_area = '';
        foreach ($area as $key => $value) {
            $dict = $model->getDictInfoById($value);
            $inc_area .= $dict['value'].' 、 ';
        }
        $info['inc_area'] = trim($inc_area , ' 、');
        //资金性质
        $funds = explode(',', $info['inc_funds']);
        $inc_funds = '';
        foreach ($funds as $key => $value) {
            $dict = $model->getDictInfoById($value);
            $inc_funds .= $dict['value'].'、';
        }
        $info['inc_funds'] = trim($inc_funds , '、');
        //资本类型
        $capital = explode(',', $info['inc_capital']);
        $inc_capital = '';
        foreach ($capital as $key => $value) {
            $dict = $model->getDictInfoById($value);
            $inc_capital .= $dict['value'].' , ';
        }
        $info['inc_capital'] = trim($inc_capital , ' , ');
        $model = model("MaterialLibrary");
        //头像
        $top_img = $model->getMaterialInfoById($info['top_img']);
        $info['top_img'] = $top_img['url'];
        $this->assign('info',$info);  
       return view();

				
     }

     //客服二维码
     public function service()
     {
     	$org_id = $this->request->param('org_id/d');
        $model = model("Organize");
        $info = $model->getOrganizeSimpleInfoById($org_id);
        $this->assign('info' , $info);
        $this->assign('title' , '联系客服 -');
        $this->assign('img' , '');
        $this->assign('des' , '金融合伙人客服很高兴为您服务!');
        return view();
     }
     
     //验证用户提交的手机号和后台录入的是否一致
     public function doVerifyTel()
     {
       
        $data = input();
        $contact_tel = trim($data['tel']);
        $org_id =$data['id'];
        if (!$contact_tel) {
            return false;
        }
        if (!is_numeric($org_id)){
            return false;
        }
        $re = model('Organize')->where(['org_id'=>$org_id,'contact_tel'=>$contact_tel])->find();
        if ($re) {            
          $this->result('' , 1 , '手机号输入正确' , 'json');        
        } else {
            $this->result('' , 0 , '手机号输入错误' , 'json');
        }
     }
     
     //机构认领页面
     public function edit(){
         $this->assign('title' , '机构认领');
         $this->assign('img' , '');
         $this->assign('des' , '机构认领');
         
         $data   = input();
         $id     = intval($data['id']);
         //会员
         $user   = $this->jsGlobal;
         $uid    = $user['member']['uid'];
         
         $mModel = model("Member");
         $mem    = $mModel->getMemberInfoById($uid);
         
         
         $model    = model("Organize");
         $info     = $model->getOrganizeSimpleInfoById($id);
         
         /*if($info['contact_tel'] !== $mem['userPhone']){
          $url   = "/home/organize/index/id/".$id;
          header("Location: $url");
          }else{
          if($info['is_confirm']==1){
          $url   = "/home/organize/index/id/".$id;
          header("Location: $url");
          }
          }*/
         
         $model_d  = model("Dict");
         $industry = $model_d->getIndustry();
         $stage    = $model_d->getStage();
         $area     = $model_d->getArea();
         $type     = $model_d->getType();
         
         foreach ($industry as $key => $item) {
             foreach ($item['sub'] as $key => $it) {
                 $count = substr_count($info['inc_industry'],$it['id']);
                 if($count==1){
                     $it['checked'] = 1;
                 } else{
                     $it['checked'] = 0;
                 }
             }
             
         }
         
         foreach ($type as $key => $item) {
             $count = substr_count($info['inc_type'],$item['id']);
             if($count==1){
                 $item['checked'] = 1;
             } else{
                 $item['checked'] = 0;
             }
         }
         
         foreach ($stage as $key => $item) {
             $count = substr_count($info['invest_stage'],$item['id']);
             if($count==1){
                 $item['checked'] = 1;
             } else{
                 $item['checked'] = 0;
             }
         }
         
         foreach ($area as $key => $item) {
             $count = substr_count($info['inc_area'],$item['id']);
             if($count==1){
                 $item['checked'] = 1;
             } else{
                 $item['checked'] = 0;
             }
         }
         
         $inc_list  = $model_d->getDictStr($info['inc_industry'],5);
         $this->assign('inc_checkval' , subStrLen($inc_list,12));
         $area_list = $model_d->getDictStr($info['inc_area'],5);
         $this->assign('area_checkval' , subStrLen($area_list,12));
         
         
         $this->assign('industryList' , $industry);
         $this->assign('stageList' , $stage);
         $this->assign('areaList' , $area);
         $this->assign('typeList' , $type);
         $this->assign('info' , $info);
         
         
         $this->assign('title' , $info['org_name']."｜FA財-一站式智能信息投融交互平台");
         return view();
     }
     
     public function doEdit(){
         $data      = [];
         $post      = input();
         $uid       = session('FINANCE_USER.uid');
         $mem       = model("Member")->getMemberInfoById($uid);
         if($post['contact_tel']==$mem['userPhone']){
             $data['org_name']           = $post['org_name'];
             $data['org_short_name']     = $post['org_short_name'];
             $data['invest_stage']       = $post['stage'];
             $data['inc_target']         = $post['inc_target'];
             $data['inc_industry']       = $post['industry'];
             $data['inc_area']           = $post['area'];
             $data['invest_area']        = $post['area'];
             $data['contacts']           = $post['contacts'];
             $data['position']           = $post['position'];
             $data['inc_type']           = $post['type'];
             $data['org_id']             = $post['id'];
             $data['status']             = 2;
             $data['flag']               = 1;
             $data['is_confirm']         = 1;
             $org    = model("Organize");
             $info   = $org->getOrganizeSimpleInfoById($post['id']);
             $edit   = $org->edit($data);
             if($edit==!false){
                 if($info['inc_industry']!=$post['industry']){
                     $res = model("OrganizeDict")->add($post['id'],$post['industry']);
                 }
                 $this->result('' ,'200', '编辑成功' , 'json');
             }else{
                 $this->result('' ,'201', '编辑失败' , 'json');
             }
         }else{
             $this->result('' ,'201', '手机号码的修改请去个人中心操作' , 'json');
         }
         
     }

}
