<?php

namespace app\home\validate;

use think\Validate;

class PresentRecord extends Validate
{
    protected $rule = [
        ['fee', 'require', '请输入提现金额'],
    ];

    protected $scene = [
        'add'   => ['fee'],
    ];
}