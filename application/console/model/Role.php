<?php

namespace app\console\model;

use think\Model;

class Role extends Model
{

    protected $autoWriteTimestamp = 'int';

    /**
     * 通过递归的方式获取该角色下的全部子角色
     * @param type $id
     * @return string
     */
    public function getAllChildId($id) {
        if (empty($this->roleList)) {
            $this->roleList = $this->getTreeArray();
        }
        $all_child_id = $id;
        if (is_array($this->roleList)) {
            foreach ($this->roleList as $k => $role) {
                if ($role->getAttr('pid') && $k != $id && $role->getAttr('pid') == $id) {
                    $all_child_id .= ',' . $this->getAllChildId($k);
                }
            }
        }
        return $all_child_id;
    }

    /**
     * 返回Tree使用的数组
     * @return array
     */
    public function getTreeArray() {
        $role_list = array();
        $role_data = $this->order(array("list_order" => "asc", "id" => "desc"))->select();
        foreach ($role_data as $role) {
            $role_list[$role->getAttr('id')] = $role;
        }
        return $role_list;
    }

    /**
     * 返回select选择列表
     * @param int $parentid 父节点ID
     * @param string $selectStr 是否要 <select></select>
     * @return string
     */
    public function selectHtmlOption($parentid = 0, $selectStr = '') {
        $tree = new \sys\Tree();
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        $str = "'<option value='\$id' \$selected>\$spacer\$name</option>";
        $tree->init($this->getTreeArray());
        if ($selectStr) {
            $html = '<select ' . $selectStr . '>';
            $html.=$tree->get_tree(0, $str, $parentid);
            $html.='</select>';
            return $html;
        }
        return $tree->get_tree(0, $str, $parentid);
    }

    public function createRole($data){
        if(empty($data)){
            $this->error = '没有角色数据';
            return false;
        }

        $pk = $this->getPk();
        //验证角色信息
        $validate = validate('Role');
        if(!$validate->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }

        if(isset($data[$pk])){
            if(isset($data['status'])){
                $data['status'] = 1;
            } else {
                $data['status'] = 0;
            }
            $res = $this->allowField(true)->isUpdate(true)->save($data);
        }else{
            $res = $this->allowField(true)->save($data);
        }

        if($res){
            return $this->$pk;
        }else{
            $this->error = '角色信息保存失败';
            return false;
        }
    }

    /**
     * 获取角色信息
     * @param $role_id
     * @return array|bool|false|\PDOStatement|string|Model
     */
    public function getRole($role_id){
        if(0 >= $role_id){
            return false;
        }
        return $this->where(array('id' => $role_id))->find();
    }

    /**
     * 根据角色ID返回全部权限
     * @param type $role_id 角色ID
     * @return array
     */
    public function getAccessList($role_id) {
        $priv_data = array();
        $data = model("RoleAccess")->getAccessList($role_id);
        if (empty($data)) {
            return $priv_data;
        }
        foreach ($data as $k => $val) {
            $priv_data[$k] = array(
                'role_id' => $val->role_id,
                'menu_id' => $val->menu_id,
                'type'    => $val->type
            );
        }
        return $priv_data;
    }


    /**
     * 检查指定菜单是否有权限
     * @param type $data menu表中一个，单条
     * @param type $role_id 需要检查的角色ID
     * @param type $priv_data 已授权权限表数据
     * @return boolean
     */
    public function hasAccessPermission($data, $role_id, $priv_data = array()) {
        if (empty($data)) {
            return false;
        }
        if (empty($priv_data)) {
            //查询已授权权限
            $priv_data = $this->getAccessList($role_id);
        }
        if (empty($priv_data)) {
            return false;
        }

        //已授权ID
        $priv_menus = array();
        foreach ($priv_data as $item) {
            $priv_menus[] = $item['menu_id'];
        }

        if(in_array($data['id'], $priv_menus)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 根据角色Id获取角色名
     * @param int $role_id 角色id
     * @return string 返回角色名
     */
    public function getRoleNameById($role_id) {
        return $this->where(array('id' => $role_id))->value('name');
    }
    


}
