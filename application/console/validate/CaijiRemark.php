<?php

namespace app\console\validate;

use think\Validate;

class CaijiRemark extends Validate
{
    protected $rule = [
        'content'    => 'require',
    ];

    protected $message = [
        'content.require'    => '请输入备注内容',
    ];

    protected $scene = [
        'add'   => ['content'],
    ];
}