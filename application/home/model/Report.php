<?php 
namespace app\home\model;

class Report extends BaseModel
{
	
	public function reportPublishApi($pub_id = 0,$uid = 0,$report = '')
	{
       if (empty($pub_id)) {
       	 $this->code = 226;
       	 $this->msg = '缺少发现id参数';
       	 return $this->result('',$this->code,$this->msg);
       }

       if (empty($uid)) {
       	 $this->code = 208;
       	 $this->msg = '缺少用户id参数';
       	 return $this->result('',$this->code,$this->msg);
       }

       if (empty($report)) {
       	 $this->code = 227;
       	 $this->msg = '缺少反馈信息参数';
       	 return $this->result('',$this->code,$this->msg);
       }
       $this->startTrans();
       try {

       	 $this->allowField(true)->save(['uid'=>$uid,'pub_id'=>$pub_id]);
       	 if (strpos($report,',') !== false) {
       	 	//如果选中多个反馈
       	 	 $report_arr= explode(',', $report);
       	 	foreach ($report_arr as $v) {
       	 		if ($v == 1) {
       	 			//内容虚假数量+1
       	 			model('Publish')->where(['id'=>$pub_id])->setInc('content_false_num');
       	 		}
       	 		if ($v == 2) {
       	 			//身份虚假数量+1
       	 			model('Publish')->where(['id'=>$pub_id])->setInc('id_false_num');
       	 		}
       	 		if ($v == 3) {
       	 			//诈骗造谣数量+1
       	 			model('Publish')->where(['id'=>$pub_id])->setInc('fraud_rumor_num');
       	 		}
       	 		if ($v == 4) {
       	 			//恶语相加数量+1
       	 			model('Publish')->where(['id'=>$pub_id])->setInc('bad_language_num');
       	 		}
       	 		if ($v == 5) {
       	 			//不诚信数量+1
       	 			model('Publish')->where(['id'=>$pub_id])->setInc('dishonesty_num');
       	 		}
       	 	}
       	 } else {
       	 	//如果只选中一个
       	 	if ($report == 1) {
   	 			//内容虚假数量+1
   	 			model('Publish')->where(['id'=>$pub_id])->setInc('content_false_num');
       	 	}
   	 		if ($report == 2) {
   	 			//身份虚假数量+1
   	 			model('Publish')->where(['id'=>$pub_id])->setInc('id_false_num');
   	 		}
   	 		if ($report == 3) {
   	 			//诈骗造谣数量+1
   	 			model('Publish')->where(['id'=>$pub_id])->setInc('fraud_rumor_num');
   	 		}
   	 		if ($report == 4) {
   	 			//恶语相加数量+1
   	 			model('Publish')->where(['id'=>$pub_id])->setInc('bad_language_num');
   	 		}
   	 		if ($report == 5) {
   	 			//不诚信数量+1
   	 			model('Publish')->where(['id'=>$pub_id])->setInc('dishonesty_num');
   	 		}
       	 }
         //同时将当前投诉的一键发布的状态更改为已删除
         
       	 $this->commit();
       	 $this->code = 200;
       	 $this->msg = '反馈成功';
       	 return $this->result('',$this->code,$this->msg);
       } catch (\Exception $e) {
       	 $this->rollback();
       	 $this->code = 228;
       	 $this->msg = '反馈失败';
       	 return $this->result('',$this->code,$this->msg);
       }
    }   
}