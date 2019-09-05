<?php

namespace app\console\validate;

use think\Validate;

class Dict extends Validate
{
    protected $rule = [
        'type'    => 'require',
        'value'    => 'require',
    ];

    protected $message = [
        'type.require'    => '请选择数据类型',
        'value.require'    => '请输入数据名称',
    ];

    protected $scene = [
        'add'   => ['type','value'],
        'edit'  => ['type','value'],
    ];
}