<?php
namespace app\console\controller;
class Publish extends ConsoleBase
{  
   //列表页展示
   public function index()
   {
   	 return view();
   }
  
   //读取一键发布内容展示到列表页
   public function read()
   {  
   	$start = $this->request->get('start',0);
   	$length = $this->request->get('length', config('paginate.list_rows'));
      $uid = $this->request->get('uid','');
      $username = $this->request->get('username','');
      $publish = model('Publish');
      $data = $publish->readPublishData($start,$length,$uid,$username);
      echo json_encode($data);
   }
   //隐藏一条发布
   public function stop()
   {
   	  $id = input('id');
   	  $publish = model('Publish');
   	  $re = $publish->stopPublish($id);
   	  if($re){
         $this->result('',1,'隐藏成功','json');
   	  } else {
   	  	  $error = $model->getError() ? $model->getError() : '隐藏失败';
         $this->result('',0,'隐藏失败','json');
   	  }
   }
   //显示一条发布
   public function start()
   {
   	  $id = input('id');
   	  $publish = model('Publish');
   	  $re = $publish->startPublish($id);
   	  if($re){
         $this->result('',1,'显示成功','json');
   	  } else {
   	  	  $error = $model->getError() ? $model->getError() : '显示失败';
         $this->result('',0,'显示失败','json');
   	  }
   }
   
   //显示一条发布的详情页
   public function detail()
   {  
      $data = input();
   	  $id = isset($data['id'])?$data['id']:0;
      $publish = model('Publish');
      $data = $publish->getPublishById($id);
   	  $this->assign('data',$data);
   	  return view();
   }
   
    //导出一键发布Excel表格
    public function pub_client()
    {
        $uid       = $this->request->get('uid', '');
        $username  = $this->request->get('username', '');
        $publish   = model('Publish');
        $list      = $publish->excelPublish($uid,$username); 
        if(publish_show($list)){
            return "ok";
        }else{
            return "no";
        }
    }
     
   //取消置顶
   public function stick_false()
   {
      $id = $this->request->param('id/d');
        $model = model("Publish");
        if($model->setPublishTopFalse($id)){
            $data['url'] = url('Publish/index');
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
        $model = model("Publish");
        if($model->setPublishTopTrue($id)){
            $data['url'] = url('Publish/index');
            $this->result($data, 1, '置顶成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '置顶失败';
            $this->result('', 0, $error, 'json');
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
        $model = model("Publish");
        if($model->setPublishOrderList($data)){
            $data['url'] = url('Publish/index');
            $this->result($data, 1, '设置排序成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '设置排序失败';
            $this->result('', 0, $error, 'json');
        }
    
   }
   //修改一条一键发布的内部客服id
   public function edit()
   {
       $param = input();
       $publish_id = $param['publish_id'];
       $inner_cs = $param['inner_cs'];
       $model = model('Publish');
       if ($model->editPublishCs($publish_id,$inner_cs)) {
           $data['url'] = url('Publish/detail',['id'=>$publish_id]);
            $this->result($data, 1, '修改内部客服id成功', 'json');
       } else {
           $error = $model->getError() ? $model->getError() : '修改内部客服id失败';
            $this->result('', 0, $error, 'json');
       }
       
   }     
}