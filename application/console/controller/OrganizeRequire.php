<?php
namespace app\console\controller;
class OrganizeRequire extends ConsoleBase
{  
	//资金需求管理首页
   public function index()
   {
   	  return view();
   }
   //资金需求管理列表页数据展示
   public function read()
   {   
       $start       = $this->request->get('start',0);
       $length      = $this->request->get('length', config('paginate.list_rows'));
   	   $or_id       = $this->request->get('or_id',0);
   	   $real_name   = $this->request->get('real_name',0);
       $is_show     = $this->request->get('is_show',0);
       $status      = $this->request->get('status',0);
       
       $organizeRequire = model('OrganizeRequire');
       $data = $organizeRequire->readOrganizeRequire($start,$length,$or_id,$real_name,$is_show,$status);
       echo json_encode($data);
   }
   
   
    //导出资金需求管理到excel
    public function or_client()
    {
        $or_id      = $this->request->get('or_id',0);
        $real_name  = $this->request->get('real_name',0);
        $is_show    = $this->request->get('is_show',0);
        $status     = $this->request->get('status',0);
        
        $or         = model('OrganizeRequire');
        $data       = $or->excelOrganizeRequire($or_id,$real_name,$is_show,$status);
        if(organize_require_show($data)){
            return 'ok';
        }
        return 'no';
    }
   //隐藏一条资金需求
   public function stop()
   {
      $id = input('id');
      $pr = model('OrganizeRequire');
      $re = $pr->stopOrganizeRequire($id);
      if($re){
         $this->result('',1,'隐藏成功','json');
      } else {
          $error = $model->getError() ? $model->getError() : '隐藏失败';
         $this->result('',0,'隐藏失败','json');
      }
   }
   //展示一条资金需求
   public function start()
   {
      $id = input('id');
      $or = model('OrganizeRequire');
      $re = $or->startOrganizeRequire($id);
      if($re){
         $this->result('',1,'展开成功','json');
      } else {
          $error = $model->getError() ? $model->getError() : '展开失败';
         $this->result('',0,'展开失败','json');
      }
   }
   //资金需求详情页
   public function detail()
   {
      $id = input('id');
      $or = model('OrganizeRequire');
      $re = $or->organizeRequireDetail($id);
      $this->assign('data',$re);
      return view();
   }
   //排序
   public function set()
   {   
       $data = [];
       $id = $this->request->post('id/d', 0);
       $list_order = $this->request->post('list_order/d', 0);
       $data['id'] = $id;
       $data['list_order'] = $list_order;
        $model = model("OrganizeRequire");
        if($model->setOrganizeRequireOrderList($data)){
            $data['url'] = url('OrganizeRequire/index');
            $this->result($data, 1, '设置排序成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '设置排序失败';
            $this->result('', 0, $error, 'json');
        }
    
   }
   //取消置顶
   public function stick_false()
   {
      $id = $this->request->param('id/d');
        $model = model("OrganizeRequire");
        if($model->setOrganizeRequireTopFalse($id)){
            $data['url'] = url('OrganizeRequire/index');
            $this->result($data, 1, '取消置顶成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '取消置顶失败';
            $this->result('', 0, $error, 'json');
        }
   }
   //置顶
   public function stick_true()
   {
        $id = $this->request->param('id/d');
        $model = model("OrganizeRequire");
        if($model->setOrganizeRequireTopTrue($id)){
            $data['url'] = url('OrganizeRequire/index');
            $this->result($data, 1, '置顶成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '置顶失败';
            $this->result('', 0, $error, 'json');
        }
   }
   //取消致精
   public function care_false()
   {
      $id = $this->request->param('id/d');
        $model = model("OrganizeRequire");
        if($model->setOrganizeRequireCareFalse($id)){
            $data['url'] = url('OrganizeRequire/index');
            $this->result($data, 1, '取消致精成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '取消致精失败';
            $this->result('', 0, $error, 'json');
        }
   }
   //致精
   public function care_true()
   {
      $id = $this->request->param('id/d');
        $model = model("OrganizeRequire");
        if($model->setOrganizeRequireCareTrue($id)){
            $data['url'] = url('OrganizeRequire/index');
            $this->result($data, 1, '致精成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '致精失败';
            $this->result('', 0, $error, 'json');
        }
   }
   //修改一条发布资金的内部客服id
   public function edit()
   {
       $param = input();
       $or_id = $param['or_id'];
       $inner_cs = $param['inner_cs'];
       $model = model('OrganizeRequire');
       if ($model->editOrganizeRequireCs($or_id,$inner_cs)) {
           $data['url'] = url('OrganizeRequire/detail',['id'=>$or_id]);
            $this->result($data, 1, '修改内部客服id成功', 'json');
       } else {
           $error = $model->getError() ? $model->getError() : '修改内部客服id失败';
            $this->result('', 0, $error, 'json');
       }
       
    } 
}