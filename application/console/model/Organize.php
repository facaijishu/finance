<?php
/**
 * Created by PhpStorm.
 * User: Cao Ming
 * Date: 2018/8/16
 * Time: 11:23
 */

namespace app\console\model;

use think\Model;
use sys\Wechat;

class Organize extends Model
{
    public function getOrganizeInfoById($id)
    {
        if(empty($id)){
            $this->error = '该机构id不存在';
            return false;
        }
        $result = $this->where(['org_id' => $id])->find();
        return $result;
    }

    public function setOrganizeStatus($id , $status)
    {
        if(empty($id)){
            $this->error = '该机构id不存在';
            return false;
        }
        if(!is_numeric($status)){
            $this->error = '机构状态不为整数';
            return false;
        }
        $data = [];
        $data['status'] = $status;
        $result = $this->where(['org_id' => $id])->update($data);
        return $result;
    }

    public function setOrganizeOrderList($data)
    {
        if(empty($data)){
            $this->error = '缺少相关数据';
            return false;
        }
        //验证数据信息
        $validate = validate('OrganizeHot');
        $scene = isset($data['id']) ? 'edit' : 'add';
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['org_id' => $data['id']])->update(['list_order' => $data['list_order']]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }

    public function createOrganizeDraft($data)
    {
        if(empty($data)){
            $this->error = '机构信息不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $data['org_name'] = trim($data['org_name']);
            $data['org_short_name'] = trim($data['org_short_name']);
            $data['customer_service_id'] = trim($data['customer_service_id']);
            $data['scale_min'] = trim($data['scale_min']);
            $data['scale_max'] = trim($data['scale_max']);
            $data['unit'] = trim($data['unit']);
            if(!empty($data['inc_target'])){
                $data['inc_target'] = trim($data['inc_target'],'-');
            }else{
                $data['inc_target'] = '';
            }

            $data['inc_industry'] = trim($data['inc_industry'],',');
            $data['inc_area'] = trim($data['inc_area'],',');
            $data['inc_funds'] = trim($data['inc_funds'],',');
            $data['inc_capital'] = trim($data['inc_capital'],',');
            $data['contacts'] = trim($data['contacts'],',');
            $data['position'] = trim($data['position'],',');
            $data['contact_tel'] = trim($data['contact_tel'],',');
            $data['top_img'] = trim($data['top_img']);
            $data['list_order'] = trim($data['list_order']);
            if(isset($data['org_id'])){
                $data['last_time'] = time();
                $data['status'] = 1;
                $this->allowField(true)->save($data ,['org_id' => $data['org_id']]);
               
            } else {
                $info = \app\console\service\User::getInstance()->getInfo();
                $system = model("SystemConfig")->getSystemConfigInfo();
                $data['create_time'] = time();
                $data['last_time'] = time();
                $data['create_uid'] = $info['uid'];
                $data['status'] = 1;
                $this->allowField(true)->save($data);
                $id = $this->getLastInsID();


                $result = createOrgEditQrcodeDirection($system['current'] , 'organize' , $id , '' , '');




                if(!$result){
                    $this->error = '创建二维码指向失败';
                    return false;
                } else {
                    $this->where(['org_id' => $id])->update(['qr_code' => $result]);
                }
            }
             //提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {

            // 回滚事务
            $this->rollback();
            return false;
        }
    }

    public function createOrganize($data)
    {
        if(empty($data)){
            $this->error = '缺少相关数据';
            return false;
        }
        //验证数据信息
        $validate = validate('Organize');
        $scene = isset($data['org_id']) ? 'edit' : 'add';
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }

        //开启事务
        $this->startTrans();
        try{
            $arr=[];
            $arr['org_name'] = trim($data['org_name']);
            $arr['org_short_name'] = trim($data['org_short_name']);
            $arr['customer_service_id'] = trim($data['customer_service_id']);
            $arr['scale_min'] = trim($data['scale_min']);
            $arr['scale_max'] = trim($data['scale_max']);
            $arr['unit'] = trim($data['unit']);
            if(!empty($data['inc_target'])){
                $arr['inc_target'] = trim($data['inc_target'],'-');
            }else{
                $arr['inc_target'] = '';
            }
            $arr['inc_industry'] = trim($data['inc_industry'],',');
            $arr['inc_area'] = trim($data['inc_area'],',');
            $arr['inc_funds'] = trim($data['inc_funds'],',');
            $arr['inc_capital'] = trim($data['inc_capital'],',');
            $arr['contacts'] = trim($data['contacts']);
            $arr['position'] = trim($data['position']);
            $arr['contact_tel'] = trim($data['contact_tel']);
            $arr['top_img'] = trim($data['top_img']);
            $arr['list_order'] = trim($data['list_order']);
            if(isset($data['org_id'])){
                $arr['last_time'] = time();
                $arr['status'] = 2;
                $arr['flag'] = 1;
                $this->allowField(true)->save($arr , ['org_id' => $data['org_id']]);
                 //修改多对多表
               $re = model('OrganizeDict')->createOrganizeDict( $data['org_id'], $arr['inc_industry']);
               if(!$re){
                  return false;
               }

            } else {
                $info = \app\console\service\User::getInstance()->getInfo();
                $system = model("SystemConfig")->getSystemConfigInfo();
                $arr['create_time'] = time();
                $arr['last_time'] = time();
                $arr['create_uid'] = $info['uid'];
                $arr['status'] = 2;
                $arr['flag'] = 1;
                $this->allowField(true)->save($arr);
                $id = $this->getLastInsID();
                // 添加多对多表
                $re = model('OrganizeDict')->createOrganizeDict( $id, $arr['inc_industry']);
                if(!$re){
                    return false;
                }
               $result = createOrgEditQrcodeDirection($system['current'] , 'organize' , $id , '' , '');
               if(!$result){
                   $this->error = '创建二维码指向失败';
                   return false;
               } else {
                   $this->where(['org_id' => $id])->update(['qr_code' => $result]);
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

    public function stopOrganize($id)
    {
        //开启事务
        $this->startTrans();
        try{
            $this->where(['org_id' => $id])->update(['flag' => 2]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    public function startOrganize($id)
    {
        //开启事务
        $this->startTrans();
        try{
            $data = $this->where(['org_id' => $id])->find();
            if($data['status'] == 1){
                $re = $this->createOrganize($data);
                if(!$re){
                   return null;
                }
            }
            $this->where(['org_id' => $id])->update(['status'=>2,'flag' => 1]);
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