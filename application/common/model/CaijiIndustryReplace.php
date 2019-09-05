<?php 
namespace app\common\model;
use think\Model;
class CaijiIndustryReplace extends Model
{
	public function industryReplace($xyfl)
	{   
		if(empty($xyfl)){
			$this->error = '参数不能为空';
			return false;
		}

		$cir = $this->where(['old_industry' =>$xyfl])->find();
        if(!$cir){
            $this->error = '旧所属行业不存在';
            return false;
        }
        $dict = model('dict')->where(['id'=>$cir['new_industry']])->find();
        if(!$dict){
            $this->error = '新所属行业不存在';
            return false;
        }
        return $dict;
	}
}