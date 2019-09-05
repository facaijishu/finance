<?php

namespace app\home\validate;

use think\Validate;

class OrganizeRequire extends Validate
{   
	//根据发布项目不同的一二级业务组合,有不同的字段验证
    public function __construct($top_dict_value = '')
    {   
        $sp_rule = [
               ["name","require|max:20","请输入名称|名称不能超过20个字符"],
               ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
            ];
        if ($top_dict_value !== '配资业务') {
            $sp_rule[] = ['invest_scale','require|check_invest_scale:1','请输入投资规模'];            
        } else {
           $sp_rule[] = ['invest_scale','require|check_invest_scale2:1','请输入资金规模'];  
        }
        
        $sp_rule[] = ['fund_type','require','请输入资金类型'];
        $sp_rule[] = ['invest_mode','require','请输入投资方式'];
        $sp_rule[] = ['invest_phase','check_invest_phase:1'];
        if ($top_dict_value !== '配资业务') {
            $sp_rule[] = ['invest_target','require|check_invest_target:1','请输入投资标准'];            
        } else {
           $sp_rule[] = ['invest_target','require|check_invest_target2:1','请输入配资标准'];  
        }
          $this->rule = $sp_rule;
          $this->scene = ['save'=>'name,business_des,invest_scale,fund_type,invest_mode,invest_phase,invest_target,'];

           
    }
  
    //自定义验证投资规模(万元)
    protected function check_invest_scale($value)
    {
       if (!preg_match('/^[1-9][0-9]{0,9}(\.[0-9]{1,2})?$/', $value)) {
       	   return '投资规模最多为10位数字,最多保留两位小数';
       }
       return true;
    }
    
     //自定义验证资金规模(万元)
    protected function check_invest_scale2($value)
    {
       if (!preg_match('/^[1-9][0-9]{0,9}(\.[0-9]{1,2})?$/', $value)) {
           return '资金规模最多为10位数字,最多保留两位小数';
       }
       return true;
    }
   //自定义验证投资标准
   protected function check_invest_target($value)
   {     
         $value = trim($value);
         if (empty($value)) {
             return '请输入投资标准';
         } 
         if (mb_strlen($value,'utf8')<10 || mb_strlen($value,'utf8')>1000) {
             return '投资标准字数必须在10~1000个之间';
         } else {
             return true;
         }
         
         
   } 
   //自定义验证配资标准
   protected function check_invest_target2($value)
   {     
         $value = trim($value);
         if (empty($value)) {
             return '请输入配资标准';
         } 
         if (mb_strlen($value,'utf8')<10 || mb_strlen($value,'utf8')>1000) {
             return '配资标准字数必须在10~1000个之间';
         } else {
             return true;
         }
         
         
   } 
   //自定义验证投资阶段
   protected function check_invest_phase($value)
   {
        if (mb_strlen($value,'utf8') > 50) {
             return '投资阶段字数不能超过50个字';
         } else {
             return true;
         }
   }  
}