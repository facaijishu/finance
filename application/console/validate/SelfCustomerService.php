<?php

namespace app\console\validate;

use think\Validate;

class SelfCustomerService extends Validate
{
    protected $rule = [
        'kf_account'    => 'require',
        'kf_nick'    => 'require',
        'kf_headimgurl'    => 'require',
    ];

    protected $message = [
        'kf_account.require'    => '客服账号为空,请输入',
        'kf_nick.require'    => '客服昵称为空,请输入',
        'kf_headimgurl.require'    => '客服头像为空,请输入',
    ];

    protected $scene = [
        'add'   => ['kf_account' , 'kf_nick' , 'kf_headimgurl'],
        'edit'   => ['kf_account' , 'kf_nick' , 'kf_headimgurl'],
    ];
}