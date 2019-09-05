<?php

namespace app\console\validate;

use think\Validate;

class DictType extends Validate
{
    protected $rule = [
        'name'    => 'require',
    ];

    protected $message = [
        'name.require'    => '请输入数据类型名称',
    ];

    protected $scene = [
        'add'   => ['name'],
        'edit'  => ['name'],
    ];
}