<?php
/**
 * Created by PhpStorm.
 * User: WL
 * Date: 2017/3/16
 * Time: 0:25
 */

namespace app\console\validate;

use think\Validate;

class User extends Validate
{
    protected $rule = [
        'login_name'    => 'require',
        'login_pwd'     => 'require|length:6,20',
        'pwd_confirm'   => 'require|confirm:login_pwd',
        'real_name'     => 'require',
        'email'         => 'email',
        'role'          => 'require',
        'captcha'       => 'require'
    ];

    protected $message = [
        'login_name.require'    => '请输入登录名',
        'login_pwd.require'     => '请输入密码',
        'login_pwd.length'      => '密码长度为6~20位',
        'pwd_confirm.require'   => '请再次输入密码',
        'pwd_confirm.confirm'   => '两次密码输入不一致',
        'real_name.require'     => '请输入真实姓名',
        'email.email'           => '邮箱格式不正确',
        'role.require'          => '请选择角色',
        'captcha.require'       => '请输入验证码',
    ];

    protected $scene = [
        'add'   => ['login_name'=>'require|checkLoginNameIsExist', 'login_pwd', 'pwd_confirm', 'real_name', 'email', 'role'],
        'edit'  => ['login_name'=>'require|checkLoginNameIsExist', 'login_pwd'=>'length:6,20', 'pwd_confirm'=>'confirm:login_pwd', 'real_name', 'email', 'role'],
        'login' => ['login_name', 'login_pwd', 'captcha'],
        'change_pwd' => ['login_pwd', 'pwd_confirm'],
        'coustom_method' => ['login_name'=>'require']
    ];

    //校验用户名是否存在
    protected function checkLoginNameIsExist($value,$rule,$data)
    {
        $user_model = model('User');

        //判断是添加还是修改用户信息
        //修改用户信息
        if(isset($data['id'])){
            //查询用户信息
            $user = $user_model->get(['uid'=>$data['id']]);
            //是否输入了登录名
            if(!$this->scene('coustom_method')->check(['login_name'=>$value])){
                return '请输入登录名';
            }
            //判断登录名是否修改
            if($user['login_name'] == $data['login_name']) {
                return true;
            }
            //登录名已修改
            if($user['login_name'] != $data['login_name'] && $user_model->checkUserIsExist($data['login_name'], 1)) {
                return '该用户已存在';
            }
            return true;
        //添加用户信息
        }else{
            if($user_model->checkUserIsExist($data['login_name'], 1)) {
                return '该用户已存在';
            }
            if(!$this->scene('coustom_method')->check(['login_name'=>$value])){
                return '请输入登录名';
            }
            return true;
        }
    }
}