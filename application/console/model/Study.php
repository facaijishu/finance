<?php

namespace app\console\model;

use think\Model;

class Study extends Model
{
    public function addStudyUrl($data){
        if(empty($data)){
            $this->error = '数据不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $config = ['type' => 'mysql','hostname' => '106.15.177.121' , 'database' => 'wei_read' , 'username' => 'root' , 'password' => 'root'];
            $re = db("ims_dg_article_author", $config)->where(['openid' => $data['openId']])->select();
            $res = $re[0]['id'];
            $this->where(['fs_id' => $data['fs_id']])->update(['status' => 1,'url'=>'http://read.jrfacai.com/web/setarticle.php?key='.$res]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    public function dealStudy($id){
        if(empty($id)){
            $this->error = '该申请id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $openid = $this->where(['fs_id'=>$id])->find();
            $config = ['type' => 'mysql','hostname' => '106.15.177.121' , 'database' => 'wei_read' , 'username' => 'root' , 'password' => 'root'];
            $re = db("ims_dg_article_author", $config)->where(['openid' => $openid->toArray()['openId']])->select();
            $res = $re[0]['id'];
            $this->where(['fs_id' => $id])->update(['status' => 1,'url'=>'http://read.jrfacai.com/web/setarticle.php?key='.$res]);
            $res = $this->where(['fs_id'=>$id])->find();
            $userinfo = db('member')->where(['openId'=>$res['openId']])->find();
            $config = ['type' => 'mysql','hostname' => '106.15.177.121' , 'database' => 'wei_read' , 'username' => 'root' , 'password' => 'root'];
            $study = db("ims_dg_article_author", $config)->where(['openid' => $userinfo['openId']])->find();
            if(empty($study)){
                $this->error = '该用户还未设置成作者';
                return false;
            } else {
                $array = [
                    'first' => ['value' => '您好，您的教师资料审核通过，已为您开通教师功能。' , 'color' => '#173177'],
                    'keyword1' => ['value' => '学习中心作者' , 'color' => '#0D0D0D'],
                    'keyword2' => ['value' => $study['id'] , 'color' => '#0D0D0D'],
                    'keyword3' => ['value' => date('Y-m-d H:i:s' , time()) , 'color' => '#0D0D0D'],
                    'remark' => ['value' => '请点击详情进行内容添加!' , 'color' => '#173177'],
                ];
                $end = sendTemplateMessage(config("study_examine"), $userinfo['openId'], 'http://read.jrfacai.com/web/setarticle.php?key='.$study['id'], $array);
                if(!$end){
                    $this->error = '模板消息发送失败';
                    return false;
                }
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