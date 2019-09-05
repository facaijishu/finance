<?php

namespace app\home\validate;

use think\Validate;

class SpoilerComments extends Validate
{
    protected $rule = [
        ['sp_id', 'require', '请输入剧透id'],
        ['pid', 'require', '请输入父级id'],
        ['content', 'require', '请输入评论内容'],
    ];

    protected $scene = [
        'add'   => ['sp_id','pid','content'],
    ];
}