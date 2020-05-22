<?php

namespace app\console\model;

use think\Model;

class Order extends Model
{
    /**
     * 支付成功的报名列表
     * @param 活动编号 $id
     * @return 数据集合
     */
    public function getOrderListByActivity($id){
        $result = $this->where(['type' => 'activity' , 'id' => $id , 'is_pay' => 1])->select();
        if(!empty($result)){
            $model = model("Member");
            foreach ($result as $key => $value) {
                $info = $model->getMemberInfoById($value['uid']);
                $result[$key]['img'] = $info['userPhoto'];
            } 
        }
        return $result;
    }
    
    /**
     * 单条记录的报名信息
     * @param 报名编号 $id
     * @return 数据集合
     */
    public function getOrderInfoById($id){
        $result = $this->where(['o_id' => $id])->find();
        return $result;
    }
    
    /**
     * 更新审核结果
     * @param 提交数据集合 $data
     * @return 更新结果
     */
    public function postReview($data){
        //开启事务
        $this->startTrans();
        try{
            $this->where(['o_id' => $data['o_id']])->update(['review' => $data['is_pass'],'note' => $data['note']]);
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