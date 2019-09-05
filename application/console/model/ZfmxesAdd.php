<?php

namespace app\console\model;

use think\Model;

class ZfmxesAdd extends Model
{
    public function getZfmxesAddList(){
        $list = $this->select();
        return $list;
    }

    public function setFinancingById($id){
        $info = $this->where(['id' => $id])->update(['zfmx_financing' => 1]);
        return $info;
    }
    public function getZfmxesAddByCodeAndDate($neeq , $date){
        $info = $this->where(['neeq' => $neeq , 'plannoticeddate' => $date])->find();
        return $info;
    }
//    public function getZfmxesAddInfoById($id){
//        $info = $this->where(['id' => $id])->find();
//        return $info;
//    }
//    public function getZfmxesAddByCodeAndId($id , $code){
//        $info = $this->where(['neeq' => $code , 'id' => $id])->find();
//        return $info;
//    }
    public function createZfmxesAdd($data){
        if(empty($data)){
            $this->error = '缺少相关增发数据';
            return false;
        }
        //验证数据信息
//        $validate = validate('ZfmxesAdd');
//        $scene = isset($data['id']) ? 'edit' : 'add';
//        if(!$validate->scene($scene)->check($data)){
//            $error = $validate->getError();
//            $this->error = $error;
//            return false;
//        }
        //开启事务
        $this->startTrans();
        try{
            $info = $this->getZfmxesAddByCodeAndDate($data['neeq'], $data['plannoticeddate']);
            if(empty($info)){
                $this->error = '该增发明细不存在';
                return false;
            } else {
                $arr = [];
                $arr['raise_money'] = $data['raise_money'];
                $arr['additional_num'] = $data['additional_num'];
                $arr['announcement_date'] = $data['announcement_date'];
                $arr['increase_price'] = $data['increase_price'];
                $arr['profit'] = $data['profit'];
                $arr['cutoff_date'] = $data['cutoff_date'];
                $arr['fundraising_use'] = $data['fundraising_use'];
                $arr['Issue_object'] = $data['Issue_object'];
                $arr['involved'] = $data['involved'];
                $arr['sale_condition'] = $data['sale_condition'];
				$arr['write_lirun'] = $data['write_lirun'];
                $arr['img_id'] = $data['img_id'];
                $this->allowField(true)->where(['neeq' => $data['neeq'] , 'plannoticeddate' => $data['plannoticeddate']])->update($arr);
            }
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