<?php
namespace app\console\controller;

class Activity extends ConsoleBase{
    public function index(){
//                $list = db("through_default_info")->field("min(create_time) create_time, uid")->group("uid")->select();
//        foreach ($list as $key => $value) {
//            $info = db("member")->where(['uid' => $value['uid']])->find();
//            db("integral_record")->where(['openId' => $info['openId']])->update(['through' => date('Y-m-d H:i:s' , $value['create_time'])]);
//        }
//        
        return view();
    }
    public function read(){
        $return = [];
        $order  = $this->request->get('order/a', []);
        $start  = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));

        $create_date1 = $this->request->get('create_date1', '');
        $create_date2 = $this->request->get('create_date2', '');
        $is_show       = $this->request->get('is_show', '');
        $act_name     = $this->request->get('act_name', '');

        $activity = model('Activity');
        //搜索条件
        if('' !== $create_date1 && '' !== $create_date2) {
            $activity->where('create_time','between',[strtotime($create_date1.'00:00:00') , strtotime($create_date2.'23:59:59')]);
        }elseif ('' !== $create_date1 && '' == $create_date2) {
            $activity->where('create_time','>',strtotime($create_date1.'00:00:00'));
        }elseif ('' == $create_date1 && '' !== $create_date2) {
            $activity->where('create_time','<',strtotime($create_date2.'23:59:59'));
        }
        if('' !== $is_show) {
            $activity->where(['is_show'=>$is_show]);
        }
        if('' !== $act_name) {
            $activity->where('act_name','like','%'.$act_name.'%');
        }
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $activity->order($_order);
        }
        $activity->field('SQL_CALC_FOUND_ROWS *');
        $list   = $activity->limit($start, $length)->select();
        $result = $activity->query('SELECT FOUND_ROWS() as count');
        $total  = $result[0]['count'];
        
        $return['data']             = $list;
        $return['recordsFiltered']  = $total;
        $return['recordsTotal']     = $total;

        echo json_encode($return);
    }
    public function add(){
		$id = input("id");
		if($id == 1){
			if(request()->isPost()){
				$activity = model('Activity');
				if($activity->createActivity(input(),$id)){
					$data['url'] = url('Activity/index');
					$this->result($data, 1, '已保存为草稿', 'json');
				}else{
					$error = $activity->getError() ? $activity->getError() : '保存为草稿失败';
					$this->result('', 0, $error, 'json');
				}
			}else{
				return view();
			}
		}else{
			if(request()->isPost()){
				$activity = model('Activity');
				if($activity->createActivity(input(),'')){
					$data['url'] = url('Activity/index');
					$this->result($data, 1, '添加活动成功', 'json');
				}else{
					$error = $activity->getError() ? $activity->getError() : '添加活动失败';
					$this->result('', 0, $error, 'json');
				}
			}else{
				return view();
			}
		}
    }
    public function edit(){
        if(request()->isPost()){
            $activity = model('Activity');
            if($activity->createActivity(input(),'')){
                $data['url'] = url('Activity/index');
                $this->result($data, 1, '修改活动成功', 'json');
            }else{
                $error = $activity->getError() ? $activity->getError() : '修改活动失败';
                $this->result('', 0, $error, 'json');
            }
        }else{
            $id         = $this->request->param('a_id/d');
            $model      = model("Activity");
            $info       = $model->getActivityInfoById($id);
            $model      = model("MaterialLibrary");
            $top_img    = $model->getMaterialInfoById($info['top_img']);
            
            $info['top_img_name']       = $top_img['name'];
            $info['sign_start_time']    = date('Y-m-d H:i' , $info['sign_start_time']);
            $info['sign_end_time']      = date('Y-m-d H:i' , $info['sign_end_time']);
            $this->assign('info' , $info);
            return view();
        }
    }
    public function stop(){
        $id = $this->request->param('id/d', 0);
        $model = model('Activity');
        if($model->stopActivity($id)){
            $this->result('', 1, '下架该活动成功！', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '下架该活动失败!';
            $this->result('', 0, $error, 'json');
        }
    }
    public function start(){
        $id = $this->request->param('id/d', 0);
        $model = model('Activity');
        if($model->startActivity($id)){
            $this->result('', 1, '上架活动成功！', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '上架活动失败!';
            $this->result('', 0, $error, 'json');
        }
    }
    public function stopSign(){
        $id = $this->request->param('id/d', 0);
        $model = model('Activity');
        if($model->stopActivitySign($id)){
            $this->result('', 1, '停用报名成功！', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '停用报名失败!';
            $this->result('', 0, $error, 'json');
        }
    }
    public function startSign(){
        $id = $this->request->param('id/d', 0);
        $model = model('Activity');
        if($model->startActivitySign($id)){
            $this->result('', 1, '启用报名成功！', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '启用报名失败!';
            $this->result('', 0, $error, 'json');
        }
    }
    
    public function showDisabled(){
        $id = $this->request->param('id/d', 0);
        $model = model('Activity');
        if($model->showActivityDisabled($id)){
            $this->result('', 1, '活动下线成功！', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '活动下线失败!';
            $this->result('', 0, $error, 'json');
        }
    }
    
    /**
     * 获取活动信息
     */
    public function detail(){
        $id     = $this->request->param('id/d');
        
        //获取活动详细信息
        $model  = model("Activity");
        $info   = $model->getActivityInfoById($id);
        if($info['is_show']==1){
            $info['show_desc'] = "上架中";
        }else{
            $info['show_desc'] = "下架中";
        }
        $info['sign_start_time']    = date('Y-m-d H:i' , $info['sign_start_time']);
        $info['sign_end_time']      = date('Y-m-d H:i' , $info['sign_end_time']);
        //获取活动报名人员信息
        $model  = model("Order");
        $list   = $model->getOrderListByActivity($id);
        
        $this->assign('list' , $list);
        $this->assign("info" , $info);
        return view();
    }
    public function uploads(){
        $file = request()->file('file');
        $path = ROOT_PATH . 'public' . DS . 'uploads' . DS . 'activity' . DS . 'img';
        if(!is_dir($path)){
            mkdir($path , 0777, true);
        }
        $result = $file->move($path);
        $date = substr($result->getSaveName(), 0 , 8);
        if($result){
            $data['name'] = $result->getFilename();
            $data['url']  = 'http://' . $_SERVER['SERVER_NAME'] . DS . 'uploads' . DS . 'activity' . DS . 'img' . DS . $date . DS . $data['name'];
            $this->result($data ,1 , '上传成功' , 'json');
        }else{
            $this->result('', 0, $file->getError(), 'json');
        }
    }
}