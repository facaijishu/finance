<?php

namespace app\console\validate;

use think\Validate;

class Dividend extends Validate
{
    protected $rule = [
        'ratio'    => 'require',
        'di_id'    => 'require',
    ];

    protected $message = [
        'ratio.require'    => '积分兑换比率理由为空,请输入',
        'di_id.require'    => '核算对象为空,请输入',
    ];

    protected $scene = [
        'eidt'   => ['ratio' , 'di_id'],
    ];
}