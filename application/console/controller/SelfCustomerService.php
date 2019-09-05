<?php
namespace app\console\controller;

class SelfCustomerService extends ConsoleBase{
    public function index(){
//        $model = model("SelfCustomerService");
//        $list = $model->getSelfCustomerServiceList();
//        $this->assign('list' , $list);
        return view();
    }
    public function read(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));

        $kf_nick = $this->request->get('kf_nick', '');

        $customer_service = model('SelfCustomerService');
        //搜索条件
        if('' !== $kf_nick) {
            $customer_service->where('kf_nick','like','%'.$kf_nick.'%');
        }
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $customer_service->order($_order);
        }
        if (!\app\console\service\User::getInstance()->isAdmin()) {
            $customer_service->where('type','eq','');
        }
       // $customer_service->where('status','neq',-1);
        $customer_service->field('SQL_CALC_FOUND_ROWS *');
        $list = $customer_service->limit($start, $length)->select();
        $result = $customer_service->query('SELECT FOUND_ROWS() as count');
        $total = $result[0]['count'];
        $data = [];
        $library = model("MaterialLibrary");
        $user = model("User");
        foreach ($list as $key => $item) {
            $info = $user->getUserById($item['uid']);
            $list[$key]['uid'] = $info['real_name'];
            $headimgurl = $library->getMaterialInfoById($item['kf_headimgurl']);
            $list[$key]['kf_headimgurl'] = $headimgurl['url_thumb'];
            $data[] = $item->toArray();
        }
        $return['data'] = $data;
        $return['recordsFiltered'] = $total;
        $return['recordsTotal'] = $total;

        echo json_encode($return);
    }
    public function add(){
        if(request()->isPost()){
            $customservice = model('SelfCustomerService');
            if($customservice->createSelfCustomerService(input())){
                $data['url'] = url('SelfCustomerService/index');
                $this->result($data, 1, '添加客服成功', 'json');
            }else{
                $error = $customservice->getError() ? $customservice->getError() : '添加客服失败';
                $this->result('', 0, $error, 'json');
            }
        }else{
            $model = model("User");
            $list = $model->where(['status' => 1 , 'is_binding_customer' => -1])->select();
            $this->assign('list' , $list);
            $this->assign('weixin' , config("weixin"));
            return view();
        }
    }
    public function edit(){
        if(request()->isPost()){
            $customservice = model('SelfCustomerService');
            if($customservice->createSelfCustomerService(input())){
                $data['url'] = url('SelfCustomerService/index');
                $this->result($data, 1, '修改客服成功', 'json');
            }else{
                $error = $customservice->getError() ? $customservice->getError() : '修改客服失败';
                $this->result('', 0, $error, 'json');
            }
        }else{
            $id = $this->request->param('id/d');
            $this->assign('kefu' , config("kefu"));
            $model = model("SelfCustomerService");
            $info = $model->getSelfCustomerServiceInfoById($id);
            $info['kf_account'] = strstr($info['kf_account'], '@', true);
            $model = model("MaterialLibrary");
            $kf_headimgurl = $model->getMaterialInfoById($info['kf_headimgurl']);
            $info['kf_headimgurl_url'] = $kf_headimgurl['name'];
            $this->assign('info' , $info);
            return view();
        }
    }
    public function delete(){
        $id = $this->request->param('id/d');
        $model = model("SelfCustomerService");
        if($result = $model->deleteCustomerService($id)){
            $data['url'] = url('SelfCustomerService/index');
            $this->result($data, 1, '禁用客服成功', 'json');
        } else {
            $error = $model->getError() ? $model->getError() : '禁用客服失败';
            $this->result('', 0, $error, 'json');
        }
    }
    public function recover()
    {
        $id = $this->request->param('id/d');
        $model = model("SelfCustomerService");
        if($result = $model->recoverCustomerService($id)){
            $data['url'] = url('SelfCustomerService/index');
            $this->result($data, 1, '启用客服成功', 'json');
        } else {
            $error = $model->getError() ? $model->getError() : '启用客服失败';
            $this->result('', 0, $error, 'json');
        }
    }

}
