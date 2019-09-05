<?php
/**
 * Created by PhpStorm.
 * User: WL
 * Date: 2017/3/13
 * Time: 20:19
 */

namespace app\console\service;

use think\Model;

class User extends Model
{

    //当前登录会员详细信息
    private static $userInfo = array();

    /**
     * 连接后台用户服务
     * @return User|null
     */
    static public function getInstance() {
        static $handier = NULL;
        if (empty($handier)) {
            $handier = model('User', 'service');
        }
        //error_log("[".date('Y-m-d H:i:s')."] ".json_encode($handier)."\r\n", 3, "E:logs/fa.log");
        return $handier;
    }

    /**
     * 魔术方法
     * @param type $name
     * @return null
     */
    public function __get($name) {
        //从缓存中获取
        if (isset(self::$userInfo->$name)) {
            return self::$userInfo->$name;
        } else {
            $userInfo = $this->getInfo();
            if (!empty($userInfo)) {
                return $userInfo->$name;
            }
            return NULL;
        }
    }

    /**
     * 获取当前登录用户资料
     * @return array
     */
    public function getInfo() {
        if (empty(self::$userInfo)) {
            self::$userInfo = $this->getUserInfo($this->isLogin(), 4);
        }
        return !empty(self::$userInfo) ? self::$userInfo : false;
    }

    /**
     * 获取用户信息
     * @param type $identifier 用户名或者用户ID
     * @return boolean|array
     */
    private function getUserInfo($identifier, $type = 1,  $password = NULL) {
        if (empty($identifier)) {
            return false;
        }
        return model('User')->getUserInfo($identifier, $type, $password);
    }


    /**
     * 用户登录认证
     * @param  string  identifier 用户名、邮箱、手机、uid
     * @param  string  $password 用户密码
     * @param  integer $type     用户名类型 （1-用户名，2-邮箱，3-手机，4-UID）
     * @return integer           登录成功-用户ID，登录失败-错误编号
     */
    public function login($identifier, $password, $type = 1){
        /* 获取用户数据 */
        $user = $this->getUserInfo($identifier);

        if($user && $user->getAttr('status')){
            /* 验证用户密码 */
            if(pwd_encrypt($password, $user->getAttr('salt')) === $user->getAttr('login_pwd')){
                //$this->updateLogin($user['id']); //更新用户登录信息
                $this->autoLogin($user);
                return $user->getAttr('uid'); //登录成功，返回用户ID
            } else {
                return -2; //密码错误
            }
        } else {
            return -1; //用户不存在或被禁用
        }
    }
    /** 授权登录*/
    public function codeLogin($userid,$mobile)
    {
        $user = $this->getUserInfo($userid);
        if($user && $user->getAttr('status')){
            /* 验证用户手机 */
            if(model('User')->where(['login_name'=>$userid,'mobile'=>$mobile])->find()){
                $this->autoLogin($user);
                return $user->getAttr('uid'); //登录成功，返回用户ID
            } else {
                return -2; //密码错误
            }
        } else {
            return -1; //用户不存在或被禁用
        }
    }
    /**
     * 自动登录用户
     * @param  integer $user 用户信息对像
     */
    private function autoLogin($user){
        /* 更新登录信息 */
        $data = array(
            'uid'               => $user->getAttr('uid'),
            'login_count'       => array('exp', '`login_count`+1'),
            'last_login_time'   => time(),
            'last_login_ip'     => $_SERVER['REMOTE_ADDR'],
//            'last_login_ip'     => get_client_ip(1),
        );
        $this->where(array('uid'=>$user->getAttr('uid')))->update($data);
        /* 记录登录SESSION和COOKIES */
        $auth = array(
            'uid'               => $user->getAttr('uid'),
            'role_id'           => $user->getAttr('role_id'),
            'login_name'        => $user->getAttr('login_name'),
            'last_login_time'   => $user->getAttr('last_login_time'),
        );

        session('user', $user->toArray());
        session('c_user_auth', $auth);
        session('c_user_auth_sign', data_auth_sign($auth, $user->getAttr('uid')));

    }

    /**
     * 检测用户是否登录
     * @return integer 0-未登录，大于0-当前登录用户ID
     */
    public function isLogin(){
        $user = session('c_user_auth');
        if (empty($user)) {
            return 0;
        } else {
            return session('c_user_auth_sign') == data_auth_sign($user, $user['uid']) ? $user['uid'] : 0;
        }
    }

    /**
     * 检测当前用户是否为管理员
     * @return boolean true-管理员，false-非管理员
     */
    public function isAdmin($uid = null){
        $uid = is_null($uid) ? $this->isLogin() : $uid;
        return $uid && (in_array($uid, config('administrator')));
    }

    /**
     * 注销当前用户
     * @return void
     */
    public function logout(){
        session('c_user_auth', null);
        session('c_user_auth_sign', null);
    }

    public function changePwd($data)
    {
        $user = model('User');
        $res = $user->changePwd($data);
        if(!$res){
            $this->error = $user->getError();
            return false;
        }
        //退出登录
        $this->logout();
        return true;
    }
}