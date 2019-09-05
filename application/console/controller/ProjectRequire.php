<?php
namespace app\console\controller;
class ProjectRequire extends ConsoleBase
{  
   //项目需求管理列表页样式展示
   public function index()
   {
   	   return view();
   }
   
   //项目需求管理列表页数据展示
   public function read()
   {   
       $start       = $this->request->get('start',0);
       $length      = $this->request->get('length', config('paginate.list_rows'));
   	   $pr_id       = $this->request->get('pr_id',0);
   	   $real_name   = $this->request->get('real_name',0);
       $is_show     = $this->request->get('is_show',0);
       $status      = $this->request->get('status',0);
       
       $projectRequire = model('ProjectRequire');
       $data = $projectRequire->readProjectRequire($start,$length,$pr_id,$real_name,$is_show,$status);
       echo json_encode($data);
   }
   
   //导出项目需求管理到excel
   public function pr_client()
   {
       $pr_id       = $this->request->get('pr_id',0);
       $real_name   = $this->request->get('real_name',0);
       $is_show     = $this->request->get('is_show',0);
       $status      = $this->request->get('status',0);
       $pr          = model('ProjectRequire');
       $data = $pr->excelProjectRequire($pr_id,$real_name,$is_show,$status);
       if(project_require_show($data)){
           return 'ok';
       }
       return 'no';
   }
   //项目需求管理详情
   public function detail()
   {
      $id = input('id');
      $pr = model('ProjectRequire');
      $re = $pr->projectRequireDetail($id);
      $this->assign('data',$re);
      return view();
   }
   //隐藏一条项目需求管理
   public function stop()
   {
      $id = input('id');
      $pr = model('ProjectRequire');
      $re = $pr->stopProjectRequire($id);
      if($re){
         $this->result('',1,'隐藏成功','json');
      } else {
          $error = $model->getError() ? $model->getError() : '隐藏失败';
         $this->result('',0,'隐藏失败','json');
      }
   }
   //展示一条项目需求管理
   public function start()
   {
      $id = input('id');
      $pr = model('ProjectRequire');
      $re = $pr->startProjectRequire($id);
      if($re){
         $this->result('',1,'展示成功','json');
      } else {
          $error = $model->getError() ? $model->getError() : '展示失败';
         $this->result('',0,'展示失败','json');
      }
   }
  //排序
   public function set()
   {   
       $data = [];
       $id = $this->request->post('id/d', 0);
       $list_order = $this->request->post('list_order/d', 0);
       $data['id'] = $id;
       $data['list_order'] = $list_order;
        $model = model("ProjectRequire");
        if($model->setProjectRequireOrderList($data)){
            $data['url'] = url('ProjectRequire/index');
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
        $model = model("ProjectRequire");
        if($model->setProjectRequireTopFalse($id)){
            $data['url'] = url('ProjectRequire/index');
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
        $model = model("ProjectRequire");
        if($model->setProjectRequireTopTrue($id)){
            $data['url'] = url('ProjectRequire/index');
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
        $model = model("ProjectRequire");
        if($model->setProjectRequireCareFalse($id)){
            $data['url'] = url('ProjectRequire/index');
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
        $model = model("ProjectRequire");
        if($model->setProjectRequireCareTrue($id)){
            $data['url'] = url('ProjectRequire/index');
            $this->result($data, 1, '致精成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '致精失败';
            $this->result('', 0, $error, 'json');
        }
   }
   //修改一条发布项目的内部客服id
   public function edit()
   {
       $param = input();
       $pr_id = $param['pr_id'];
       $inner_cs = $param['inner_cs'];
       $model = model('ProjectRequire');
       if ($model->editProjectRequireCs($pr_id,$inner_cs)) {
           $data['url'] = url('ProjectRequire/detail',['id'=>$pr_id]);
            $this->result($data, 1, '修改内部客服id成功', 'json');
       } else {
           $error = $model->getError() ? $model->getError() : '修改内部客服id失败';
            $this->result('', 0, $error, 'json');
       }
       
    } 
}