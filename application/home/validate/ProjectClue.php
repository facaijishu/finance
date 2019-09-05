<?php

namespace app\home\validate;

use think\Validate;

class ProjectClue extends Validate
{
    protected $rule = [
        ['pro_name', 'require', '请输入项目名称'],
        ['capital_plan', 'require', '请选择所属业务'],
        ['financing_amount',['require','regex' => '/^(0|[1-9][0-9]{0,9})(\.[0-9]{1,2})?$/'], '请输入项目金额|请输入正确的项目金额'],
        ['company_name', 'require', '请输入公司名称'],
        ['contacts', 'require', '请输入姓名'],
        ['contacts_position', 'require', '请输入职位'],
        ['contacts_tel',['require' , 'regex' => '/^1\d{10}$/'], '请输入联系电话|请输入正确的联系电话'],
        //['contacts_email',['require' , 'regex' => '/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/'], '请输入联系人邮箱|请输入正确的联系邮箱'],
    ];

    protected $scene = [
        'add'   => ['pro_name','capital_plan','financing_amount','company_name','contacts','contacts_position','contacts_tel'],
    ];
}