<?php

namespace app\home\model;

use think\Model;

class Binding extends Model
{
    public function createBinding($data){
        if(empty($data)){
            $this->error = '绑定数据不存在';
            return false;
        }
        
        //验证数据信息
        $validate = validate('Binding');
        $scene = 'add';
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }

        //开启事务
        $this->startTrans();
        try{
            $user = session('FINANCE_USER');
            $uid = $user['uid'];
            model("Member")->where(['uid' => $uid])->update(['userPhone' => $data['phone'] , 'userType' => 1]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    public function sendProjectInfo($name,$phone,$time,$info,$openId,$id){
        //开启事务
        $this->startTrans();
        try{
            //客户负责人：客服姓名，联系方式：客户手机，跟进时间：资料最后更新时间；客户备注：绑定用户或金融合伙人
            $array = [
                'first' => ['value' => '您好，您有一个新客户!' , 'color' => '#173177'],
                'keyword1' => ['value' => $name , 'color' => '#0D0D0D'],
                'keyword2' => ['value' => $phone , 'color' => '#0D0D0D'],
                'keyword3' => ['value' => $time, 'color' => '#0D0D0D'],
                'keyword4' => ['value' => $info, 'color' => '#0D0D0D'],
                //'remark' => ['value' => '请点击查看详情链接至大宗详情!' , 'color' => '#173177'],
            ];
            $end = sendTemplateMessage(config("kehu_progress"), $openId, '', $array);
            if(!$end){
                $this->error .= $id;
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