<?php

namespace app\console\model;

use think\Model;

class Dividend extends Model
{
    public function getDividendInfoById($id){
        $result = $this->where(['di_id' => $id])->find();
        return $result;
    }
    public function accountDividend($data){
        if(empty($data)){
            $this->error = '核算数据不存在';
            return false;
        }
        //验证数据信息
        $validate = validate('Dividend');
        $scene = 'edit';
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            
            $info = $this->where(['di_id' => $data['di_id']])->find();
            $arr = [];
            $arr['total_money'] = floatval($info['jifen']*$data['ratio']);
            $arr['ratio'] = $data['ratio'];
            $arr['accounting'] = 1;
            $this->allowField(true)->where(['di_id' => $data['di_id']])->update($arr);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    public function grantDividend($id){
        //开启事务
        $this->startTrans();
        try{
            $info = $this->where(['di_id' => $id])->find();
            $grant = json_decode($info['content'],true);
            $model = model("Member");
            foreach ($grant as $key => $value) {
                $money = floatval(floatval($value['sum'])*floatval($info['ratio']));
                $member = $model->getMemberInfoById($value['uid']);
                $arr = [];
                if($member['last_month'] == '' || $info['month'] > $member['last_month']){
                    $arr['last_month'] = $info['month'];
                    $arr['last_dividend'] = $money;
                }
                $arr['balance'] = floatval($member['balance'])+floatval($money);
                $model->where(['uid' => $value['uid']])->update($arr);
                //奖金发放通知
//                $userinfo = model("Member")->where(['openId' => $result['openid']])->find();
                $array = [
                    'first'    => ['value' => '您好，本月分红已发放!' , 'color' => '#173177'],
                    'keyword1' => ['value' => $member['realName'] , 'color' => '#0D0D0D'],
                    'keyword2' => ['value' => '认证合伙人' , 'color' => '#0D0D0D'],
                    'keyword3' => ['value' => $money.'元' , 'color' => '#0D0D0D'],
                    'keyword4' => ['value' => date('Y-m-d H:i:s' , time()) , 'color' => '#0D0D0D'],
                    'remark'   => ['value' => '感谢您的付出，奖金已自动结算至您的可提现余额中' , 'color' => '#173177'],
                ];
                $end = sendTemplateMessage(config("company_play_money"), $member['openId'], $_SERVER['SERVER_NAME'].'/home/my_center/index', $array);
                if(!$end){
                    $this->error = '模板消息发送失败';
                    return false;
                }
            }
            $this->where(['di_id' => $id])->update(['success' => 1]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }

    public function createMonthData($arr , $key , $flag = false){
        //开启事务
        $this->startTrans();
        try{
            $start_time = strtotime($arr[$key].'-01 00:00:00');
            $end_time   = strtotime('+1 month' , $start_time);
            $result     = model("IntegralRecord")->alias("ir")
                                                ->join("Member m","ir.openId=m.openId")
                                                ->field("sum(integral) sum,ir.uid")
                                                ->where('ir.through','gt',$arr[$key].'-01 00:00:00')
                                                ->where('ir.through','lt', date('Y-m-d H:i:s' ,$end_time))
                                                ->where('m.userType','eq',2)
                                                ->group("ir.uid")
                                                ->select();
            $peoples = count($result);
            $jifen   = 0;
            foreach ($result as $key1 => $value) {
                $jifen += intval($value['sum']);
            }
            
            if($flag){
                $data = [];
                $data['month']          = $arr[$key];
                $data['jifen']          = $jifen;
                $data['peoples']        = $peoples;
                $data['content']        = json_encode($result);
                $data['create_time']    = time();
                $data['update_time']    = time();
                $this->allowField(true)->save($data);
            } else {
                $data = [];
                $data['jifen']          = $jifen;
                $data['peoples']        = $peoples;
                $data['content']        = json_encode($result);
                $data['update_time']    = time();
                $this->allowField(true)->where(['month' => $arr[$key]])->update($data);
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