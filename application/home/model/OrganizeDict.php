<?php 
namespace app\home\model;

// use think\Model;
class OrganizeDict extends BaseModel
{
    /**
     *
     * @param unknown $id
     * @param unknown $data
     */
    public function add($id, $data)
    {
        $dicts = $this->field('id')->where('org_id = '.$id)->select();
        if(!empty($dicts)){
            $maps["id"] = array("in",$data);
            $delete     = $this->where($maps)->delete();
        }
        $str = explode(',', $data);
        foreach($str as $v) {
            $tmp            = [];
            $tmp['org_id']  = $id;
            $tmp['id']      = $v;
            $inserts[]      = $tmp;
        }
        
        $re = $this->saveAll($inserts);
        if($re){
            return true;
        }else{
            return false;
        }
    }
}