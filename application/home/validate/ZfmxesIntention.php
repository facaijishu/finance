<?php

namespace app\home\validate;

use think\Validate;

class ZfmxesIntention extends Validate
{
    protected $rule = [
        ['neeq', 'require', '请输入股票代码'],
        ['plannoticeddate', 'require', '请输入发布日期'],
        ['num', 'require', '请输入意向股数'],
        ['phone', 'require', '请输入手机号码'],
    ];
    protected $scene = [
        'add'   => ['neeq','plannoticeddate','num','phone'],
    ];
}