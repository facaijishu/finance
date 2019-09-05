<?php
namespace app\console\controller;

use \sys\Tree;

class Menu extends ConsoleBase
{
    /**
     * 菜单列表
     */
    public function index()
    {
        $result = model("Menu")->order(array("list_order" => "ASC"))->select();
        $tree = new Tree();
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        foreach ($result as $item) {
            $item->str_manage = '<a class="btn btn-xs btn-primary" href="javascript:void(0);" onclick="loadURL(\'' . url("menu/add", array("pid" => $item->id)) . '\');">添加子菜单</a> <a class="btn btn-xs btn-warning" href="javascript:void(0);" onclick="loadURL(\''. url("menu/edit", array("id" => $item->id)) .'\')">修改</a> <a class="btn btn-xs btn-danger js-menu-del" data-menu-id="'.$item->id.'" href="javascript:void(0);">删除</a> ';
            $item->status_text = $item->status_text;
            $array[] = $item;
        }
        $tree->init($array);
        $str = "<tr>
                <td><input name='list_order' type='text' size='3' value='\$list_order' data-id='\$id' class='input' style='background-color:rgba(255,255,255,.2); rel='tooltip' data-placement='right' data-original-title='直接编辑排序' data-html='true'></td>
                <td>\$id</td>
                <td >\$spacer\$name</td>
                <td><i class='fa fa-lg fa-fw \$icon'></i></td>
                <td>\$status_text</td>
                <td>\$str_manage</td>
            </tr>";
        $menu = $tree->get_tree(0, $str);
        $this->assign("menu", $menu);
        return view();
    }

    /**
     * 添加菜单
     */
    public function add()
    {
        if($this->request->isPost()){
            $menu = model('Menu');
            if($menu->createMenu(input())){
                $data['url'] = url('Menu/index');
                $this->result($data, 1, '添加菜单成功', 'json');
            }else{
                $error = $menu->getError() ? $menu->getError() : '添加菜单失败';
                $this->result('', 0, $error, 'json');
            }
        }else{
            $tree = new Tree();
            $pid = $this->request->param('pid/d', 0);
            $result = model('Menu')->order('list_order asc')->select();
            foreach ($result as $item) {
                $item['selected'] = $item->id == $pid ? 'selected' : '';
                $array[] = $item;
            }
            $str = "<option value='\$id' \$selected>\$spacer \$name</option>";
            $tree->init($array);
            $select_option = $tree->get_tree(0, $str);
            $this->assign("select_option", $select_option);
            return view('add');
        }
    }

    /**
     * 编辑菜单
     * @return \think\response\View
     */
    public function edit(){
        if($this->request->isPost()){
            $menu = model('Menu');
            if($menu->createMenu(input())){
                $data['url'] = url('Menu/index');
                $this->result($data, 1, '编辑菜单成功', 'json');
            }else{
                $error = $menu->getError() ? $menu->getError() : '编辑菜单失败';
                $this->result('', 0, $error, 'json');
            }
        }else{
            $id = $this->request->param('id/d');
            $menu = model('Menu')->where(['id' => $id])->find();
            if(is_null($menu)){
                $this->error('获取菜单信息出错');
            }
            $tree = new Tree();
            $result = model('Menu')->order('list_order asc')->select();
            foreach ($result as $item) {
                $item['selected'] = $item->id == $menu->pid ? 'selected' : '';
                $array[] = $item;
            }
            $str = "<option value='\$id' \$selected>\$spacer \$name</option>";
            $tree->init($array);
            $select_option = $tree->get_tree(0, $str);
            $this->assign('menu', $menu);
            $this->assign("select_option", $select_option);
            return view('edit');
        }
    }

    /**
     * 删除菜单
     */
    public function delete()
    {
        $id = $this->request->param('id/d', 0);
        if(empty($id)){
            $this->result('', 0, '请指定要删除的菜单', 'json');
        }
        $count = model('Menu')->where(array("pid" => $id))->count();
        if ($count > 0) {
            $this->result('', 0, '该菜单下还有子菜单，不能删除！', 'json');
        }
        if (model('Menu')->where( ['id' => $id] )->delete() !== false) {
            $this->result('', 1, '删除菜单成功', 'json');
        } else {
            $this->result('', 0, '删除失败', 'json');
        }
    }

    public function order()
    {
        $id = $this->request->param('id/d', 0);
        $order = $this->request->param('order/d', 0);

        $res = model('Menu')->save(['list_order'=>$order], ['id'=>$id]);
        if($res){
            return $this->result('', 1, '已修改，请刷新页面', 'json');
        } else {
            return $this->result('', 0, '排序出错', 'json');
        }
    }
}
