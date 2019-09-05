<?php 
namespace app\console\model;

use think\Model;
use sys\Wechat;
class OrganizeDict extends Model
{

    public function createOrganizeDict($id, $data)
    {

        if (empty($data)) {
            return false;
        }
        if (!is_numeric($id)){
            return false;
        }
        if (!stripos($data, ',')) {
            $data = [$data];
        } else {
            $data = explode(',', $data);
        }

        $dicts = $this->field('id')->where('org_id = '.$id)->select();
        if(!empty($dicts)){
            //删除数据
            $arrData = [];
            foreach ($dicts as $value) {
                if(!in_array($value['id'],$data)){
                    $arrData[] = $value['id'];
                }
            }
            if(!empty($arrData)){
                $strDel = implode(',', $arrData);
                $maps["id"] = array("in",$strDel);
                $re = $this->where($maps)->delete();
                if(!$re){
                    return false;
                }
            }
        }else{
            $inserts = [];
            // 添加数据
            foreach($data as $v) {

                $tmp = [];
                $tmp['org_id'] = $id;
                $tmp['id'] = $v;
                $inserts[] = $tmp;

            }

            $re = $this->saveAll($inserts);
            if($re){
                return true;
            }else{
                return false;
            }
        }

        $dicts = $this->field('id')->where('org_id = '.$id)->select();
        if(!empty($dicts)){
            $column = [];
            foreach ($dicts as $val){
                $column[] = $val->toArray();
            }
            $column = array_column($column,'id');

            $inserts = [];
            // 添加数据
            foreach($data as $v) {
                if(!in_array($v,$column)){
                    $tmp = [];
                    $tmp['org_id'] = $id;
                    $tmp['id'] = $v;
                    $inserts[] = $tmp;
                }
            }
            if (!empty($inserts)) {
                $re = $this->saveAll($inserts);
                if($re){
                    return true;
                }else{
                    return false;
                }
            }
            return true;

        }else{
            $inserts = [];
            // 添加数据
            foreach($data as $v) {

                $tmp = [];
                $tmp['org_id'] = $id;
                $tmp['id'] = $v;
                $inserts[] = $tmp;

            }

            $re = $this->saveAll($inserts);
            if($re){
                return true;
            }else{
                return false;
            }
        }

    }

}