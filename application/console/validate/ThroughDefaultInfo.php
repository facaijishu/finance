<?php

namespace app\console\validate;

use think\Validate;

class ThroughDefaultInfo extends Validate
{
    protected $rule = [
        'reason'    => 'require',
        'uid'    => 'require',
    ];

    protected $message = [
        'reason.require'    => '拒绝理由为空,请输入',
        'uid.require'    => '拒绝对象为空,请输入',
    ];

    protected $scene = [
        'add'   => ['reason' , 'uid'],
    ];
}