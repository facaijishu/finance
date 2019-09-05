<?php

namespace app\console\validate;

use think\Validate;

class Message extends Validate
{
    protected $rule = [
        'content'    => 'require',
    ];

    protected $message = [
        'content.require'    => '内容为空,请输入备注的内容',
    ];

    protected $scene = [
        'add'   => ['content'],
        'edit'   => ['content'],
    ];
}