<?php 
namespace app\console\controller;
class Comment extends ConsoleBase
{   
	//读取评论列表数据
	public function read()
	{   
		$start = $this->request->get('start',0);
   	$length = $this->request->get('length', config('paginate.list_rows'));
		$pub_id = $this->request->get('pub_id',0);
		$comment = model('Comment');
		$data = $comment->readCommentData($start,$length,$pub_id);
		echo json_encode($data);

	}
	//隐藏一条评论
	public function stop()
	{
	  $id = input('id');
   	  $comment = model('Comment');
   	  $re = $comment->stopComment($id);
   	  if($re){
         $this->result('',1,'隐藏成功','json');
   	  } else {
   	  	  $error = $model->getError() ? $model->getError() : '隐藏失败';
         $this->result('',0,'隐藏失败','json');
   	  }
	}
	//显示一条评论
   public function start()
   {
   	  $id = input('id');
   	  $comment = model('Comment');
   	  $re = $comment->startComment($id);
   	  if($re){
         $this->result('',1,'显示成功','json');
   	  } else {
   	  	  $error = $model->getError() ? $model->getError() : '显示失败';
         $this->result('',0,'显示失败','json');
   	  }
   }
   //显示一条评论的详情页(回复列表页)
   public function detail()
   {  
   	  $id = input('id');//发布的id
        $pub_id = input('pub_id');
   	  $this->assign('comment_id',$id);
        $this->assign('pub_id',$pub_id);
   	  return view();
   }
   //读取回复列表数据
   public function replyRead()
   {
   	  $start = $this->request->get('start',0);
   	  $length = $this->request->get('length',config('paginate.list_rows'));
   	  $fid = $this->request->get('fid',0);
   	  $comment = model('Comment');
   	  $data = $comment->commentDetail($start,$length,$fid);
   	  echo json_encode($data);
   }
   //隐藏一条回复
   public function replyStop()
   {
   	  $id = input('id');
   	  $comment = model('Comment');
   	  $re = $comment->stopReply($id);
   	  if($re){
         $this->result('',1,'隐藏成功','json');
   	  } else {
   	  	  $error = $model->getError() ? $model->getError() : '隐藏失败';
         $this->result('',0,'隐藏失败','json');
   	  }
   }
   //展示一条回复
   public function replyStart()
   {
   	  $id = input('id');
   	  $comment = model('Comment');
   	  $re = $comment->startReply($id);
   	  if($re){
         $this->result('',1,'展示成功','json');
   	  } else {
   	  	  $error = $model->getError() ? $model->getError() : '展示失败';
         $this->result('',0,'展示失败','json');
   	  }
   }
} 