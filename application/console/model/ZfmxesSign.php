<?php

namespace app\console\model;

use think\Model;

class ZfmxesSign extends Model
{
    public function getZfmxesSignByCodeAndDate($code , $plannoticeddate){
        $info = $this->where(['neeq' => $code , 'plannoticeddate' => $plannoticeddate])->find();
        return $info;
    }

    public function createZfmxesSign($data){
        if(empty($data)){
            $this->error = '缺少相关签约数据';
            return false;
        }
        //验证数据信息
        $validate = validate('ZfmxesSign');
        $scene = isset($data['id']) ? 'edit' : 'add';
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $user = \app\console\service\User::getInstance()->getInfo();
            $info = $this->getZfmxesSignByCodeAndDate($data['neeq'], $data['plannoticeddate']);
            if(empty($info)){
                $arr = [];
                $arr['neeq'] = $data['neeq'];
                $arr['plannoticeddate'] = $data['plannoticeddate'];
                $arr['sign'] = $data['sign'];
                $arr['content'] = $data['content'];
                $arr['create_time'] = time();
                $arr['create_uid'] = $user['uid'];
                $this->allowField(true)->save($arr);
            } else {
                $arr = [];
                $arr['sign'] = $data['sign'];
                $arr['content'] = $data['content'];
                $arr['update_time'] = time();
                $arr['update_uid'] = $user['uid'];
                $this->allowField(true)->where(['neeq' => $data['neeq'] , 'plannoticeddate' => $data['plannoticeddate']])->update($arr);
            }
            model("ZfmxesAdd")->where(['neeq' => $data['neeq'] , 'plannoticeddate' => $data['plannoticeddate']])->update(['zfmx_sign' => 1]);
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