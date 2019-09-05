<?php 
namespace app\home\model;

class FollowUser extends BaseModel
{
	
	public function followPublisherApi($from_uid = 0,$to_uid = 0)
	{
       if (empty($from_uid)) {
       	  $this->code = 208;
       	  $this->msg = '缺少当前访问用户参数';
       	  return $this->result('',$this->code,$this->msg);
       }
       if (empty($to_uid)) {
       	  $this->code = 226;
       	  $this->msg = '缺少合伙人用户参数';
       	  return $this->result('',$this->code,$this->msg);
       }

       //查询公众号用户之前是否关注过该合伙人
       $re = $this->where(['from_uid'=>$from_uid,'to_uid'=>$to_uid])->find();
       if (empty($re)) {
       	    //第一次关注
       	  	$data['from_uid'] = $from_uid;
	       	$data['to_uid'] = $to_uid;
	       	try {
	       		$this->allowField(true)->save($data);
	       		$this->code = 200;
	       	    $this->msg = '关注该合伙人成功';
	       	    return $this->result('',$this->code,$this->msg);
	       	} catch (\Exception $e) {
	       		$this->allowField(true)->save($data);
	       		$this->code = 230;
	       	    $this->msg = '关注该合伙人失败 '.$e->getMessage();
	       	    return $this->result('',$this->code,$this->msg);
	       	}
	       	
	       	
       } 
       // 非第一次关注/取关       
        //当前是取关状态
        if ($re['is_del'] == 1) {
        	//重新关注
        	try {
        		$this->where(['id'=>$re['id']])->update(['is_del'=>2]);
	       		$this->code = 200;
	       	    $this->msg = '关注该合伙人成功';
	       	    return $this->result('',$this->code,$this->msg);
        	} catch (Exception $e) {
	       		$this->code = 230;
	       	    $this->msg = '关注该合伙人失败 '.$e->getMessage();
	       	    return $this->result('',$this->code,$this->msg);
        	}
        	
        }
        
        //当前是关注状态
        if ($re['is_del'] == 2) {
        	//取消关注
        	try {
        		$this->where(['id'=>$re['id']])->update(['is_del'=>1]);
	       		$this->code = 200;
	       	    $this->msg = '取消关注该合伙人成功';
	       	    return $this->result('',$this->code,$this->msg);
        	} catch (Exception $e) {
	       		$this->code = 230;
	       	    $this->msg = '取消关注该合伙人失败'.$e->getMessage();
	       	    return $this->result('',$this->code,$this->msg);
        	}
        	
        }
	}
    //个人中心-我的关注列表
    public function showMyFollowApi($from_uid=0,$page=1,$num=4)
    {
        if (empty($from_uid)) {
          $this->code = 208;
          $this->msg = '缺少当前访问用户参数';
          return $this->result('',$this->code,$this->msg);
        }
        $offset = (intval($page) - 1) * intval($num);
        $list = $this->alias('fu')
               ->join('Member m','fu.to_uid=m.uid')
               ->join('Through th','fu.to_uid=th.uid')
               ->field('m.uid,m.userPhoto,m.realName,th.company_jc,th.position')
               ->where(['fu.from_uid'=>$from_uid,'fu.is_del'=>2])
               ->limit($offset,intval($num))
               ->select()
               ->toArray();

        if (empty($list)) {
            $this->code = 201;
            $this->msg = '数据不存在';
            return $this->result('',$this->code,$this->msg);
        }
        
        foreach ($list as &$value) {
            $re = $this->where(['from_uid'=>$value['uid'],'to_uid'=>$from_uid])->find();
            if (!empty($re)) {
                $value['is_follow'] = '已互关';
            }else{
                $value['is_follow'] = '已关注';
            }
        }
        return $this->result($list);
    }
    //个人中心-我的粉丝
    public function showMyFansApi($to_uid=0,$page=1,$num=4)
    {
        if (empty($to_uid)) {
          $this->code = 208;
          $this->msg = '缺少当前访问用户参数';
          return $this->result('',$this->code,$this->msg);
        }
        $offset = (intval($page) - 1) * intval($num);
        $list = $this->alias('fu')
               ->join('Member m','fu.from_uid=m.uid')
               ->join('Through th','fu.from_uid=th.uid')
               ->field('m.uid,m.userPhoto,m.realName,th.company_jc,th.position')
               ->where(['fu.to_uid'=>$to_uid,'fu.is_del'=>2])
               ->limit($offset,intval($num))
               ->select()
               ->toArray();

        if (empty($list)) {
            $this->code = 201;
            $this->msg = '数据不存在';
            return $this->result('',$this->code,$this->msg);
        }
        
        foreach ($list as &$value) {
            $re = $this->where(['from_uid'=>$to_uid,'to_uid'=>$value['uid']])->find();
            if (!empty($re)) {
                $value['is_follow'] = '已互关';
            }else{
                $value['is_follow'] = '关注';
            }
        }
        return $this->result($list);
    }
}