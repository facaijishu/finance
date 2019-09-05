<?php

namespace app\console\model;

use think\Db;
use think\Model;
use think\Request;

class RoleAccess extends Model
{

    /**
     * 根据角色ID返回全部权限
     * @param type $role_id 角色ID
     * @return array
     */
    public function getAccessList($role_id) {
        if (empty($role_id)) {
            return false;
        }
        //子角色列表
        $child = explode(',', model('Role')->getAllChildId($role_id));
        if (empty($child)) {
            return false;
        }
        //查询出该角色拥有的全部权限列表
        $data = $this->where(array('role_id' => array('IN', $child)))->select();
        if (empty($data)) {
            return false;
        }
        return $data;
    }

    /**
     * 检查用户是否有对应权限
     * @param type $map 角色权限菜单ID，为空自动获取
     * @return type
     */
    public function hasAccessPermission($map = '') {
        //超级管理员
        if (\app\console\service\User::getInstance()->isAdmin()) {
            return true;
        }
        /*if (!is_array($map)) {
            //子角色列表
            $child = explode(',', model("Role")->getAllchildid(User::getInstance()->role_id));
            if (!empty($map)) {
                $map = trim($map, '/');
                $map = explode('/', $map);
                if (empty($map)) {
                    return false;
                }
            } else {
                $map = array(Request::instance()->module(), Request::instance()->controller(), Request::instance()->action());
            }
            if (count($map) >= 3) {
                list($module, $controller, $action) = $map;
            } elseif (count($map) == 1) {
                $module = Request::instance()->module();
                $controller = Request::instance()->controller();
                $action = $map[0];
            } elseif (count($map) == 2) {
                $module = Request::instance()->module();
                list($controller, $action) = $map;
            }
            $map = array('role_id' => array('IN', $child), 'module' => $module, 'controller' => $controller, 'action' => $action);
        }*/
        $count = $this->where($map)->count();
        return $count ? true : false;
    }

    /**
     * 角色授权
     * @param type $addauthorize 授权数据
     * @param type $role_id 角色id
     * @return boolean
     */
    public function batchAuthorize($authorize_date, $role_id = 0) {
        if (empty($role_id)) {
            if (empty($authorize_date[0]['role_id'])) {
                $this->error = '角色ID不能为空！';
                return false;
            }
            $role_id = $authorize_date[0]['role_id'];
        }

        //权限数据为空时，清除授权
        if (empty($authorize_date)) {
            $res = $this->where(array("role_id" => $role_id))->delete();
            if($res){
                return true;
            }else{
                $this->error = '执行清除授权失败！';
                return false;
            }
        }

        //删除旧的权限
        $this->where(array("role_id" => $role_id))->delete();
        return $this->saveAll($authorize_date) !== false ? true : false;
    }
}
