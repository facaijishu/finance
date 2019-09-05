<?php

namespace app\home\model;

//use think\Model;

class MaterialLibrary extends BaseModel
{
    public function getMaterialInfoById($id){
        $result = $this->where(['ml_id' => $id])->find();
        return $result;
    }
     //保存上传材料
    //$path 保存路径
    //$object 保存对象
    public function createMaterial($path , $path_cut , $path_compress , $object,$uid){
        if($path == ''){
            $this->error = '保存路径设置出错';
            return false;
        }
        if(empty($object)){
            $this->error = '保存对象不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $info = $object->getInfo();
            $arr = [];
            $arr['type'] = $info['type'];
            $arr['name'] = $info['name'];
            $arr['url'] = $path . DS . $object->getSaveName();
            if($path_cut != ''){
                $arr['url_thumb'] = $path_cut . DS . $object->getSaveName();
            }
            if($path_compress != ''){
                $arr['url_compress'] = $path_compress . DS . $object->getSaveName();
            }
            $arr['filesize'] = $info['size'];
            $arr['create_time'] = time();
            $arr['create_uid'] = $uid;
            $this->allowField(true)->save($arr);
            $id = $this->getLastInsID();
            // 提交事务
            $this->commit();
            return $id;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
}
