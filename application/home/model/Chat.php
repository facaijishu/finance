<?php 
namespace app\home\model;
class Chat extends BaseModel
{  
	//查询哪些用户给当前登录用户发送过的最后一条消息
   public function getReplyMsg($from_uid = 0)
   {   

        $sql = "select c.from_uid,c.to_uid,m.userPhoto,m.realName,m.company_jc,m.position from (select * from fic_chat where to_uid = $from_uid  order by  is_read,  chat_id desc ) c join fic_member m on c.from_uid = m.uid group by c.from_uid order by c.is_read ,c.chat_id desc ";
        $re  = $this->query($sql);
        if (!empty($re)) {
        	//获取未读消息数量
        	foreach ($re as &$v) {
        	    $total_num       = $this->getNoReadNum($v['from_uid'],$v['to_uid']);
        	    if($total_num>99){
        	        $v['noread_num'] = $total_num."...";
        	    }else{
        	        $v['noread_num'] = $total_num;
        	    }
                
                $data = $this->getLastNoReadMsg($v['from_uid'],$v['to_uid']);
                $time = explode(' ', $data['create_time']);
                
                //$v['content'] = $data['content'];
                //$v['type'] = $data['type'];
                $now = date("Y-m-d",time());
                if($time[0]<$now){
                    $v['day']           = $time[0];
                }else{
                    $v['day']          = substr($time[1], 0, 5);
                }
        	}
        	return $re;
        }
        return $re;
        
   }

   //获取每个聊天对象未读消息数量
   public function getNoReadNum($from_uid = 0,$to_uid = 0)
   {
   	    $count = $this->where(['from_uid' => $from_uid,'to_uid' => $to_uid,'is_read' => 0])
        		      ->count();
        return $count;
   }

   //获取最后一条未读消息
   public function getLastNoReadMsg($from_uid = 0,$to_uid = 0)
   {    
   	    $data['content']        = '';
   	    $data['type']           = '';
   	    $data['create_time']    = '';
   	    $map = "(from_uid = $from_uid and to_uid = $to_uid) or (from_uid = $to_uid and to_uid = $from_uid)";
        $re = $this->field('content,type,create_time')->where($map)
                                          ->order('chat_id desc')
                                          ->find();
        if (!empty($re)) {
        	$data['content']       = $re['content'];
        	$data['type']          = $re['type'];
        	$data['create_time']   = $re['create_time'];
        }
        return $data;
   }
   
   //更新登录用户未读消息总和
   public function getAllNoread($from_uid = 0)
   {
   	    $all_noread = model('Chat')->where(['to_uid'=>$from_uid,'is_read'=>0])
   	                               ->count();   	    
   	    return $all_noread;
   }
   
   
   public function add($data){
       if(empty($data)){
           $this->error = '';
           return false;
       }
       //开启事务
       $this->startTrans();
       try{
           $this->allowField(true)->save($data);
           $id = $this->getLastInsID();
           $this->commit();
           $info = $this->getChatOnce($id);
           return $info;
       } catch (\Exception $e) {
           // 回滚事务
           $this->rollback();
           return false;
       }
   }
   
   /**
    * 获取单跳消息的记录
    * @param 消息编号 $id
    * @return 数据集
    */
   public function getChatOnce($id){
       $result  = model('Chat')->where(" chat_id = ".$id )->find();
       if (!empty($result)) {
           $time = explode(' ', $result['create_time']);
           $result['day'] = $time[0];
           $result['time'] = substr($time[1], 0, 5);
       }
       
       return $result;
   }
   
   /**
    * 获取固定聊天编号范围之间的记录
    * @param unknown $from_uid
    * @param unknown $to_uid
    * @param 消息编号最小范围  $min
    * @param 消息编号最大范围  $max
    * @param 显示数量 $num
    * @return 数据集
    */
   
   public function getAllById($from_uid ,$to_uid, $min = 0 , $max = 0 , $num = 0){
       if($min && $max){
           $this->error = '暂无次需求';
           return false;
       }elseif($min) {
           $map         = "chat_id < ".$min." and (( from_uid =". $from_uid." and to_uid = ". $to_uid .") or ( from_uid = ". $to_uid ." and to_uid =". $from_uid." ) ) ";
           $result        = $this->where($map)
                               ->order('create_time desc')
                               ->limit($num)
                               ->select()
                               ->toArray();
       }elseif ($max) {
           $map         = "chat_id > ".$max." and (( from_uid =". $from_uid." and to_uid = ". $to_uid .") or ( from_uid = ". $to_uid ." and to_uid =". $from_uid." ) ) ";
           $result        = $this->where($map)
                               ->order('create_time desc')
                               ->limit($num)
                               ->select()
                               ->toArray();
       }
       if(!empty($result)){
           foreach ($result as $key => $value) {
               $time = explode(' ', $value['create_time']);
               $result[$key]['day'] = $time[0];
               $result[$key]['time'] = substr($time[1], 0, 5);
           }
       }
       return $result;
   }

}
