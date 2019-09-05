<?php 
namespace app\home\model;
class Point extends BaseModel
{
	public function pointPublishApi($uid=0,$id=0)
	{
       if (!is_numeric($id) || !$id || !is_numeric($uid) || !$uid) {
       	  $this->code = 223;
       	  $this->msg = '参数非法';
       	  return $this->result('',$this->code,$this->msg);
       }
        $data = [];
        $data['pub_id'] = $id;
        $data['uid'] = $uid; 
       //查询是否是第一次点赞
       $re = $this->field('id,is_del')->where(['uid'=>$uid,'pub_id'=>$id])->find();
       if (empty($re)) {
       	 //第一次
       	  $this->startTrans();
	       try {
	       	      	 
	       	 $this->allowField(true)->save($data);
	       	 model('Publish')->where(['id'=>$data['pub_id']])->setInc('point_num');
	       	 $this->commit();
	       	 $this->code = 200;
	       	 $this->msg = '点赞成功';
	       	 return $this->result('',$this->code,$this->msg);
	       } catch (\Exception $e) {
	       	 $this->rollback();
	       	 $this->code = 224;
	       	 $this->msg = '点赞失败';
	       	 return $this->result('',$this->code,$this->msg);
	       }

       }else{
       	 //第二次
       	  //查看该发布的状态
       	  if ($re['is_del'] ==1) {
       	  	//未点赞
                  $this->startTrans();
       	  	try {
       	  		$this->where(['id'=>$re['id']])->update(['is_del'=>2]);
       	  		model('Publish')->where(['id'=>$data['pub_id']])->setInc('point_num');
	       	    $this->commit();
	       	    $this->code = 200;
	       	    $this->msg = '点赞成功';
	       	    return $this->result('',$this->code,$this->msg);
       	  	} catch (\Exception $e) {
       	  		$this->rollback();
		       	$this->code = 224;
		       	$this->msg = '点赞失败';
		       	return $this->result('',$this->code,$this->msg);
       	  	}
       	  }
       	  if ($re['is_del'] ==2) {
       	  	//已点赞
                  $this->startTrans();
                  try {
       	  		$this->where(['id'=>$re['id']])->update(['is_del'=>1]);
       	  		model('Publish')->where(['id'=>$data['pub_id']])->setDec('point_num');
	       	    $this->commit();
	       	    $this->code = 200;
	       	    $this->msg = '取消点赞成功';
	       	    return $this->result('',$this->code,$this->msg);
       	  	} catch (\Exception $e) {
       	  		$this->rollback();
		       	$this->code = 224;
		       	$this->msg = '取消点赞失败 '.$e->getMessage();
		       	return $this->result('',$this->code,$this->msg);
       	  	}  
       	  }
             
       	  
       }
       
	}
}