<?php

namespace app\home\model;

use think\Model;

class ActivityComment extends Model
{
    public function getActivityCommentListByAcID($id){
        $result = $this->where(['a_id' => $id , 'pid' => 0])->select();
        $model = model("Member");
        foreach ($result as $key => $value) {
            $user = $model->getMemberInfoById($value['uid']);
            $result[$key]['userName'] = $user['userName'];
            $result[$key]['userPhoto'] = $user['userPhoto'];
            $list = $this->where(['a_id' => $id , 'pid' => $value['ac_id']])->select();
            foreach ($list as $key1 => $value1) {
                $user1 = $model->getMemberInfoById($value1['uid']);
                $list[$key1]['userName'] = $user1['userName'];
                $list[$key1]['userPhoto'] = $user1['userPhoto'];
            }
            $result[$key]['list'] = $list;
        }
        return $result;
    }

    public function createActivityComment($data){
        if(empty($data)){
            $this->error = '评论数据不存在';
            return false;
        }
        //验证数据信息
        $validate = validate('ActivityComment');
        $scene = 'add';
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $arr = [];
            $arr['a_id'] = $data['id'];
            $arr['pid'] = $data['pid'];
            $arr['content'] = $data['content'];
            $arr['uid'] = session('FINANCE_USER.uid');
            $arr['create_time'] = time();
            $result = $this->allowField(true)->save($arr);
            if($result){
                $id = $this->getLastInsID();
                $result = $this->alias("ac")
                        ->join("member m","ac.uid=m.uid")
                        ->field("ac.*,m.userName,m.userPhoto")
                        ->where(['ac.ac_id' => $id])
                        ->find();
            }
                // 提交事务
            $this->commit();
            return $result;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
}