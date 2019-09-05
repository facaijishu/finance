<?php

namespace app\home\validate;

use think\Validate;

class Binding extends Validate
{
    protected $rule = [
        ['phone', 'require|length:11,11', '请输入手机号|手机号不正确'],
    ];

    protected $scene = [
        'add'   => ['phone'],
    ];
}