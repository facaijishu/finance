<?php

namespace app\console\validate;

use think\Validate;

class Trilateral extends Validate
{
    protected $rule = [
        'title'         => 'require',
        'type'          => 'require',
        'meetingtime'   => 'require',
        'content'       => 'require',
        'num'           => 'require',
        'cosponsor'     => 'require',
        'pro_ids'       => 'require',
        'top_img'       => 'require',
        'index_img'     => 'require',
        

    ];

    protected $message = [
        'title.require'         => '路演标题为空,请输入',
        'type.require'          => '路演类型为空,请输入',
        'meetingtime.require'   => '路演时间为空,请输入',
        'content.require'       => '路演简介为空,请输入',
        'num.require'           => '参演人数为空,请输入',
        'cosponsor.require'     => '协办方为空,请输入',
        'pro_ids.require'       => '参演项目为空,请输入',
        'top_img.require'       => '一览展示图为空,请上传',
        'index_img.require'     => '详细展示图为空,请上传',
    ];

    protected $scene = [
        'add'   => ['title' , 'type' , 'meetingtime','top_img','content','num','cosponsor','pro_ids','top_img','index_img'],
        'edit'  => ['title' , 'type' , 'meetingtime','top_img','content','num','cosponsor','pro_ids','top_img','index_img'],
    ];
}