<?php
namespace app\console\validate;

use think\Validate;

class OrganizeRequire extends Validate
{
    protected $rule = [
        'list_order'    => 'require',
        'id'    => 'require',
    ];

    protected $message = [
        'list_order.require'    => '请输入排序号',
        'id.require'    => '缺少发布项目id',
    ];

    protected $scene = [
        'edit'   => ['list_order','id'],
    ];
}