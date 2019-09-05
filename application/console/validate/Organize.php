<?php
/**
 * Created by PhpStorm.
 * User: Cao Ming
 * Date: 2018/8/18
 * Time: 10:26
 */

namespace app\console\validate;
use think\Validate;

class Organize extends Validate
{
    protected $rule = [
        ['org_name', 'require', '请输入机构名称'],
        ['customer_service_id', 'require', '请输入内部客服id'],
        ['scale_min', 'require', '请输入管理规模最小值'],
        ['scale_max', 'require', '请输入管理规模最大值'],
        ['inc_industry', 'require', '请选择投资方向'],
        ['inc_area', 'require', '请选择所属地域'],
        ['inc_funds', 'require', '请选择资金性质'],
        ['inc_capital', 'require', '请选择资本类型'],
        ['contacts', 'require', '请输入机构联系人'],
        ['position', 'require', '请输入职位'],
        ['contact_tel', 'require', '请输入联系方式'],
        ['top_img','>:0','请上传头像'],
        ['list_order','require','请输入排序']
        
    ];

    protected $scene = [
        'add'   => ['org_name','customer_service_id','scale','inc_industry','inc_area','inc_funds','inc_capital','contacts','position','contact_tel','top_img','list_order'],
        'edit'   => ['org_name','customer_service_id','scale','inc_industry','inc_area','inc_funds','inc_capital','contacts','position','contact_tel','top_img','list_order'],
    ];
}