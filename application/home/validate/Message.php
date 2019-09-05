<?php

namespace app\home\validate;

use think\Validate;

class Message extends Validate
{
    protected $rule = [
        ['content', 'require', '请输入留言内容'],
    ];

    protected $scene = [
        'add'   => ['content'],
    ];
}