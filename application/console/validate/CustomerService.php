<?php

namespace app\console\validate;

use think\Validate;

class CustomerService extends Validate
{
    protected $rule = [
        'kf_account'    => 'require',
        'kf_nick'    => 'require',
        'kf_headimgurl'    => 'require',
        'kf_qr_code'    => 'require',
    ];

    protected $message = [
        'kf_account.require'    => '客服账号为空,请输入',
        'kf_nick.require'    => '客服昵称为空,请输入',
        'kf_headimgurl.require'    => '客服头像为空,请输入',
        'kf_qr_code.require'    => '客服二维码为空,请上传',
    ];

    protected $scene = [
        'add'   => ['kf_account' , 'kf_nick' , 'kf_headimgurl','kf_qr_code'],
        'edit'   => ['kf_account' , 'kf_nick' , 'kf_headimgurl','kf_qr_code'],
    ];
}