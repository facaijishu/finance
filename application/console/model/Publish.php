<?php 
namespace app\console\model;
use think\Model;
class Publish extends Model 
{   
    protected $resultSetType = 'collection';
	//读取发布列表页数据
	public function readPublishData($start = 0,$length = 20,$uid = '',$username = '')
	{   
		$return = [];
        $this->alias('pb')
             ->join('Member m','pb.uid = m.uid')
             ->field('SQL_CALC_FOUND_ROWS pb.id,pb.uid,pb.create_time,pb.content,pb.rank,pb.is_top,pb.is_del,pb.status,m.realName')
             ->order('pb.id desc');
        if ('' != $uid) {
        	$this->where(['pb.uid' => $uid]);
        }
        if ('' != $username) {
        	$this->where(['m.realName' => $username]);
        }
        $list = $this->limit($start,$length)->select();
        foreach ($list as &$each_list) {
            $content_length = mb_strlen($each_list['content'],'utf-8');
            if ($content_length>30) {
                $each_list['content'] = mb_substr($each_list['content'],0,30,'utf-8').'......';
            }
        }
        $result= $this->query('SELECT FOUND_ROWS() as count');
        $total = $result[0]['count'];
        $return['data'] = $list;
        $return['recordsFiltered'] = $total;
        $return['recordsTotal'] = $total;
        return $return;
	}
	//隐藏一条发布
    public function stopPublish($id='')
    {
    	if(!$id){
    		$this->error = '发布id不存在';
    		return false;
    	}
        $this->startTrans();
        try {
        	$data['status'] = 2;
        	$this->where(['id' => $id])->update($data);
            //同时把该用户的require_num-1
            $user = $this->field('uid')->where(['id' => $id])->find();
            if (!empty($user)) {
                //每隐藏一条发现,member对应的需求数量-1
                model('Member')->where(['uid'=>$user['uid']])->setDec('require_num');
                //同时更新该用户的缓存
                // $info = model('Member')->where(['uid'=>$user['uid']])->find()->toArray();
                // session('FINANCE_USER',$info);
            }
        	$this->commit();
        	return true;
        } catch (\Exception $e) {
        	$this->rollback();
        	return false;
        }

    }
    //显示一条发布
    public function startPublish($id='')
    {
    	if(!$id){
    		$this->error = '发布id不存在';
    		return false;
    	}
        $this->startTrans();
        try {
        	$data['status'] = 1;
        	$this->where(['id' => $id])->update($data);
            //同时把该用户的require_num+1
            $user = $this->field('uid')->where(['id' => $id])->find();
            if (!empty($user)) {
                //每显示一条发现,member对应的需求数量+1
                model('Member')->where(['uid'=>$user['uid']])->setInc('require_num');
                // //同时更新该用户的缓存
                // $info = model('Member')->where(['uid'=>$user['uid']])->find()->toArray();
                // session('FINANCE_USER',$info);
                // dump(session('FINANCE_USER'));die;
            }
        	$this->commit();
        	return true;
        } catch (\Exception $e) {
        	$this->rollback();
        	return false;
        }
    }
    

    /**
     * 读取导出excel的数据
     * @param string $uid
     * @param string $username
     * @return 数据集合
     */
    public function excelPublish($uid = '',$username = '')
    {   
        $this->alias('pb')
        ->join('Member m','pb.uid = m.uid')
        ->field('SQL_CALC_FOUND_ROWS pb.id,pb.uid,m.realName,pb.create_time,pb.content,pb.point_num,pb.id_false_num,pb.fraud_rumor_num,pb.bad_language_num,pb.dishonesty_num,pb.forward_num,pb.content_false_num,pb.forward_num,pb.is_del,pb.status')
        ->order('pb.id desc');
        if ('' != $uid) {
            $this->where(['pb.uid' => $uid]);
        }
        if ('' != $username) {
            $this->where(['m.realName' => $username]);
        }
        $data = $this->select();
        foreach($data as &$v) {
            $tp       = model('TagsPublish');
            $tag_name = $tp->where(['pub_id'=>$v['id'],'is_del'=>2])->field(['tag_name'])->select();
            
            if (empty($tag_name)) {
                $v['label'] = '未添加任何个性标签';
            } else {
                $label = "";
                foreach ($tag_name as &$vv) {
                    $label .= $vv['tag_name'];
                }
                $v['label'] = $label;
            }
        }
       
        return $data;
    }
    
    
    //取消置顶
    public function setPublishTopFalse($id = '')
    {
        if(!$id){
            $this->error = '发布id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['id' => $id])->update(['is_top' => 2]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    //置顶
    public function setPublishTopTrue($id = '')
    {
        if(!$id){
            $this->error = '发布id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['id' => $id])->update(['is_top' => 1]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    //排序
    public function setPublishOrderList($data = [])
    {
        if(empty($data)){
            $this->error = '缺少相关数据';
            return false;
        }
        //验证数据信息
        $validate = validate('Publish');
        $scene = isset($data['id']) ? 'edit' : 'add';
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['id' => $data['id']])->update(['rank' => $data['list_order']]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    //详情页
    public function getPublishById($id = '')
    {
        if(!$id ){
            $this->error = '发布id不存在';
            return false;
        }
        $data = $this->alias('p')
              ->join('Member m','p.uid = m.uid')
              ->field('p.*,m.realName')
              ->where(['p.id' => $id])
              ->find();
        $ml = model('MaterialLibrary');
        $map['ml_id'] = array('in',$data['img_id']);
        $img = $ml->field('url')->where($map)->select();
        $data['img'] = $img;
        $tp = model('TagsPublish');
        $tag_name = $tp->where(['pub_id'=>$id,'is_del'=>2])->field(['tag_name'])->select();
        if (empty($tag_name)) {
            $data['label'] = '未添加任何个性标签';
        } else {
            $label = array_column($tag_name, 'tag_name');
            $label = implode(',', $label);
            $data['label'] = $label;
        }
        return $data;      
    }
    
    //修改内部客服id
    public function editPublishCs($publish_id = 0,$inner_cs = 0)
    {
        if(!$publish_id){
            $this->error = '发布id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['id' => $publish_id])->update(['inner_cs' => $inner_cs]);
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