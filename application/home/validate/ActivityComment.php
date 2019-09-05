<?php

namespace app\home\validate;

use think\Validate;

class ActivityComment extends Validate
{
    protected $rule = [
        ['id', 'require', '请输入活动id'],
        ['pid', 'require', '请输入父级id'],
        ['content', 'require', '请输入评论内容'],
    ];

    protected $scene = [
        'add'   => ['id','pid','content'],
    ];
}