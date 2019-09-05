<?php

namespace app\console\validate;

use think\Validate;

class ProjectQa extends Validate
{
    protected $rule = [
        'question'    => 'require',
        'answer'    => 'require',
    ];

    protected $message = [
        'question.require'    => '问题为空,请输入问答的问题',
        'answer.require'    => '答案为空,请输入问答的答案',
    ];

    protected $scene = [
        'add'   => ['question' , 'answer'],
        'edit'   => ['question' , 'answer'],
    ];
}