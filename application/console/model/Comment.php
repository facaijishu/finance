<?php 
namespace app\console\model;
use think\Model;
/**
 * 评论表模型
 */
class Comment extends Model
{
	//读取评论列表数据
	public function readCommentData($start = 0,$length = 20,$pub_id = '')
	{   
		$return = [];
		// if (!$resource) {
		// 	$this->error = '评论来源不能为空';
		// 	return false;
		// }
		if (!$pub_id) {
            $this->error = '评论来源id不能为空';
            return false;
		}
		$this->alias('c')
		     ->join('Member m','c.uid = m.uid')
		     ->field('SQL_CALC_FOUND_ROWS c.cid,c.fid,c.content,c.pub_id,c.create_time,c.is_del,c.status,m.realName')
		     ->where(['c.pub_id' => $pub_id])
         ->where('c.fid=0');
        $list = $this->limit($start,$length)->select();
        $result = $this->query("SELECT FOUND_ROWS() as count");
        $total = $result[0]['count'];
        $return['data'] = $list;
        $return['recordsFiltered'] = $total;
        $return['recordsTotal'] = $total;
        return $return;     
	}
	//隐藏一条评论
	public function stopComment($id = '')
	{
		if(!$id){
    		$this->error = '评论id不存在';
    		return false;
    	}
        $this->startTrans();
        try {
        	$data['status'] = 2;
        	$this->where(['id' => $id])->update($data);
        	$this->commit();
        	return true;
        } catch (\Exception $e) {
        	$this->rollback();
        	return false;
        }
	}
	//显示一条评论
	public function startComment($id = '')
	{
		if(!$id){
    		$this->error = '评论id不存在';
    		return false;
    	}
        $this->startTrans();
        try {
        	$data['status'] = 1;
        	$this->where(['id' => $id])->update($data);
        	$this->commit();
        	return true;
        } catch (\Exception $e) {
        	$this->rollback();
        	return false;
        }
	}
	//查看一条评论的详情(回复列表页)
	public function commentDetail($start = 0,$length = 20,$fid = '')
	{
        if (!$fid){
        	$this->error = '评论id不存在';
        	return false;
        }
        $return = [];
        $this->alias('c')
             ->join('Member m','c.uid=m.uid')
             ->field('SQL_CALC_FOUND_ROWS c.*,m.realName')
             ->where(['c.fid' => $fid]);
        $data = $this->limit($start,$length)->select();
        $result = $this->query('SELECT FOUND_ROWS() as count');
        $total = $result[0]['count'];
        $return['data']= $data;
        $return['recordsFiltered'] = $total;
        $return['recordsTotal'] = $total;
        return $return;
	}
    //隐藏一条回复
    public function stopReply($id='')
    {
       if (!$id) {
          $this->error = '回复id不存在';
          return false;
       }
       $this->startTrans();
       try{
           $data['status'] = 2;
           $this->where(['cid' => $id])->update($data);
           $this->commit();
           return true;
       }catch(\Exception $e){
           $this->rollback();
           return false;
       }
    }
    //展示一条回复
    public function startReply($id='')
    {
       if (!$id) {
          $this->error = '回复id不存在';
          return false;
       }
       $this->startTrans();
       try{
           $data['status'] = 1;
           $this->where(['cid' => $id])->update($data);
           $this->commit();
           return true;
       }catch(\Exception $e){
           $this->rollback();
           return false;
       }
    }
}