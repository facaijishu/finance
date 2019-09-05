<?php

namespace app\console\validate;

use think\Validate;

class Consultation extends Validate
{
    protected $rule = [
        'title'    => 'require',
        'source'    => 'require',
//        'list_order'    => 'require',
        'top_img'    => 'require',
    ];

    protected $message = [
        'title.require'    => '资讯标题为空,请输入',
        'source.require'    => '资讯来源为空,请输入',
//        'list_order.require'    => '资讯排序为空,请输入',
        'top_img.require'    => '头图为空,请上传',
    ];

    protected $scene = [
        'add'   => ['title' , 'source','top_img' ],
        'edit'   => ['title' , 'source' ,'top_img'],
    ];
}