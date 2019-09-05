<?php

namespace app\console\model;

use think\Model;

class Menu extends Model
{

    /**
     * 数据修改器，status修改
     * @param $value
     * @return int
     */
    public function setStatusAttr($value)
    {
        if(is_null($value) || empty($value)){
            return 0;
        }
        if('on' === $value || 1 == $value){
            return 1;
        }
    }

    /**
     * 数据修改器，status_text修改
     * @param $value
     * @return int
     */
    public function getStatusTextAttr($value, $data)
    {
        $status = [0 => '不显示', 1 => '显示'];
        return $status[$data['status']];
    }

    protected function switchStatusValue($data){
        if(!isset($data['status'])){
            return 0;
        }
        if('on'===$data['status'] || 1==$data['status']){
            return 1;
        }
    }

    /**
     * 获取菜单导航
     * @param type $app
     * @param type $model
     * @param type $action
     */
    /*public function getMenu() {
        $menuid = I('get.menuid', 0, 'intval');
        $menuid = $menuid ? $menuid : cookie("menuid", "", array("prefix" => ""));
        $info = $this->where(array("id" => $menuid))->getField("id,action,app,controller,parentid,parameter,type,name");
        $find = $this->where(array("parentid" => $menuid, "status" => 1))->getField("id,action,app,controller,parentid,parameter,type,name");
        if ($find) {
            array_unshift($find, $info[$menuid]);
        } else {
            $find = $info;
        }
        foreach ($find as $k => $v) {
            $find[$k]['parameter'] = "menuid={$menuid}&{$find[$k]['parameter']}";
        }
        return $find;
    }*/

    /**
     * 获取菜单
     * @return type
     */
    public function getMenuList()
    {
        /**
        $items['0changyong'] = array(
            "id" => "",
            "name" => "常用菜单",
            "parent" => "changyong",
            "url" => U("Public/changyong"),
        );
        foreach (D('Admin/AdminPanel')->getAllPanel(\Admin\Service\User::getInstance()->id) as $r) {
            $items[$r['mid'] . '0changyong'] = array(
                "icon" => "",
                "id" => $r['mid'] . '0changyong',
                "name" => $r['name'],
                "parent" => "changyong",
                "url" => U($r['url']),
            );
        }
        $changyong = array(
            "changyong" => array(
                "icon" => "",
                "id" => "changyong",
                "name" => "常用",
                "parent" => "",
                "url" => "",
                "items" => $items
            )
        );
         **/
        $often = array();
        $data = $this->getTree(0);
        return array_merge($often, $data ? $data : array());
    }

    /**
     * 取得树形结构的菜单
     * @param type $myid
     * @param type $pid
     * @param type $level
     * @return type
     */
    public function getTree($myid, $pid = "", $level = 1)
    {
        $data = $this->adminMenu($myid);
        $level++;
        if (is_array($data)) {
            foreach ($data as $item) {
                $id = $item->getAttr('id');
                $type = $item->getAttr('type');
                $module = $item->getAttr('module');
                $controller = $item->getAttr('controller');
                $action = $item->getAttr('action');
                //附带参数
                $param = ['menu_id' => $id, 'menu_type' => $type];
                if ($item->getAttr('param')) {
                    $param_p = [];
                    $_param = explode('&', $item->getAttr('param'));
                    foreach ($_param as $key => $value) {
                        $arr = explode('=', $value);
                        $param_p[$arr[0]] = $arr[1];
                    }
//                    $_param = parse_str($item->getAttr('param'));
                    $param = array_merge($param, $param_p);
                }
                
                $array = array(
                    'icon'          => $item->getAttr('icon'),
                    'id'            => $id . $module,
                    'name'          => $item->getAttr('name'),
                    'module'        => $item->getAttr('module'),
                    'controller'    => $item->getAttr('controller'),
                    'action'        => $item->getAttr('action'),
                    'pid'           => $pid,
                    'url'           => url("{$module}/{$controller}/{$action}", $param),
                );
                $res[$id . $module] = $array;
                $child = $this->getTree($item->getAttr('id'), $id, $level);
                //由于后台管理界面只支持三层，超出的不层级的不显示
                if ($child && $level <= 2) {
                    $res[$id . $module]['items'] = $child;
                }
            }
        }
        return isset($res) ? $res : array();
    }

    /**
     * 按父ID查找菜单子项
     * @param integer $pid   父菜单ID
     * @param integer $with_self  是否包括他自己
     */
    public function adminMenu($pid, $with_self = false)
    {
        //父节点ID
        $pid = (int)$pid;
        $result = $this->where(array('pid' => $pid, 'status' => 1))
                        ->order('list_order ASC,id ASC')
                        ->select();
        if (empty($result)) {
            $result = array();
        }
        
        if ($with_self) {
            $parentInfo = $this->where(array('id' => $pid))->find();
            $result2[] = $parentInfo ? $parentInfo : array();
            $result = array_merge($result2, $result);
        }
        //是否超级管理员
        if (model('User', 'service')->isAdmin()) {
            //如果角色为超级管理员 直接通过
            return $result;
        }
        $array = array();
        //子角色列表
        $user = session('c_user_auth');
        $child = explode(',', model('Role')->getAllChildId($user['role_id']));
        foreach ($result as $item) {
            //方法
            $action = $item->getAttr('action');
           //条件
            $where = array('menu_id' => $item->getAttr('id'), 'role_id' => array('IN', $child));
            //如果是菜单项
            if ($item->getAttr('type') == 0) {
                $where['type'] = 0;
            }else{
                $where['type'] = 1;
            }
            
            //public开头的通过
            if (preg_match('/^publics_/', $action)) {
                $array[] = $item;
            } else {
                /*
                if (preg_match('/^ajax_([a-z]+)_/', $action, $_match)) {
                    $action = $_match[1];
                }*/
                //是否有权限
                if (model('RoleAccess')->hasAccessPermission($where)) {
                    $array[] = $item;
                  
                }
            }
        }
       
        return $array;
    }

    public function createMenu($data)
    {
        if(empty($data)){
            $this->error = '没有菜单数据';
            return false;
        }

        //验证菜单信息
        $validate = validate('Menu');
        if(!$validate->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }

        $data['status'] = $this->switchStatusValue($data);
        if(isset($data['id'])){
            $res = $this->allowField(true)->isUpdate(true)->save($data, ['id' => $data['id']]);
        }else{
            $res = $this->allowField(true)->save($data);
        }
        if($res){
            return $this->id;
        }else{
            $this->error = '菜单数据保存失败';
            return false;
        }
    }

    /**
     * 菜单排序
     * @param $data 数据格式为数组
     * [
     *      ['id'=>1, 'list_order'=>0]
     *      ['id'=>2, 'list_order'=>1]
     *      ['id'=>3, 'list_order'=>2]
     *      ['id'=>4, 'list_order'=>3]
     * ]
     * @return bool
     */
    public function menuSort($data)
    {
        if(empty($data) || !is_array($data)){
            $this->error = '排序数据出错';
            return false;
        }

        if($this->allowField(['list_order', 'id'])->saveAll($data)){
            return true;
        }else{
            $this->error = '排序出错';
            return false;
        }
    }

    /**
     * 更新菜单缓存
     * @param type $data
     * @return type
     */
    public function menu_cache() {
        $data = $this->select();
        if (empty($data)) {
            return false;
        }
        $cache = array();
        foreach ($data as $rs) {
            $cache[$rs['id']] = $rs;
        }
        cache('Menu', $cache);
        return $cache;
    }
}
