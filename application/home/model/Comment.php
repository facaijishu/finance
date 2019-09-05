<?php 
namespace app\home\model;
/**
 * 
 */
class Comment extends BaseModel
{
	//当前发现的全部评论
	public function showComment($uid = 0,$pub_id = 0)
	{
        if (empty($uid)) {
              $this->code = 208;
              $this->msg = '缺少用户id参数';
              return $this->result('',$this->code,$this->msg);
            }

        if (empty($pub_id)) {
          $this->code = 226;
          $this->msg = '缺少发现id参数';
          return $this->result('',$this->code,$this->msg);
        }
        //全部评论
        $comment = $this->alias('c')
                        ->join('Member m','c.uid=m.uid')
                        ->field('c.cid,c.uid,m.userPhoto,m.realName,m.company_jc,m.position,c.content')
                        ->where(['c.fid'=>0,'c.pub_id'=>$pub_id,'c.is_del'=>1,'c.status'=>1])
                        ->select()
                        ->toArray();
        
        if (!empty($comment)) {
        	foreach ($comment as &$v) {
        		//每条评论是否有回复
        		$v['reply']= $this->alias('co')
        		       ->join('Member m','co.uid=m.uid')
        		       ->field('m.realName,co.content')
        		       ->where(['co.fid'=>$v['cid'],'co.pub_id'=>$pub_id,'co.is_del'=>1,'co.status'=>1])
        		       ->select()
        		       ->toArray();
                //当前发布评论的用户是否被关注
                $fu = model('FollowUser');
                $is_follow = $fu->field('is_del')->where(['from_uid'=>$uid,'to_uid'=>$v['uid']])->find();
                if (empty($is_follow)) {
                	$v['is_follow'] = '关注';
                } else {
                	if ($is_follow['is_del'] == 1) {
                		$v['is_follow'] = '关注';
                	}
                	if ($is_follow['is_del'] == 2) {
                		$v['is_follow'] = '已关注';
                	}
                }
                
        	}
            return $this->result($comment);
        }
        
        $this->code = 241;
        $this->msg = '该发现目前没有评论';
        return $this->result('',$this->code,$this->msg);

	}

    //对当前发现发表评论
    public function addCommentPublishApi($data = [])
    {
        if (empty($data)) {
            $this->code = 201;
            $this->msg = '数据不存在';
            return $this->result('',$this->code,$this->msg);
        }
        if (!is_numeric($data['fid']) || !is_numeric($data['pub_id'])) {
            $this->code = 223;
            $this->msg = '参数非法';
            return $this->result('',$this->code,$this->msg);
        }
        
        if (empty($data['content'])) {
            $this->code = 242;
            $this->msg = '评论内容不能为空';
            return $this->result('',$this->code,$this->msg);
        }
        $this->startTrans();
        try {

            $this->allowField(true)->save($data);
            model('Publish')->where(['id'=>$data['pub_id']])->setInc('comment_num');
            $this->commit();
            $this->code = 200;
            $this->msg = '评论成功';
            return $this->result('',$this->code,$this->msg);
        } catch (\Exception $e) {
            $this->rollback();
            $this->code = 233;;
            $this->msg = '评论失败 '.$e->getMessage();
            return $this->result('',$this->code,$this->msg);
        }
    }
}