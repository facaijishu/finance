<?php
namespace app\console\controller;

class Zfmxes extends ConsoleBase{
    public function index(){
        $list = model("ZfmxesAdd")->where(['zfmx_examine' => 1 , 'zfmx_financing' => 0])->select();
        $now_time = time();
        $all_time = 3600*24*90;
        $str = '';
        foreach ($list as $key => $value) {
            $time = intval($now_time)-intval(strtotime($value['create_time']));
            if($time > $all_time){
                $info = model("ZfmxesAdd")->setFinancingById($value['id']);
                if(!$info){
                    $str .= $value['id'].',';
                }
            }
        }
        if($str != ''){
            echo '此编号项目未自动设置下架,请注意!'.trim($str, ',');
        }
//        $list = model("QrcodeDirection")->order("id asc")->select();
//        $str = '';
//        foreach ($list as $key => $value) {
//            $info = model("QrcodeDirection")->addQrCode($value);
//            if(!$info){
//                $str .= $value['id'].'-';
//            }
//        }
        return view();
    }
    public function already(){
        return view();
    }
	public function dingzeng_export(){
		$return = [];
        //$order = $this->request->get('order/a', []);
        //$start = $this->request->get('start', 0);
        //$length = $this->request->get('length', config('paginate.list_rows'));
		
        $create_date1 = $this->request->get('create_date1', '');
        $create_date2 = $this->request->get('create_date2', '');
        $zfmx_examine = $this->request->get('zfmx_examine', '');
        $zfmx_exhibition = $this->request->get('zfmx_exhibition', '');
        $zfmx_financing = $this->request->get('zfmx_financing', '');
        $zfmx_sign = $this->request->get('zfmx_sign', '');
//        $sec_uri_tyshortname = $this->request->get('sec_uri_tyshortname', '');
        $neeq = $this->request->get('neeq', '');
        $zfmxes = model('ZfmxesAdd')
            ->alias("za")
//                ->join("Zfmxes z" , "za.neeq=z.neeq and za.plannoticeddate=z.plannoticeddate","LEFT")
            ->join("QrcodeDirection qd","za.qr_code=qd.id","LEFT")
            ->field("SQL_CALC_FOUND_ROWS za.*,qd.qrcode_path")
            ->where("za.zfmx_examine","eq",1)
            ->order("za.stick desc,za.id desc");
        //搜索条件
        if('' !== $create_date1 && '' !== $create_date2) {
            $zfmxes->where('create_time','between',[strtotime($create_date1.'00:00:00') , strtotime($create_date2.'23:59:59')]);
        }elseif ('' !== $create_date1 && '' == $create_date2) {
            $zfmxes->where('create_time','>',strtotime($create_date1.'00:00:00'));
        }elseif ('' == $create_date1 && '' !== $create_date2) {
            $zfmxes->where('create_time','<',strtotime($create_date2.'23:59:59'));
        }
        //搜索条件
        if('' !== $zfmx_examine) {
            if($zfmx_examine == 0){
                $zfmxes->where('zfmx_examine is null');
            } else {
                $zfmxes->where('zfmx_examine','eq',1);
            }
        }
        if('' !== $zfmx_exhibition) {
            $zfmxes->where('za.zfmx_exhibition','eq',$zfmx_exhibition);
        }
        if('' !== $zfmx_financing) {
            $zfmxes->where('za.zfmx_financing','eq',$zfmx_financing);
        }
        if('' !== $zfmx_sign) {
            $zfmxes->where('za.zfmx_sign','eq',$zfmx_sign);
        }

        if('' !== $neeq) {
            $zfmxes->where('neeq','like','%'.$neeq.'%');
        }
        
        $list = $zfmxes->select();
        $data = [];
        $model = model("Zfmxes");
        foreach ($list as $key => $item) {
            $info = $model->where(['neeq' => $item['neeq'] , 'plannoticeddate' => $item['plannoticeddate']])->find();
            $list[$key]['sec_uri_tyshortname'] = $info['sec_uri_tyshortname'];
            $list[$key]['msecucode'] = $info['msecucode'];
            $list[$key]['sublevel'] = $info['sublevel'];
            $list[$key]['trademethod'] = $info['trademethod'];
            $list[$key]['r_id'] = $info['id'];
            $data[] = $item->toArray();
        }
        //$return['data'] = $data;
        //$return['recordsFiltered'] = $total;
        //$return['recordsTotal'] = $total;
		if(project_show($data,1)){
			return "ok";
		}else{
			return "no";
		}
        //echo json_encode($return);
	}
    public function read(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));

        $create_date1 = $this->request->get('create_date1', '');
        $create_date2 = $this->request->get('create_date2', '');
        $zfmx_examine = $this->request->get('zfmx_examine', '');
        $zfmx_exhibition = $this->request->get('zfmx_exhibition', '');
        $zfmx_financing = $this->request->get('zfmx_financing', '');
        $zfmx_sign = $this->request->get('zfmx_sign', '');
        $sec_uri_tyshortname = $this->request->get('sec_uri_tyshortname', '');
        $msecucode = $this->request->get('msecucode', '');

        $zfmxes = model('Zfmxes')
            ->alias("z")
            ->join("ZfmxesAdd za" , "za.neeq=z.neeq and za.plannoticeddate=z.plannoticeddate","LEFT")
            ->field("SQL_CALC_FOUND_ROWS z.*,za.zfmx_examine,za.zfmx_exhibition,za.zfmx_financing,za.zfmx_sign")
            ->where("za.zfmx_examine is null");
        //搜索条件
        if('' !== $create_date1 && '' !== $create_date2) {
            $zfmxes->where('created_at','between',[strtotime($create_date1.'00:00:00') , strtotime($create_date2.'23:59:59')]);
        }elseif ('' !== $create_date1 && '' == $create_date2) {
            $zfmxes->where('created_at','>',strtotime($create_date1.'00:00:00'));
        }elseif ('' == $create_date1 && '' !== $create_date2) {
            $zfmxes->where('created_at','<',strtotime($create_date2.'23:59:59'));
        }
        //搜索条件
        if('' !== $zfmx_examine) {
            if($zfmx_examine == 0){
                $zfmxes->where('zfmx_examine is null');
            } else {
                $zfmxes->where('zfmx_examine','eq',1);
            }
        }
        if('' !== $zfmx_exhibition) {
            $zfmxes->where('za.zfmx_exhibition','eq',$zfmx_exhibition);
        }
        if('' !== $zfmx_financing) {
            $zfmxes->where('za.zfmx_financing','eq',$zfmx_financing);
        }
        if('' !== $zfmx_sign) {
            $zfmxes->where('za.zfmx_sign','eq',$zfmx_sign);
        }
		
        if('' !== $sec_uri_tyshortname) {
            $zfmxes->where('z.sec_uri_tyshortname','like','%'.$sec_uri_tyshortname.'%');
        }
		
        if('' !== $msecucode) {
            $zfmxes->where('msecucode','like','%'.$msecucode.'%');
        }
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $zfmxes->order($_order);
        }
//        $gszls->field('SQL_CALC_FOUND_ROWS *');
        $list = $zfmxes->limit($start, $length)->select();
        $result = $zfmxes->query('SELECT FOUND_ROWS() as count');
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
    public function alreadyread(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));

        $create_date1 = $this->request->get('create_date1', '');
        $create_date2 = $this->request->get('create_date2', '');
        $zfmx_examine = $this->request->get('zfmx_examine', '');
        $zfmx_exhibition = $this->request->get('zfmx_exhibition', '');
        $zfmx_financing = $this->request->get('zfmx_financing', '');
        $zfmx_sign = $this->request->get('zfmx_sign', '');
//        $sec_uri_tyshortname = $this->request->get('sec_uri_tyshortname', '');
        $neeq = $this->request->get('neeq', '');

        $zfmxes = model('ZfmxesAdd')
            ->alias("za")
//                ->join("Zfmxes z" , "za.neeq=z.neeq and za.plannoticeddate=z.plannoticeddate","LEFT")
            ->join("QrcodeDirection qd","za.qr_code=qd.id","LEFT")
            ->field("SQL_CALC_FOUND_ROWS za.*,qd.qrcode_path")
            ->where("za.zfmx_examine","eq",1)
            ->order("za.stick desc,za.id desc");
        //搜索条件
        if('' !== $create_date1 && '' !== $create_date2) {
            $zfmxes->where('create_time','between',[strtotime($create_date1.'00:00:00') , strtotime($create_date2.'23:59:59')]);
        }elseif ('' !== $create_date1 && '' == $create_date2) {
            $zfmxes->where('create_time','>',strtotime($create_date1.'00:00:00'));
        }elseif ('' == $create_date1 && '' !== $create_date2) {
            $zfmxes->where('create_time','<',strtotime($create_date2.'23:59:59'));
        }
        //搜索条件
        if('' !== $zfmx_examine) {
            if($zfmx_examine == 0){
                $zfmxes->where('zfmx_examine is null');
            } else {
                $zfmxes->where('zfmx_examine','eq',1);
            }
        }
        if('' !== $zfmx_exhibition) {
            $zfmxes->where('za.zfmx_exhibition','eq',$zfmx_exhibition);
        }
        if('' !== $zfmx_financing) {
            $zfmxes->where('za.zfmx_financing','eq',$zfmx_financing);
        }
        if('' !== $zfmx_sign) {
            $zfmxes->where('za.zfmx_sign','eq',$zfmx_sign);
        }
//        if('' !== $sec_uri_tyshortname) {
//            $zfmxes->where('sec_uri_tyshortname','like','%'.$sec_uri_tyshortname.'%');
//        }
        if('' !== $neeq) {
            $zfmxes->where('neeq','like','%'.$neeq.'%');
        }
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $zfmxes->order($_order);
        }
//        $gszls->field('SQL_CALC_FOUND_ROWS *');
        $list = $zfmxes->limit($start, $length)->select();
        $result = $zfmxes->query('SELECT FOUND_ROWS() as count');
        $total = $result[0]['count'];
        $data = [];
        $model = model("Zfmxes");
        foreach ($list as $key => $item) {
            $info = $model->where(['neeq' => $item['neeq'] , 'plannoticeddate' => $item['plannoticeddate']])->find();
            $list[$key]['sec_uri_tyshortname'] = $info['sec_uri_tyshortname'];
            $list[$key]['msecucode'] = $info['msecucode'];
            $list[$key]['sublevel'] = $info['sublevel'];
            $list[$key]['trademethod'] = $info['trademethod'];
            $list[$key]['r_id'] = $info['id'];
            $data[] = $item->toArray();
        }
        $return['data'] = $data;
        $return['recordsFiltered'] = $total;
        $return['recordsTotal'] = $total;

        echo json_encode($return);
    }
    public function detail(){
        $id = $this->request->param('id/d');
        $model = model("Zfmxes");
        $info = $model->getZfmxesInfoById($id);
        //定增头图
        $model = model("ZfmxesAdd");
        $zfmxes_add = $model->getZfmxesAddByCodeAndDate(trim(strstr($info['msecucode'], '.', true) , '.') , $info['plannoticeddate']);
        $img_url = model('MaterialLibrary')->getMaterialInfoById($zfmxes_add['img_id']);
        if (!empty($img_url)) {
            $info['img_url'] = config('view_replace_str')['__DOMAIN__'].$img_url['url']; 
        } else {
            $info['img_url'] = '';
        }
        
        //定增头图
        $this->assign("info" , $info);
        $this->assign("date" , substr($info['created_at'], 0 , 10).'定增');
        $this->assign("neeq" , trim(strstr($info['msecucode'], '.', true) , '.'));
        return view();
    }
    public function edit(){
        if(request()->isPost()){
            $data = input();
            $model = model('ZfmxesAdd');
            if($model->createZfmxesAdd($data)){
                $data['url'] = url('Zfmxes/detail' , ['id' => $data['id']]);
                $this->result($data, 1, '修改增发明细成功', 'json');
            }else{
                $error = $model->getError() ? $model->getError() : '修改增发明细失败';
                $this->result('', 0, $error, 'json');
            }
        }else{
            $id = $this->request->param('id/d', 0);
            $info = model("Zfmxes")->getZfmxesSimpleInfo($id);
            $this->assign('neeq' , $info['neeq']);
            $this->assign('plannoticeddate' , $info['plannoticeddate']);
            $model = model("ZfmxesAdd");
            
            $info = $model->getZfmxesAddByCodeAndDate(trim(strstr($info['msecucode'], '.', true) , '.') , $info['plannoticeddate']);
            //定增头图
            $url = model('MaterialLibrary')->getMaterialInfoById($info['img_id']);
            if (!empty($url)) {
                $info['name'] = $url['name'];
            } else {
                $info['name'] = '';
            }
            
            //定增头图
            $this->assign('info' , $info);
            $this->assign('id' , $id);
            return view();
        }
    }
    public function examine(){
        $id = $this->request->param('id/d');
        $model = model("Zfmxes");
        if($model->setZfmxesExamine($id)){
            $data['url'] = url('Zfmxes/detail' , ['id' => $id]);
            $this->result($data, 1, '审核成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '审核失败';
            $this->result('', 0, $error, 'json');
        }
    }
    public function financing(){
        $id = $this->request->param('id/d');
        $model = model("Zfmxes");
        if($model->setZfmxesFinancing($id)){
            $data['url'] = url('Zfmxes/detail' , ['id' => $id]);
            $this->result($data, 1, '融资结束成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '融资结束失败';
            $this->result('', 0, $error, 'json');
        }
    }
    public function exhibition_true(){
        $id = $this->request->param('id/d');
        $model = model("Zfmxes");
        if($model->setZfmxesExhibitionTrue($id)){
            $data['url'] = url('Zfmxes/detail' , ['id' => $id]);
            $this->result($data, 1, '展示成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '展示失败';
            $this->result('', 0, $error, 'json');
        }
    }
    public function exhibition_false(){
        $id = $this->request->param('id/d');
        $model = model("Zfmxes");
        if($model->setZfmxesExhibitionFalse($id)){
            $data['url'] = url('Zfmxes/detail' , ['id' => $id]);
            $this->result($data, 1, '取消展示成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '取消展示失败';
            $this->result('', 0, $error, 'json');
        }
    }
    public function sign(){
        $data = input();
        $model = model("ZfmxesSign");
        if($model->createZfmxesSign($data)){
            $data['url'] = url('Zfmxes/detail' , ['id' => $data['id']]);
            $this->result($data, 1, '签约成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '签约失败';
            $this->result('', 0, $error, 'json');
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
        $msecucode = $this->request->get('msecucode', '');
        $zfmxes = model('Zfmxes')
            ->alias("z")
            ->join("ZfmxesAdd za" , "za.neeq=z.neeq and za.plannoticeddate=z.plannoticeddate","LEFT")
            ->field("SQL_CALC_FOUND_ROWS z.*,za.zfmx_examine,za.zfmx_exhibition,za.zfmx_financing,za.zfmx_sign,za.qr_code,za.stick")
            ->where("za.zfmx_examine","eq",1)
            ->where("za.zfmx_exhibition","eq",1)
            ->where("za.zfmx_financing","eq",0);
        if('' !== $sec_uri_tyshortname) {
            $zfmxes->where('z.sec_uri_tyshortname','like','%'.$sec_uri_tyshortname.'%');
        }
        if('' !== $msecucode) {
            $zfmxes->where('msecucode','like','%'.$msecucode.'%');
        }
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $zfmxes->order($_order);
        }
        $list = $zfmxes->limit($start, $length)->select();
        $result = $zfmxes->query('SELECT FOUND_ROWS() as count');
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
        $model = model("Zfmxes");
        if($model->setZfmxesTopTrue($id)){
            $data['url'] = url('Zfmxes/settop');
            $this->result($data, 1, '置顶成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '置顶失败';
            $this->result('', 0, $error, 'json');
        }
    }
    public function stick_false(){
        $id = $this->request->param('id/d');
        $model = model("Zfmxes");
        if($model->setZfmxesTopFalse($id)){
            $data['url'] = url('Zfmxes/settop');
            $this->result($data, 1, '取消置顶成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '取消置顶失败';
            $this->result('', 0, $error, 'json');
        }
    }
}