<?php
namespace app\console\validate;

use think\Validate;

class Publish extends Validate
{
    protected $rule = [
        'list_order'    => 'require',
        'id'    => 'require',
    ];

    protected $message = [
        'list_order.require'    => '请输入排序号',
        'id.require'    => '缺少一键发布id',
    ];

    protected $scene = [
        'edit'   => ['list_order','id'],
    ];
}