<?php
/**
 * Created by PhpStorm.
 * User: WL
 * Date: 2017/3/10
 * Time: 0:57
 */

namespace app\console\controller;

class User extends ConsoleBase
{

    /**
     * 用户列表
     * @return \think\response\View
     */
    public function index(){
//        $this->assign('title', '用户管理');
//
//        $user = model('User');
//        $param = [];
//        if(request()->has('role_id', 'param')){
//            $param['role_id'] = $this->request->param('role_id/d');
//        }
//
//        $list = $user->getUserList($param);
//        $this->assign('list', $list);
        return view();
    }

    public function read(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));

        $uid = $this->request->get('uid', '');
        $login_name = $this->request->get('login_name', '');
        $real_name = $this->request->get('real_name', '');

        $user = model('User');
        //搜索条件
        if('' !== $uid) {
            $user->where(['uid'=>$uid]);
        }
        if('' !== $login_name) {
            $user->where('login_name','like','%'.$login_name.'%');
        }
        if('' !== $real_name) {
            $user->where('real_name','like','%'.$real_name.'%');
        }
//        $user->where('status','eq',1);
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $user->order($_order);
        }
        $user->field('SQL_CALC_FOUND_ROWS *');
        $list = $user->limit($start, $length)->select();
        $result = $user->query('SELECT FOUND_ROWS() as count');
        $total = $result[0]['count'];
        $data = [];
        $user = \app\console\service\User::getInstance();
        foreach ($list as $key => $item) {
            $role = \think\Db::name('role_user')
                ->alias('ru')
                ->field('r.id, r.name')
                ->join('role r', 'ru.role_id = r.id', 'left')
                ->where(['ru.uid'=>$item['uid']])
                ->find();
//            $name = '';
//            foreach ($role as $key => $value) {
                $name = '<div class="badge bg-color-blue">'.$role['name'].'</div>';
//            }
            $list[$key]['user_type'] = $name;
            if($user->isAdmin($item['uid'])){
                $list[$key]['is_admin'] = 1;
            }
            if($item['last_login_time'] == 0){
                $list[$key]['last_login_time'] = '-';
            } else {
                $list[$key]['last_login_time'] = date("Y-m-d H:i:s" , $item['last_login_time']);
            }
            $data[] = $item->toArray();
        }
        $return['data'] = $data;
        $return['recordsFiltered'] = $total;
        $return['recordsTotal'] = $total;

        echo json_encode($return);
    }
    /**
     * 添加用户
     * @return \think\response\View
     */
    public function add(){
        if(request()->isPost()){
            $user = model('User');
            if($user->createUser(input())){
                $data['url'] = url('user/index');
                $this->result($data, 1, '添加用户成功', 'json');
            }else{
                $error = $user->getError() ? $user->getError() : '添加用户失败';
                $this->result('', 0, $error, 'json');
            }
        }else{
            $admin = config('administrator');
            $this->assign('roles', json_encode($admin));
            $this->assign('title', '添加用户');
            $option = model('Role')->selectHtmlOption(0);
            $this->assign('role_option', $option);
            return view();
        }
    }

    /**
     * 编辑用户
     * @return \think\response\View
     */
    public function edit(){
        if(request()->isPost()){
            $user = model('User');
            if($user->createUser(input())){
                $data['url'] = url('user/index');
                $this->result($data, 1, '修改用户成功', 'json');
            }else{
                $error = $user->getError() ? $user->getError() : '修改用户失败';
                $this->result('', 0, $error, 'json');
            }
        }else{
            $id = $this->request->param('id/d');
            $user = model('User');
            $info = $user->getUserById($id);
            $this->assign('info', $info);

            $selected = [];
            foreach ($info['roles'] as $role){
                $selected[] = $role['id'];
            }
            $option = model('Role')->selectHtmlOption($selected);
            $this->assign('role_option', $option);
            $this->assign('title', '编辑用户');
            return view();
        }
    }

    /**
     * 删除用户
     */
    public function delete()
    {
        $id = $this->request->param('id/d', 0);
        if (\app\console\service\User::getInstance()->isAdmin($id)) {
            $this->error("超级管理员不能删除！");
        }

        $user = model('User');
        if($user->deleteUser($id)){
            $this->result('', 1, '删除成功！', 'json');
        }else{
            $error = $user->getError() ? $user->getError() : '删除失败!';
            $this->result('', 0, $error, 'json');
        }
    }

    public function validUserIsExist()
    {
        var_dump(input());die;
    }
}