<?php

namespace app\console\validate;

use think\Validate;

class Spoiler extends Validate
{
    protected $rule = [
//        'title'    => 'require',
        'content'    => 'require',
    ];

    protected $message = [
//        'title.require'    => '标题为空,请输入',
        'content.require'    => '正文为空,请输入',
    ];

    protected $scene = [
        'add'   => [ 'content'],
        'edit'   => [ 'content'],
    ];
}