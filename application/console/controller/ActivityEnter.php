<?php
namespace app\console\controller;

class ActivityEnter extends ConsoleBase{
    public function index(){
        $activity = model("Activity");
        $activity->where('a_id','>','54');
        //$orders->where(['a_id' , 'gt' , '54']);
        $list = $activity->order("a_id desc")->select();
        $this->assign('list' , $list);
        return view();
    }
    
    public function read(){
        $return = [];
        $order  = $this->request->get('order/a', []);
        $start  = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));
        
        $a_id         = $this->request->get('a_id', '');
        $create_date1 = $this->request->get('start_time', '');
        $create_date2 = $this->request->get('end_time', '');
        $review       = $this->request->get('review', '');
        
        
        $orders   = model('Order');
        
        $orders->where(['type'=>'activity']);
        $orders->where(['is_pay'=>1]);
        
        if('' !== $a_id) {
            $orders->where(['id'=>$a_id]);
        }else{
            $orders->where('id','>','54');
        }
        
        if('' !== $review) {
            $orders->where(['review'=>$review]);
        }
        
        if('' !== $create_date1 && '' !== $create_date2) {
            $orders->where('create_time','between',[strtotime($create_date1.'00:00:00') , strtotime($create_date2.'23:59:59')]);
        }elseif ('' !== $create_date1 && '' == $create_date2) {
            $orders->where('create_time','>',strtotime($create_date1.'00:00:00'));
        }elseif ('' == $create_date1 && '' !== $create_date2) {
            $orders->where('create_time','<',strtotime($create_date2.'23:59:59'));
        }
        
        
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $orders->order($_order);
        }
        
        $orders->field('SQL_CALC_FOUND_ROWS *');
        $list   = $orders->limit($start, $length)->select();
        $result = $orders->query('SELECT FOUND_ROWS() as count');
        $total  = $result[0]['count'];
        $data   = [];
        foreach ($list as $key => $item) {
            $model  = model("Member");
            $user   = $model->getMemberInfoById($item['uid']);
            $list[$key]['uid']       = $user['uid'];
            $list[$key]['userName']  = $user['userName'];
            $list[$key]['is_follow'] = $user['is_follow'];
            $data[] = $item->toArray();
        }
        $return['data']             = $data;
        $return['recordsFiltered']  = $total;
        $return['recordsTotal']     = $total;
        echo json_encode($return);
    }
    
    /**
     * 更新报名的会员信息到会员表
     */
    public function updatem(){
        $data   = input();
        $order  = model('Order')->getOrderInfoById($data['id']);
        $model  = model("Member");
        $info   = $model->getMemberInfoById($order['uid']);
        
        $update = [];
        //微信
        if($order['introduce']!==$info['weixin']){
            $update['weixin']       = $order['introduce'];
        }
        
        //公司
        if($order['company_jc']!==$info['company_jc']){
            $update['company_jc']   = $order['company_jc'];
            $update['company']      = $order['company_jc'];
            //职位
            $update['position']     = $order['position'];
        }
        
        //真实姓名
        if($order['name']!==$info['realName']){
            $update['realName']     = $order['name'];
        }
        if(!empty($update)){
            $res = model("Member")->where(['uid' => $order['uid']])->update($update);
            if($res){
                $this->result('', 1, '更新成功', 'json');
            }else{
                $this->result('', 0, '更新失败，请稍后再试', 'json');
            }
            
        }else{
            $this->result('', 0, '会员信息已经同步，无需更新', 'json');
        } 
    }
    
    public function detail(){
        $id    = $this->request->param('id/d');
        $info  = model('Order')->getOrderInfoById($id);
        if($info['review']==1){
            $info['review_des'] = "通过";
        }else if($info['review']==2){
            $info['review_des'] = "不通过";
        }else{
            $info['review_des'] = "待审核";
        }
        
        $model  = model("Member");
        $user   = $model->getMemberInfoById($info['uid']);
        $info['userName']   = $user['userName'];
        $info['userPhoto']  = $user['userPhoto'];
        if($user['userSex']==0){
            $info['userSex'] = "未知";
        }else if($user['userSex']==1){
            $info['userSex'] = "男";
        }else{
            $info['userSex'] = "女";
        }
        if($user['is_follow']==1){
            $info['is_follow'] = "是";
        }else{
            $info['is_follow'] = "否";
        }
        $this->assign("info" , $info);
        return view();
    }
    
    public function review(){
        $data  = input();
        if($data['o_id']> 0){
            $model = model('Order');
            $res   = $model->postReview($data);
            if($res){
                $data['url'] = url('ActivityEnter/index');
                $this->result($data, 1, '审核提交成功', 'json');
            } else {
                $this->result($data, 0, '审核提交失败', 'json');
            }
        }else{
            $this->result('', 0, '操作异常', 'json');
        }
    }
    
    

}