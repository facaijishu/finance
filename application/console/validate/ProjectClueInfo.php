<?php

namespace app\console\validate;

use think\Validate;

class ProjectClueInfo extends Validate
{
    protected $rule = [
        'content'    => 'require',
        'pc_id'    => 'require',
    ];

    protected $message = [
        'content.require'    => '备注理由为空,请输入',
        'pc_id.require'    => '备注对象为空,请输入',
    ];

    protected $scene = [
        'add'   => ['content' , 'pc_id'],
    ];
}