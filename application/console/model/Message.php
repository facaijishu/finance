<?php

namespace app\console\model;

use think\Model;

class Message extends Model
{
    public function getMessageInfoById($id){
        if(empty($id)){
            $this->error = '该留言id不存在';
            return false;
        }
        $info = $this->where(['m_id' => $id])->find();
        $pro = model("project")->getProjectInfoById($info['pro_id']);
        $info['pro_name'] = $pro['pro_name'];
        if($info['type'] == 'member'){
            $member = model("Member")->getMemberInfoById($info['create_uid']);
            $info['create_uid'] = $member['userName'];
        } else {
            $user = model("User")->getUserById($info['create_uid']);
            $info['create_uid'] = $user['real_name'];
        }
        $list = $this->where(['pid' => $id])->select();
        foreach ($list as $key => $value) {
            $list[$key]['pro_name'] = $pro['pro_name'];
            $user = model("User")->getUserById($value['create_uid']);
            $list[$key]['create_user'] = $user['real_name'];
        }
        $info['list'] = $list;
        return $info;
    }
    public function createMessage($data){
        if(empty($data)){
            $this->error = '备注数据不存在';
            return false;
        }
        //验证数据信息
        $validate = validate('Message');
        $scene = isset($data['m_id']) ? 'edit' : 'add';
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }

        //开启事务
        $this->startTrans();
        try{
            $info = \app\console\service\User::getInstance()->getInfo();
            $m_info = $this->where(['m_id' => $data['id']])->find();
            $arr = [];
            $arr['pro_id'] = $m_info['pro_id'];
            $arr['pid'] = $data['id'];
            $arr['type'] = 'admin';
            $arr['content'] = $data['content'];
            $arr['create_time'] = time();
            $arr['last_time'] = time();
            $arr['create_uid'] = $info['uid'];
            $arr['status'] = 1;
            $result = $this->allowField(true)->save($arr);
            if($result){
                $this->where(['m_id' => $data['id']])->update(['last_time' => time()]);
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