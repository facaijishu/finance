<?php

namespace app\home\validate;

use think\Validate;

class Through extends Validate
{
    protected $rule = [
        
        ['role_type', 'require', '请输入身份'],
        ['real_name', 'require|max:4', '请输入姓名|姓名不能超过4个字'],
        ['realName', 'require|max:4', '请输入姓名|姓名不能超过4个字'],
        ['position','require|max:20','请输入职务|职务不能超过20个字'],
        ['phone', 'require|length:11,11', '请输入手机号|手机号不正确'],
        ['company','require','请输入公司'],
        ['company_jc','require|max:5','请输入公司简称|公司简称为5个字以内'],
        //['email', 'require|email', '请输入联系邮箱|邮箱格式不正确'],
        //['company', 'require', '请输入公司名称'],
        //['position', 'require', '请输入职位'],
//        ['card', 'require', '请上传名片'],
    ];

    protected $scene = [
        'add'   => ['role_type','real_name','phone'],
        'edit'   => ['role_type','realName','phone','company','company_jc'],
    ];
}