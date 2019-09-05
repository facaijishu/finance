<?php

namespace app\console\controller;

use \sys\Tree;

class Role extends ConsoleBase
{

    /**
     * 角色列表
     * @return \think\response\View
     */
    public function index()
    {
        $this->assign('title', '角色管理');

        $tree = new Tree();
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        $result = model('Role')->getTreeArray();
        foreach ($result as $k => $item) {
            $operating = '';
            if (1 == $item->id) {
                $operating = '<a class="btn btn-xs btn-primary disabled">权限设置</a> <a class="btn btn-xs btn-info"  href="javascript:void(0);" onclick="loadURL(\''.url("user/index", array("role_id" => $item->id)).'\')" >成员管理</a> <a class="btn btn-xs btn-warning disabled">修改</a> <a class="btn btn-xs btn-danger disabled">删除</a>';
            } else {
                $operating = '<a class="btn btn-xs btn-primary" href="javascript:void(0);" onclick="loadURL(\''.url("role/authorize", array("id" => $item->id)).'\')" >权限设置</a> <a class="btn btn-xs btn-info" href="javascript:void(0);" onclick="loadURL(\''.url("user/index", array("role_id" => $item->id)).'\')" >成员管理</a> <a class="btn btn-xs btn-warning" href="javascript:void(0);" onclick="loadURL(\''.url("role/edit", array("id" => $item->id)).'\')" >修改</a> <a class="btn btn-xs btn-danger js-role-del" data-role-id="'.$item->id.'" href="javascript:void(0);" >删除</a>';
            }
            $result[$k]['operating'] = $operating;

            $status_text = '';
            if(1 == $item->status){
                $status_text = '<i class="fa fa-check"></i>';
            }else{
                $status_text = '<i class="fa fa-times"></i>';
            }
            $result[$k]['status_text'] = $status_text;
        }
        $str = "<tr>
          <td>\$id</td>
          <td>\$spacer\$name</td>
          <td>\$remark</td>
          <td>\$status_text</td>
          <td>\$operating</td>
        </tr>";
        $tree->init($result);
        $this->assign("role", $tree->get_tree(0, $str));

        return view();
    }

    /**
     * 添加角色
     * @return \think\response\View
     */
    public function add()
    {
        if($this->request->isPost()){
            $role = model('Role');
            if($role->createRole(input())){
                $this->result(['url'=>url('Role/index')], 1, '添加角色成功', 'json');
            }else{
                $error = $role->getError() ? $role->getError() : '添加角色失败';
                $this->result('', 0, $error, 'json');
            }
        }else{
            $this->assign('title', '添加角色');

            $option = model('Role')->selectHtmlOption(0);
            $this->assign('role_option', $option);
            return view();
        }
    }

    /**
     * 编辑角色
     * @return \think\response\View
     */
    public function edit()
    {
        if($this->request->isPost()){
            $role = model('Role');
            if($role->createRole(input())){
                $this->result(['url'=>url('Role/index')], 1, '修改角色成功', 'json');
            }else{
                $error = $role->getError() ? $role->getError() : '修改角色失败';
                $this->result('', 0, $error, 'json');
            }
        }else{
            $this->assign('title', '添加角色');
            $id = $this->request->param('id/d');
            $role = model('Role')->where(['id' => $id])->find();
            if(is_null($role)){
                $this->error('获取角色信息出错');
            }
            $option = model('Role')->selectHtmlOption($role->pid);
            $this->assign('role_option', $option);
            $this->assign('role', $role);
            return view();
        }
    }

    /**
     * 删除角色
     */
    public function delete()
    {
        $id = $this->request->param('id/d', 0);
        $count = model('Role')->where(array("pid" => $id))->count();
        if ($count > 0) {
            $this->result('', 0, "该角色下还有子角色，不能删除！", 'json');
        }
        if (model('Role')->where( ['id' => $id] )->delete() !== false) {
            $this->result('', 1, "删除角色成功！", 'json');
        } else {
            $this->result('', 0, "删除失败！", 'json');
        }
    }

    /**
     * 角色授权
     * @return \think\response\View
     */
    public function authorize()
    {
        if ($this->request->isPost()) {
            $role_id = $this->request->param('role_id/d', 0);
            if (!$role_id) {
                $this->result('', 0, '需要授权的角色不存在！', 'json');
            }
            $role_access = model('RoleAccess');
            //被选中的菜单项
            $menu_id_str = input('menu_id', '');
            if(empty($menu_id_str)){
                $menu_id_all = '';
            }else{
                $menu_id_all = explode(',', $menu_id_str);
            }

            if (is_array($menu_id_all) && count($menu_id_all) > 0) {
                //取得菜单数据
                $menu_info = cache('Menu');

                $authorize_data = array();
                //检测数据合法性
                foreach ($menu_id_all as $menu_id) {
                    if (empty($menu_info[$menu_id])) {
                        continue;
                    }
                    $data = array();
                    $data['role_id']    = $role_id;
                    $data['menu_id']    = $menu_id;
                    $data['type']       = $menu_info[$menu_id]['type'] ? 1 : 0;
                    $authorize_data[]     = $data;
                }
                if ($role_access->batchAuthorize($authorize_data, $role_id)) {
                    $this->result(['url'=>url("role/index")], 1, '授权成功！', 'json');
                } else {
                    $error = $role_access->getError() ? $role_access->getError() : '授权失败！';
                    $this->result('', 0, $error, 'json');
                }
            } else {
                if($role_access->batchAuthorize(array(), $role_id)){
                    $this->result(['url'=>url("role/index")], 1, '执行清除授权成功！', 'json');
                }else{
                    $error = $role_access->getError() ? $role_access->getError() : '执行清除授权失败！';
                    $this->result('', 0, $error, 'json');
                };
            }
        } else {
            //角色ID
            $role_id = $this->request->param('id/d', 0);
            if (empty($role_id)) {
                $this->error("参数错误！");
            }
            //菜单缓存
            $result = cache("Menu");

            //获取已权限表数据
            $priv_data = model("Role")->getAccessList($role_id);
            $role = model('Role');
            $json = array();
            foreach ($result as $val) {
                $data = array(
                    'id' => $val['id'],
                    'pid' => $val['pid'],
                    'name' => $val['name'] . ($val['type'] == 0 ? "(菜单项)" : "")
                );
                if($role->hasAccessPermission($val, $role_id, $priv_data)){
                    $data['checked'] = true;
                }
                $json[] = $data;
            }
            $this->assign([
                'role_json'      => json_encode($json),
                'role_id'   => $role_id,
                'role_name'      => $role->getRoleNameById($role_id)
            ]);
            return view();
        }
    }

}
