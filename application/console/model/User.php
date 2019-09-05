<?php

namespace app\console\model;

use think\Model;

class User extends Model
{

    protected $autoWriteTimestamp = 'int';

    protected static function init()
    {
        parent::init();
        User::event('before_write', function ($user){
            if($user && isset($user->login_pwd)){
                $user->salt = genRandomString();
                $user->login_pwd = pwd_encrypt($user->login_pwd, $user->salt);
            }
        });
    }

    /**
     * 数据修改器，status修改
     * @param $value
     * @return int
     */
    public function setStatusAttr($value)
    {
        if(is_null($value)){
            return 0;
        }
        if('on' == $value){
            return 1;
        }
    }

    /**
     * 获取用户信息
     * @param $identifier
     * @param int $type 用户名类型 （1-用户名，2-邮箱，3-手机，4-UID）
     * @param null $password
     * @return array|bool|false|int|\PDOStatement|string|Model
     */
    public function getUserInfo($identifier, $type = 1, $password = NULL)
    {
        if (empty($identifier)) {
            return false;
        }
        $user = $this->checkUserIsExist($identifier, $type);
        if(!$user) {
            return false;
        }

        //密码验证
        if (!empty($password) && pwd_encrypt($password, $user->getAttr('salt')) !== $user->getAttr('login_pwd')) {
            return false;
        }
        if(0 >= $user->getAttr('default_role_id')){
            $role = model('RoleUser')->where(array('uid' => $user->getAttr('uid')))->find();
            $role_id = $role ? $role->getAttr('role_id') : 0;
            $user->setAttr('role_id', $role_id);
        }else{
            $user->setAttr('role_id', $user->getAttr('default_role_id'));
        }

        return $user;
    }

    public function getUserById($uid)
    {
        $user = $this->where(['uid'=>$uid])->find();

        if($user){
            $role_list = \think\Db::name('role_user')
                ->alias('ru')
                ->field('r.id, r.name')
                ->join('role r', 'ru.role_id = r.id', 'left')
                ->where(['ru.uid'=>$uid])
                ->select();
            $role_temp = [];
            foreach ($role_list as $role){
                $role_temp[] = $role['id'];
            }
            $user['roles_str'] = implode(',', $role_temp);
            $user['roles'] = $role_list;
        }
        return $user;
    }

    public function createUser($data)
    {
        if(empty($data)){
            $this->error = '没有用户数据';
            return false;
        }
        //验证用户信息
        $validate = validate('User');
        $scene = isset($data['id']) ? 'edit' : 'add';
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }

        //开启事务
        $this->startTrans();
        try{
            $user_data = [];
            if(isset($data['id'])){
                if(empty($data['login_pwd']) || empty($data['pwd_confirm'])){
                    unset($data['login_pwd']);
                    unset($data['pwd_confirm']);
                }
                if (!isset($data['status'])) {
                    $this->where(['uid' => $data['id']])->update(['status' => 0]);
                }
                $this->allowField(true)->save($data, ['uid'=>$data['id']]);
                $roles = $data['role'];
                $roles = explode(',', $roles);
                foreach($roles as $k=>$item){
                    $user_data[$k]['uid'] = $data['id'];
                    $user_data[$k]['role_id'] = $item;
                }
                $role_user = model('RoleUser');
                $role_user->where(['uid' => $data['id']])->delete();
                $role_user->isUpdate(true)->saveAll($user_data);
            }else{
                $data['create_ip'] = $_SERVER['REMOTE_ADDR'];
                $this->allowField(true)->save($data);
                
                $roles = $data['role'];
                $roles = explode(',', $roles);
                foreach($roles as $k=>$item){
                    $user_data[$k]['uid'] = $this->uid;
                    $user_data[$k]['role_id'] = $item;
                }
                model('RoleUser')->saveAll($user_data, false);
                
            }
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }

    public function getUserList($param = [])
    {
        if(isset($param['role_id'])){
            $list = $this->alias('u')
                        ->field('u.*')
                        ->join('role_user ru', 'ru.uid = u.uid', 'left')
                        ->where(['ru.role_id'=>$param['role_id']])
                        ->select();
        }else{
            $list = $this->select();
        }
        $user = \app\console\service\User::getInstance();
        foreach ($list as &$item) {
            $role = \think\Db::name('role_user')
                ->alias('ru')
                ->field('r.id, r.name')
                ->join('role r', 'ru.role_id = r.id', 'left')
                ->where(['ru.uid'=>$item['uid']])
                ->select();
            $item['roles'] = $role;
            $item->last_login_time = $item->last_login_time ? date('Y-m-d H:i:s', $item->last_login_time) : '';
            if($user->isAdmin($item->uid)){
                $item->is_admin = 1;
            }else{
                $item->is_admin = 0;
            }
        }
        return $list;
    }

    public function deleteUser($uid)
    {
        if(empty($uid)){
            $this->error = '用户不存在';
            return false;
        }

        //开启事务
        $this->startTrans();

        try{
            model('RoleUser')->where(['uid'=>$uid])->delete();
            $this->where(['uid'=>$uid])->delete();
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }

    public function changePwd($data){
        if(empty($data)){
            $this->error = '没有用户数据';
            return false;
        }
        //验证用户信息
        $validate = validate('User');
        if(!$validate->scene('change_pwd')->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }

        return $this->allowField(true)->save($data, ['uid'=>$data['id']]);
    }

    /**
     * 检查用户是否存在
     * @param $identifier
     * @param int $type
     * @return array|bool|false|int|\PDOStatement|string|Model 存在：用户对象； 不存在：false;
     */
    public function checkUserIsExist($identifier, $type = 1)
    {
        if (empty($identifier)) {
            return false;
        }
        $map = array();
        switch ($type) {
            case 1:
                $map['login_name'] = $identifier;
                break;
            case 2:
                $map['email'] = $identifier;
                break;
            case 3:
                $map['mobile'] = $identifier;
                break;
            case 4:
                $map['uid'] = $identifier;
                break;
            default:
                return 0; //参数错误
        }

        $user = $this->where($map)->find();

        return empty($user) ? false : $user;
    }
}
