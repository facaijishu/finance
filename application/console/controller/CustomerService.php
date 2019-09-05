<?php
namespace app\console\controller;

class CustomerService extends ConsoleBase{
    public function index(){
        $model = model("CustomerService");
        $list = $model->getCustomerServiceList();
        $this->assign('list' , $list);
        return view();
    }
    public function read(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));

        $kf_nick = $this->request->get('kf_nick', '');

        $customer_service = model('CustomerService');
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
        $customer_service->where('status','neq',-1);
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
            $code = $library->getMaterialInfoById($item['kf_qr_code']);
            $list[$key]['kf_qr_code'] = $code['url_thumb'];
            $list[$key]['createTime'] = date('Y-m-d H:i:s' , $item['createTime']);
            $data[] = $item->toArray();
        }
        $return['data'] = $data;
        $return['recordsFiltered'] = $total;
        $return['recordsTotal'] = $total;

        echo json_encode($return);
    }
    public function add(){
        if(request()->isPost()){
            $customservice = model('CustomerService');
            if($customservice->createCustomerService(input())){
                $data['url'] = url('CustomerService/index');
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
            $customservice = model('CustomerService');
            if($customservice->createCustomerService(input())){
                $data['url'] = url('CustomerService/index');
                $this->result($data, 1, '修改客服成功', 'json');
            }else{
                $error = $customservice->getError() ? $customservice->getError() : '修改客服失败';
                $this->result('', 0, $error, 'json');
            }
        }else{
            $id = $this->request->param('id/d');
            $this->assign('weixin' , config("weixin"));
            $model = model("CustomerService");
            $info = $model->getCustomerServiceInfoById($id);
            $info['kf_account'] = strstr($info['kf_account'], '@', true);
            $model = model("MaterialLibrary");
            $kf_headimgurl = $model->getMaterialInfoById($info['kf_headimgurl']);
            $info['kf_headimgurl_url'] = $kf_headimgurl['name'];
            $kf_qr_code = $model->getMaterialInfoById($info['kf_qr_code']);
            $info['kf_qr_code_url'] = $kf_qr_code['name'];
            $this->assign('info' , $info);
            return view();
        }
    }
    public function delete(){
        $id = $this->request->param('id/d');
        $model = model("CustomerService");
        if($result = $model->deleteCustomerService($id)){
            $data['url'] = url('CustomerService/index');
            $this->result($data, 1, '删除客服成功', 'json');
        } else {
            $error = $model->getError() ? $model->getError() : '删除客服失败';
            $this->result('', 0, $error, 'json');
        }
    }

    public function binding(){
        $model = model("CustomerService");
        if($model->waitBindingCustomer(input())){
            $data['url'] = url('CustomerService/index');
            $this->result($data, 1, '客服邀请绑定成功', 'json');
        } else {
            $error = $model->getError() ? $model->getError() : '客服邀请绑定失败';
            $this->result('', 0, $error, 'json');
        }
    }

    public function synchronization(){
        $url = 'https://api.weixin.qq.com/cgi-bin/customservice/getkflist?access_token='. getAccessTokenFromFile();
        $result = httpGet($url);
        $result = (array)json_decode($result);
//        var_dump($result);
        $model = model("CustomerService");
        if($model->synchronizationCustomerService($result['kf_list'])){
            $data['url'] = url('CustomerService/index');
            $this->result($data, 1, '客服同步成功', 'json');
        } else {
            $error = $model->getError() ? $model->getError() : '客服同步失败';
            $this->result('', 0, $error, 'json');
        }
    }
}