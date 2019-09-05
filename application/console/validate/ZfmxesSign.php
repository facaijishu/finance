<?php

namespace app\console\validate;

use think\Validate;

class ZfmxesSign extends Validate
{
    protected $rule = [
        'content'    => 'require',
    ];

    protected $message = [
        'content.require'    => '请输入签约备注内容',
    ];

    protected $scene = [
        'add'   => ['content'],
        'edit'   => ['content'],
    ];
}