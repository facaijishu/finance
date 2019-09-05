<?php

namespace app\console\validate;

use think\Validate;

class Bonus1 extends Validate
{
    protected $rule = [
        'content'    => 'require',
//        'time'    => 'require',
//        'total_amount'    => 'require',
//        'amount'    => 'require',
    ];

    protected $message = [
        'content.require'    => '请填写公告内容',
//        'time.require'    => '请选择日期',
//        'total_amount.require'    => '请输入共分红',
//        'amount.require'    => '请输入最高获得',
    ];

    protected $scene = [
        'add'   => ['content'],
        'edit'  => ['content'],
    ];
}