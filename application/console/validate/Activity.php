<?php

namespace app\console\validate;

use think\Validate;

class Activity extends Validate
{
    protected $rule = [
        'act_name'          => 'require',
        'start_time'        => 'require',
        'end_time'          => 'require',
        'sign_start_time'   => 'require',
        'sign_end_time'     => 'require',
        'address'           => 'require',
        'fee'               => 'require',
        'max_people'        => 'require',
        'list_order'        => 'require',
        'introduce'         => 'require',
        'content'           => 'require',
        'top_img'           => 'require',
    ];

    protected $message = [
        'act_name.require'          => '活动标题为空,请输入',
        'start_time.require'        => '活动开始时间为空,请输入',
        'end_time.require'          => '活动结束时间为空,请输入',
        'sign_start_time.require'   => '报名开始时间为空,请输入',
        'sign_end_time.require'     => '报名结束时间为空,请输入',
        'address.require'           => '活动地址为空,请输入',
        'fee.require'               => '活动费用为空,请输入',
        'max_people.require'        => '人数上限为空,请输入',
        'list_order.require'        => '排序为空,请输入',
        'introduce.require'         => '活动简介为空,请输入',
        'content.require'           => '活动详情为空,请输入',
        'top_img.require'           => '头图为空,请上传',
    ];

    protected $scene = [
        'add'   => ['act_name' , 'start_time','end_time' ,'sign_start_time', 'sign_end_time','address' , 'fee' , 'max_people' , 'content' , 'top_img','list_order','introduce'],
        'edit'  => ['act_name' , 'start_time','end_time' ,'sign_start_time', 'sign_end_time','address' , 'fee' , 'max_people' , 'content' , 'top_img','list_order','introduce'],
    ];
}