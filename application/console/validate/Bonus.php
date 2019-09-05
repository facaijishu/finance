<?php

namespace app\console\validate;

use think\Validate;

class Bonus extends Validate
{
    protected $rule = [
        'time'    => 'require',
        'total_amount'    => 'require',
        'amount'    => 'require',
    ];

    protected $message = [
        'time.require'    => '请选择日期',
        'total_amount.require'    => '请输入平台成交总金额',
        'amount.require'    => '请输入可分红金额',
    ];

    protected $scene = [
        'add'   => ['time','total_amount','amount'],
        'edit'  => ['time','total_amount','amount'],
    ];
}