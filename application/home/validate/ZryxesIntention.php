<?php

namespace app\home\validate;

use think\Validate;

class ZryxesIntention extends Validate
{
    protected $rule = [
        ['num', 'require', '请输入意向股数'],
        ['phone', 'require', '请输入手机号码'],
    ];
    protected $scene = [
        'add'   => ['num','phone'],
    ];
}