<?php
namespace app\console\controller;

class Zryxes extends ConsoleBase{
    public function index(){
        $list = model("ZryxesEffect")->where(['zryx_financing' => 0])->select();
        $now_time = time();
        $all_time = 3600*24*90;
        $str = '';
        foreach ($list as $key => $value) {
            $time = intval($now_time)-intval(strtotime($value['create_time']));
            if($time > $all_time){
                $info = model("ZryxesEffect")->setFinancingById($value['id']);
                if(!$info){
                    $str .= $value['id'].',';
                }
            }
        }
        if($str != ''){
            echo '此编号项目未自动设置下架,请注意!'.trim($str, ',');
        }
        return view();
    }
    public function sucai(){
        return view();
    }
	public function dazong_client(){
		$return = [];
        //$order = $this->request->get('order/a', []);
        //$start = $this->request->get('start', 0);
        //$length = $this->request->get('length', config('paginate.list_rows'));

        $create_date1 = $this->request->get('create_date1', '');
        $create_date2 = $this->request->get('create_date2', '');
        $zryx_exhibition = $this->request->get('zryx_exhibition', '');
        $zryx_financing = $this->request->get('zryx_financing', '');
        $zryx_sign = $this->request->get('zryx_sign', '');
        $sec_uri_tyshortname = $this->request->get('sec_uri_tyshortname', '');
        $secucode = $this->request->get('secucode', '');

        $zryxes = model('ZryxesEffect')
                ->alias("ze")
                ->join("QrcodeDirection qd","ze.qr_code=qd.id","LEFT")
                ->field("SQL_CALC_FOUND_ROWS ze.*,qd.qrcode_path");
        //搜索条件
        if('' !== $create_date1 && '' !== $create_date2) {
            $zryxes->where('create_time','between',[strtotime($create_date1.'00:00:00') , strtotime($create_date2.'23:59:59')]);
        }elseif ('' !== $create_date1 && '' == $create_date2) {
            $zryxes->where('create_time','>',strtotime($create_date1.'00:00:00'));
        }elseif ('' == $create_date1 && '' !== $create_date2) {
            $zryxes->where('create_time','<',strtotime($create_date2.'23:59:59'));
        }
        //搜索条件
        if('' !== $zryx_exhibition) {
            $zryxes->where('zryx_exhibition','eq',$zryx_exhibition);
        }
        if('' !== $zryx_financing) {
            $zryxes->where('zryx_financing','eq',$zryx_financing);
        }
        if('' !== $zryx_sign) {
            $zryxes->where('zryx_sign','eq',$zryx_sign);
        }
        if('' !== $sec_uri_tyshortname) {
            $zryxes->where('sec_uri_tyshortname','like','%'.$sec_uri_tyshortname.'%');
        }
        if('' !== $secucode) {
            $zryxes->where('secucode','like','%'.$secucode.'%');
        }
		$zryxes->order('id desc');
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $zryxes->order($_order);
        }
        $list = $zryxes->select();
        $result = $zryxes->query('SELECT FOUND_ROWS() as count');
        $total = $result[0]['count'];
        $data = [];
        foreach ($list as $key => $item) {
            $data[] = $item->toArray();
        }
		if(project_show($data,2)){
			return "ok";
		}else{
			return "no";
		}
	}
    public function read(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));

        $create_date1 = $this->request->get('create_date1', '');
        $create_date2 = $this->request->get('create_date2', '');
        $zryx_exhibition = $this->request->get('zryx_exhibition', '');
        $zryx_financing = $this->request->get('zryx_financing', '');
        $zryx_sign = $this->request->get('zryx_sign', '');
        $sec_uri_tyshortname = $this->request->get('sec_uri_tyshortname', '');
        $secucode = $this->request->get('secucode', '');

        $zryxes = model('ZryxesEffect')
                ->alias("ze")
                ->join("QrcodeDirection qd","ze.qr_code=qd.id","LEFT")
                ->field("SQL_CALC_FOUND_ROWS ze.*,qd.qrcode_path");
        //搜索条件
        if('' !== $create_date1 && '' !== $create_date2) {
            $zryxes->where('create_time','between',[strtotime($create_date1.'00:00:00') , strtotime($create_date2.'23:59:59')]);
        }elseif ('' !== $create_date1 && '' == $create_date2) {
            $zryxes->where('create_time','>',strtotime($create_date1.'00:00:00'));
        }elseif ('' == $create_date1 && '' !== $create_date2) {
            $zryxes->where('create_time','<',strtotime($create_date2.'23:59:59'));
        }
        //搜索条件
        if('' !== $zryx_exhibition) {
            $zryxes->where('zryx_exhibition','eq',$zryx_exhibition);
        }
        if('' !== $zryx_financing) {
            $zryxes->where('zryx_financing','eq',$zryx_financing);
        }
        if('' !== $zryx_sign) {
            $zryxes->where('zryx_sign','eq',$zryx_sign);
        }
        if('' !== $sec_uri_tyshortname) {
            $zryxes->where('sec_uri_tyshortname','like','%'.$sec_uri_tyshortname.'%');
        }
        if('' !== $secucode) {
            $zryxes->where('secucode','like','%'.$secucode.'%');
        }
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $zryxes->order($_order);
        }
//        $zryxes->field('SQL_CALC_FOUND_ROWS *');
        $list = $zryxes->limit($start, $length)->select();
        $result = $zryxes->query('SELECT FOUND_ROWS() as count');
        $total = $result[0]['count'];
        $data = [];
        foreach ($list as $key => $item) {
            $data[] = $item->toArray();
        }
        $return['data'] = $data;
        $return['recordsFiltered'] = $total;
        $return['recordsTotal'] = $total;

        echo json_encode($return);
    }
    public function sucaiRead(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));

        $create_date1 = $this->request->get('create_date1', '');
        $create_date2 = $this->request->get('create_date2', '');
        $sec_uri_tyshortname = $this->request->get('sec_uri_tyshortname', '');
        $secucode = $this->request->get('secucode', '');

        $zryxes = model('Zryxes')
                ->alias("z")
                ->join("CaijiStatus cs" , "z.neeq=cs.neeq","LEFT")
                ->field("SQL_CALC_FOUND_ROWS z.*,cs.zryxes_status")
                ->order("cs.zryxes_status asc,z.enddate desc");
        //搜索条件
        if('' !== $create_date1 && '' !== $create_date2) {
            $zryxes->where('created_at','between',[$create_date1 , $create_date2]);
        }elseif ('' !== $create_date1 && '' == $create_date2) {
            $zryxes->where('created_at','>',$create_date1);
        }elseif ('' == $create_date1 && '' !== $create_date2) {
            $zryxes->where('created_at','<',$create_date2);
        }
        //搜索条件
        if('' !== $sec_uri_tyshortname) {
            $zryxes->where('sec_uri_tyshortname','like','%'.$sec_uri_tyshortname.'%');
        }
        if('' !== $secucode) {
            $zryxes->where('secucode','like','%'.$secucode.'%');
        }
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $zryxes->order($_order);
        }
//        $zryxes->field('SQL_CALC_FOUND_ROWS *');
        $list = $zryxes->limit($start, $length)->select();
        $result = $zryxes->query('SELECT FOUND_ROWS() as count');
        $total = $result[0]['count'];
        $data = [];
        foreach ($list as $key => $item) {
            $data[] = $item->toArray();
        }
        $return['data'] = $data;
        $return['recordsFiltered'] = $total;
        $return['recordsTotal'] = $total;

        echo json_encode($return);
    }
    public function sucaiDetail(){
        $id = $this->request->param('id/d');
        $model = model("Zryxes");
        $info = $model->getZryxesInfoById($id);
        $this->assign("info" , $info);
        $this->assign("date" , substr($info['created_at'], 0 , 10).'转让');
        return view();
    }
    public function release(){
        $id = $this->request->param('id/d');
        $model = model("Zryxes");
        if($id = $model->releaseZryxes($id)){
            $data['url'] = url('Zryxes/index');
//            $data['url'] = url('Zryxes/detail' , ['id' => $id]);
            $this->result($data, 1, '发布成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '发布失败';
            $this->result('', 0, $error, 'json');
        }
    }
    public function exhibition_true(){
        $id = $this->request->param('id/d');
        $model = model("ZryxesEffect");
        if($model->setZryxesEffectExhibitionTrue($id)){
            $data['url'] = url('Zryxes/detail' , ['id' => $id]);
            $this->result($data, 1, '展示成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '展示失败';
            $this->result('', 0, $error, 'json');
        }
    }
    public function exhibition_false(){
        $id = $this->request->param('id/d');
        $model = model("ZryxesEffect");
        if($model->setZryxesEffectExhibitionFalse($id)){
            $data['url'] = url('Zryxes/detail' , ['id' => $id]);
            $this->result($data, 1, '取消展示成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '取消展示失败';
            $this->result('', 0, $error, 'json');
        }
    }
    public function detail(){
        $id = $this->request->param('id/d');
        $model = model("ZryxesEffect");
        $info = $model->getZryxesEffectInfoById($id);
        //大宗头图
        $img_url = model('MaterialLibrary')->getMaterialInfoById($info['img_id']);
        if (!empty($img_url)) {
            $info['img_url'] = config('view_replace_str')['__DOMAIN__'].$img_url['url']; 
        } else {
            $info['img_url'] = '';
        }
        //大宗头图
        $this->assign("info" , $info);
        $this->assign("date" , substr($info['created_at'], 0 , 10).'转让');
        return view();
    }
    public function detail2(){
        $id = $this->request->param('id/d');
        $model = model("ZryxesEffect");
        $info = $model->getZryxesEffectInfoById($id);
        $this->assign("info" , $info);
        $this->assign("id" , $id);
        $this->assign("date" , substr($info['created_at'], 0 , 10).'转让');
        return view();
    }
    public function add(){
        if(request()->isPost()){
            $data = input();
            $model = model('ZryxesEffect');
            if($model->createZryxesEffect($data)){
                $data['url'] = url('Zryxes/index');
                $this->result($data, 1, '添加大宗成功', 'json');
            }else{
                $error = $model->getError() ? $model->getError() : '添加大宗失败';
                $this->result('', 0, $error, 'json');
            }
        }else{
            return view();
        }
    }
    public function edit(){
        if(request()->isPost()){
            $data = input();
            $model = model('ZryxesEffect');
            if($model->createZryxesEffect($data)){
                $data['url'] = url('Zryxes/detail' , ['id' => $data['id']]);
                $this->result($data, 1, '修改大宗项目成功', 'json');
            }else{
                $error = $model->getError() ? $model->getError() : '修改大宗项目失败';
                $this->result('', 0, $error, 'json');
            }
        }else{
            $id = $this->request->param('id/d', 0);
            $info = model("ZryxesEffect")->getZryxesEffectInfoById($id);
            //大宗头图
            $url = model('MaterialLibrary')->getMaterialInfoById($info['img_id']);
            if (!empty($url)) {
                $info['img_name'] = $url['name'];
            } else {
                $info['img_name'] = '';
            }
            
            //大宗头图
            $info['enddate'] = date('Y-m-d' , strtotime($info['enddate']));
            $this->assign('info' , $info);
            return view();
        }
    }
    public function settop(){
        return view();
    }
    public function settopread(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));
        $sec_uri_tyshortname = $this->request->get('sec_uri_tyshortname', '');
        $secucode = $this->request->get('secucode', '');
        $zryxes = model('ZryxesEffect');
        //搜索条件
        if('' !== $sec_uri_tyshortname) {
            $zryxes->where('sec_uri_tyshortname','like','%'.$sec_uri_tyshortname.'%');
        }
        if('' !== $secucode) {
            $zryxes->where('secucode','like','%'.$secucode.'%');
        }
        $zryxes->where(['zryx_exhibition' => 1 , 'zryx_financing' => 0]);
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $zryxes->order($_order);
        }
        $zryxes->field('SQL_CALC_FOUND_ROWS *');
        $list = $zryxes->limit($start, $length)->select();
        $result = $zryxes->query('SELECT FOUND_ROWS() as count');
        $total = $result[0]['count'];
        $data = [];
        foreach ($list as $key => $item) {
            $data[] = $item->toArray();
        }
        $return['data'] = $data;
        $return['recordsFiltered'] = $total;
        $return['recordsTotal'] = $total;
        echo json_encode($return);
    }
    public function stick_true(){
        $id = $this->request->param('id/d');
        $model = model("ZryxesEffect");
        if($model->setZryxesEffectTopTrue($id)){
            $data['url'] = url('Zryxes/settop');
            $this->result($data, 1, '置顶成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '置顶失败';
            $this->result('', 0, $error, 'json');
        }
    }
    public function stick_false(){
        $id = $this->request->param('id/d');
        $model = model("ZryxesEffect");
        if($model->setZryxesEffectTopFalse($id)){
            $data['url'] = url('Zryxes/settop');
            $this->result($data, 1, '取消置顶成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '取消置顶失败';
            $this->result('', 0, $error, 'json');
        }
    }
    public function boutique(){
        $id = $this->request->param('id/d');
        $model = model("Project");
        if($result = $model->createBoutiqueProject($id)){
            $data['url'] = url('Project/detail' , ['id' => $result]);
            $this->result($data, 1, '致精成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '致精失败';
            $this->result('', 0, $error, 'json');
        }
    }
    public function stop_boutique(){
        $id = $this->request->param('id/d');
        $model = model("Project");
        if($result = $model->stopBoutiqueProject($id)){
            $data['url'] = url('Project/detail' , ['id' => $result]);
            $this->result($data, 1, '取消致精成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '取消致精失败';
            $this->result('', 0, $error, 'json');
        }
    }
    /**
     * @return \think\response\View
     * @param:推送消息
     * @time:2018/5/29
     */
    public function sendinfo(){
        $id = $this->request->param('id/d');
        $uid = $this->request->post('uid/d');
        $openId = $this->request->post('openId');
        $userType = $this->request->post('userType');
        $name = $this->request->post('name');
        $model = model("ZryxesEffect");
        if($model->sendProjectInfo($id,$uid,$openId,$userType,$name)){
            $data['code'] = 200;
            $this->result($data, 200, '推送成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '推送失败';
            $data['code'] = 400;
            $this->result($data, 400, $error, 'json');
        }
    }
}