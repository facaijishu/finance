<?php

namespace app\console\validate;

use think\Validate;

class ProjectNews extends Validate
{
    protected $rule = [
        'title'    => 'require',
//        'source'    => 'require',
        'content'    => 'require',
    ];

    protected $message = [
        'title.require'    => '标题为空,请输入新闻的标题',
//        'source.require'    => '来源为空,请输入新闻的来源',
        'content.require'    => '内容为空,请输入新闻的内容',
    ];

    protected $scene = [
        'add'   => ['title'  , 'content'],
        'edit'   => ['title'  , 'content'],
    ];
}