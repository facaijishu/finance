<?php

namespace app\console\validate;

use think\Validate;

class ActivitySign extends Validate
{
    protected $rule = [
        'a_id'          => 'require',
        'mobile'        => 'require',
        'realname'      => 'require',
        'company'       => 'require',
        'position'      => 'require',
    ];

    protected $message = [
        'a_id.require'      => '活动编号为空,请输入',
        'mobile.require'    => '手机号码为空,请输入',
        'realname.require'  => '真实姓名为空,请输入',
        'company.require'   => '公司名称为空,请输入',
        'position.require'  => '职位为空,请输入',
    ];

    protected $scene = [
        'add'   => ['a_id' , 'mobile','realname' ,'company', 'position'],
      ];
}