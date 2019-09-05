<?php
namespace app\console\controller;
class Member extends ConsoleBase{
    public function index(){
        return view();
    }
    /**
    @param:用户拉黑
    @time：2018/5/25
     **/
    public function pullback(){
        $data   = input('id');
        $openId = input('openid');
        if(isset($data) && $data !== "" && isset($openId) && $openId !== ""){
            $member = model('member');
            $num['pullback'] = $data;
            $result = $member->where("openId = '".$openId."'")->find();
            if($result){
                $kar = $member->where("openId = '".$openId."'")->update($num);
                if($kar){
                    $t['msg'] = "操作成功";
                    $t['id'] = $data;
                    $t['code'] = 200;
                    return json($t);
                }
            }else{
                $t['msg'] = "用户不存在";
                $t['id'] = $data;
                $t['code'] = 101;
                return json($t);
            }
        }else{
            $t['msg'] = "操作失败";
            $t['id'] = $data;
            $t['code'] = 102;
            return json($t);
        }
    }
    public function read(){
        $return = [];
        $order  = $this->request->get('order/a', []);
        $start  = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));

        $create_date1   = $this->request->get('create_date1', '');
        $create_date2   = $this->request->get('create_date2', '');
        $last_date1     = $this->request->get('last_date1', '');
        $last_date2     = $this->request->get('last_date2', '');
        $userName       = $this->request->get('userName', '');
        $realName       = $this->request->get('realName', '');
        $userPhone      = $this->request->get('userPhone', '');
        $uid            = $this->request->get('uid', '');
        $userType       = $this->request->get('userType', '');
        $is_follow      = $this->request->get('is_follow', '');

        $member = model('member');
        //搜索条件
        if('' !== $create_date1 && '' !== $create_date2) {
            $member->where('createTime','between',[strtotime($create_date1.'00:00:00') , strtotime($create_date2.'23:59:59')]);
        }elseif ('' !== $create_date1 && '' == $create_date2) {
            $member->where('createTime','>',strtotime($create_date1.'00:00:00'));
        }elseif ('' == $create_date1 && '' !== $create_date2) {
            $member->where('createTime','<',strtotime($create_date2.'23:59:59'));
        }
        if('' !== $last_date1 && '' !== $last_date2) {
            $member->where('lastTime','between',[strtotime($last_date1.'00:00:00') , strtotime($last_date2.'23:59:59')]);
        }elseif ('' !== $last_date1 && '' == $last_date2) {
            $member->where('lastTime','>',strtotime($last_date1.'00:00:00'));
        }elseif ('' == $last_date1 && '' !== $last_date2) {
            $member->where('lastTime','<',strtotime($last_date2.'23:59:59'));
        }
        if('' !== $userName) {
            $member->where('userName','like','%'.$userName.'%');
        }
        if('' !== $realName) {
            $member->where('realName','like','%'.$realName.'%');
        }
        if('' !== $userPhone) {
            $member->where('userPhone','like','%'.$userPhone.'%');
        }
        if('' !== $uid) {
            $member->where('uid','eq',$uid);
        }
        if('' !== $userType) {
            $member->where('userType','eq',$userType);
        }
        if('' !== $is_follow) {
            $member->where('is_follow','eq',$is_follow);
        }
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $member->order($_order);
        }
        $member->field('SQL_CALC_FOUND_ROWS *');
        $list = $member->limit($start, $length)->select();
        $result = $member->query('SELECT FOUND_ROWS() as count');
        $total = $result[0]['count'];
        $data = [];
        $model = model("Member");
        foreach ($list as $key => $item) {
            if($item['superior'] != 0){
                $user = $model->getMemberInfoById($item['superior']);
                $list[$key]['superior'] = $user['realName'];
            } else {
                $list[$key]['superior'] = '-';
            }
            $list[$key]['createTime'] = date('Y-m-d H:i:s' , $item['createTime']);
            $list[$key]['lastTime'] = date('Y-m-d H:i:s' , $item['lastTime']);
            $data[] = $item->toArray();
        }
        $return['data'] = $data;
        $return['recordsFiltered'] = $total;
        $return['recordsTotal'] = $total;

        echo json_encode($return);
    }
    public function count(){
        $all_member = model("Member")->count('uid');
        $all_through = model("Member")->where(['userType' => 2])->count('uid');
        $all_superior = model("Member")->where("superior","neq",0)->count('uid');
        $all_month = model("Member")->field("DATE_FORMAT(`CREATE_TIME`, '%Y-%m') as day,count('uid') as num")->group("day")->order('day desc')->select();
        $all_through_tb = model("Through")->field("DATE_FORMAT(`THROUGH_TIME`, '%Y-%m') as day,count('uid') as num")->where(['status' => 1])->group("day")->order('day desc')->select();
        $arr = [];
        foreach ($all_through_tb as $key => $value) {
            $arr[$value['day']] = $value['num'];
        }
        foreach ($all_month as $key => $value) {
            $all_month[$key]['through'] = 0;
            if(isset($arr[$value['day']])){
                $all_month[$key]['through'] = $arr[$value['day']];
            }
        }
        $this->assign('all_member' , $all_member);
        $this->assign('all_through' , $all_through);
        $this->assign('all_superior' , $all_superior);
        $this->assign('all_month' , $all_month);
        return view("count");
    }
    public function detail(){
        $model = model("Member");
        $list = $model->getMemberList(2);
        $this->assign('list' , $list);
        return view();
    }
    public function detailread(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));

        $create_date1 = $this->request->get('create_date1', '');
        $create_date2 = $this->request->get('create_date2', '');
        $uid = $this->request->get('uid', '');
        $userName = $this->request->get('userName', '');
        $userType = $this->request->get('userType', '');

        $member = model('member');
        $member->where('superior','neq',0);
        //搜索条件
        if('' !== $create_date1 && '' !== $create_date2) {
            $member->where('createTime','between',[strtotime($create_date1.'00:00:00') , strtotime($create_date2.'23:59:59')]);
        }elseif ('' !== $create_date1 && '' == $create_date2) {
            $member->where('createTime','>',strtotime($create_date1.'00:00:00'));
        }elseif ('' == $create_date1 && '' !== $create_date2) {
            $member->where('createTime','<',strtotime($create_date2.'23:59:59'));
        }
        if('' !== $uid) {
            $member->where('superior','eq',$uid);
        }
        if('' !== $userName) {
            $member->where('userName','like','%'.$userName.'%');
        }
        if('' !== $userType) {
            $member->where('userType','eq',$userType);
        }
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $member->order($_order);
        }
        $member->field('SQL_CALC_FOUND_ROWS *');
        $list = $member->limit($start, $length)->select();
        $result = $member->query('SELECT FOUND_ROWS() as count');
        $total = $result[0]['count'];
        $data = [];
        $model = model("Member");
        foreach ($list as $key => $item) {
            $info = $model->getMemberInfoById($item['superior']);
            $list[$key]['superior'] = $info['realName'];
            $data[] = $item->toArray();
        }
        $return['data'] = $data;
        $return['recordsFiltered'] = $total;
        $return['recordsTotal'] = $total;

        echo json_encode($return);
    }
    
    /**
     * 用户详细信息导出
     */
    public function exportMember(){
        vendor('PHPExcel.PHPExcel');
        $objPHPExcel = new \PHPExcel();
        
        
        $create_date1   = $this->request->get('create_date1', '');
        $create_date2   = $this->request->get('create_date2', '');
        $last_date1     = $this->request->get('last_date1', '');
        $last_date2     = $this->request->get('last_date2', '');
        $userName       = $this->request->get('userName', '');
        $realName       = $this->request->get('realName', '');
        $userPhone      = $this->request->get('userPhone', '');
        $openId         = $this->request->get('openId', '');
        $userType       = $this->request->get('userType', '');
        $is_follow      = $this->request->get('is_follow', '');
        
        
        $member = model('member');
        
        //注册时间
        if('' !== $create_date1 && '' !== $create_date2) {
            $member->where('createTime','between',[strtotime($create_date1.'00:00:00') , strtotime($create_date2.'23:59:59')]);
        }elseif ('' !== $create_date1 && '' == $create_date2) {
            $member->where('createTime','>',strtotime($create_date1.'00:00:00'));
        }elseif ('' == $create_date1 && '' !== $create_date2) {
            $member->where('createTime','<',strtotime($create_date2.'23:59:59'));
        }
        
        //最新一次登录时间
        if('' !== $last_date1 && '' !== $last_date2) {
            $member->where('lastTime','between',[strtotime($last_date1.'00:00:00') , strtotime($last_date2.'23:59:59')]);
        }elseif ('' !== $last_date1 && '' == $last_date2) {
            $member->where('lastTime','>',strtotime($last_date1.'00:00:00'));
        }elseif ('' == $last_date1 && '' !== $last_date2) {
            $member->where('lastTime','<',strtotime($last_date2.'23:59:59'));
        }
        
        //微信昵称
        if('' !== $userName) {
            $member->where('userName','like','%'.$userName.'%');
        }
        
        //注册姓名
        if('' !== $realName) {
            $member->where('realName','like','%'.$realName.'%');
        }
        
        //电话
        if('' !== $userPhone) {
            $member->where('userPhone','like','%'.$userPhone.'%');
        }
        
        //微信openid
        if('' !== $openId) {
            $member->where('openId','like','%'.$openId.'%');
        }
        
        //身份类型
        if('' !== $userType) {
            $member->where('userType','eq',$userType);
        }
        
        //公众号关注
        if('' !== $is_follow) {
            $member->where('is_follow','eq',$is_follow);
        }
        
        
        $list = $member->select();
        
        
        
        $datas = [];
        foreach ($list as $key => $value) {
            if($value['userType'] == 0){
                $typename = '游客';
            }elseif ($value['userType'] == 1) {
                $typename = '绑定用户';
            }elseif ($value['userType'] == 2) {
                $typename = '认证合伙人';
            }
            
            if($value['is_follow'] == 1){
                $isFollow ="已关注";
            }else {
                $isFollow ="未关注";
            }
            
            if($value['superior'] != 0){
                $user = $member->getMemberInfoById($value['superior']);
                $superior = $user['realName'];
            } else {
                $superior = '-';
            }
            
            $arr  = [$value['uid'],$value['userName'],$value['realName'],$value['userPhone'],$value['openId'],$typename,$superior,date('Y-m-d H:i:s',$value['createTime']),date('Y-m-d H:i:s',$value['lastTime']),$isFollow];
            array_push($datas, $arr);
        }
        
        $objPHPExcel->getProperties()
                    ->setCreator("Financial partner")
                    ->setTitle("用户明细表")
                    ->setSubject("用户明细表")
                    ->setDescription("用户明细表");
        $objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(18);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(19);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(22);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(18);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('J')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('C')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('E')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('F')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('G')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('H')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('I')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('J')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('C')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('E')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('F')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('G')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('H')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('I')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('D2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('E2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('F2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('G2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('H2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('I2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('J2')->getFont()->setBold(true);
        $name = '用户信息明细表（截止时间：'. date('Y-m-d H:i:s' , time()).'）';
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', $name)
        ->setCellValue('A2', '用户ID')
        ->setCellValue('B2', '昵称')
        ->setCellValue('C2', '姓名')
        ->setCellValue('D2', '电话')
        ->setCellValue('E2', 'openid')
        ->setCellValue('F2', '身份')
        ->setCellValue('G2', '上级用户')
        ->setCellValue('H2', '注册时间')
        ->setCellValue('I2', '最后登录日期')
        ->setCellValue('J2', '是否关注公众号');
        $objPHPExcel->getActiveSheet()->setTitle(date('Y-m-d' , time()));
        $objPHPExcel->setActiveSheetIndex(0);
        
        $i = 3;
        foreach($datas as $data){
            $objPHPExcel->getActiveSheet()->setCellValue('A'. $i, $data[0]);
            $objPHPExcel->getActiveSheet()->setCellValue('B'. $i, $data[1]);
            $objPHPExcel->getActiveSheet()->setCellValue('C'. $i, $data[2]);
            $objPHPExcel->getActiveSheet()->setCellValue('D'. $i, $data[3]);
            $objPHPExcel->getActiveSheet()->setCellValue('E'. $i, $data[4]);
            $objPHPExcel->getActiveSheet()->setCellValue('F'. $i, $data[5]);
            $objPHPExcel->getActiveSheet()->setCellValue('G'. $i, $data[6]);
            $objPHPExcel->getActiveSheet()->setCellValue('H'. $i, $data[7]);
            $objPHPExcel->getActiveSheet()->setCellValue('I'. $i, $data[8]);
            $objPHPExcel->getActiveSheet()->setCellValue('J'. $i, $data[9]);
            $i ++;
        }
        
        // 输出Excel表格到浏览器下载
        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$name.'.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        //header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        //header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        //header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        //header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        $objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
        $objWriter->save('php://output');
    }
    
    
    
    public function downLoadMember(){
        vendor('PHPExcel.PHPExcel');
        $objPHPExcel = new \PHPExcel();
        
        $model      = model("Through");
        $list       = $model->where(['status' => 1])->select();
        $model      = model("Member");
        $week_num   = intval(date('w' , time()))-1;
        
        $data = [];
        foreach ($list as $key => $value) {
            $time = date('Y-m-d' , strtotime('-'. $week_num.' day'));
            $week = $model->where('CREATE_TIME','gt',$time.' 00:00:00')->where(['superior' => $value['uid']])->select();
            $week_ins = $this->getStatisticsData($week);
            $week_all = count($week);
            $all = $model->where(['superior' => $value['uid']])->select();
            $all_ins = $this->getStatisticsData($all);
            $all_all = count($all);
            $arr = [$value['uid'],$value['realName'],$week_ins[0],$all_ins[0],$week_ins[2],$all_ins[2],$week_ins[1],$all_ins[1],$week_all,$all_all];
            array_push($data, $arr);
        }
        $datas = [];
        for ($index = 0; $index < count($data); $index++) {
            for ($index1 = $index; $index1 < count($data); $index1++) {
                if($data[$index][8] < $data[$index1][8]){
                    $asd = $data[$index1];
                    $data[$index1] = $data[$index];
                    $data[$index] = $asd;
                }
            }
        }
        $datas = $data;
        $num1 = 0;$num2 = 0;
        $num3 = 0;$num4 = 0;
        $num5 = 0;$num6 = 0;
        $num7 = 0;$num8 = 0;
        foreach ($datas as $key => $value) {
            $num1 += intval($value[2]);
            $num2 += intval($value[3]);
            $num3 += intval($value[4]);
            $num4 += intval($value[5]);
            $num5 += intval($value[6]);
            $num6 += intval($value[7]);
            $num7 += intval($value[8]);
            $num8 += intval($value[9]);
        }
        $end = ['','总计',$num1,$num2,$num3,$num4,$num5,$num6,$num7,$num8];
        array_push($datas, $end);
        $objPHPExcel->getProperties()
                ->setCreator("Financial partner")
                ->setTitle("金融合伙人拉新统计表")
                ->setSubject("金融合伙人拉新统计表")
                ->setDescription("金融合伙人拉新统计表");
        $objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(18);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(19);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(22);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(18);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('J')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('C')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('E')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('F')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('G')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('H')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('I')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('J')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('C')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('E')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('F')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('G')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('H')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('I')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('D2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('E2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('F2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('G2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('H2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('I2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('J2')->getFont()->setBold(true);
        $name = '金融合伙人拉新统计表（截止时间：'. date('Y-m-d H:i:s' , time()).'）';
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', $name)
                ->setCellValue('A2', '用户ID')
                ->setCellValue('B2', '合伙人姓名')
                ->setCellValue('C2', '本周新增游客数')
                ->setCellValue('D2', '总游客数')
                ->setCellValue('E2', '本周新增认证合伙人')
                ->setCellValue('F2', '总认证合伙人')
                ->setCellValue('G2', '本周新增绑定用户')
                ->setCellValue('H2', '总绑定用户')
                ->setCellValue('I2', '本周合计')
                ->setCellValue('J2', '总数合计');
        $objPHPExcel->getActiveSheet()->setTitle(date('Y-m-d' , time()));
        $objPHPExcel->setActiveSheetIndex(0);
        
        $i = 3;
        foreach($datas as $data){
            $objPHPExcel->getActiveSheet()->setCellValue('A'. $i, $data[0]);
            $objPHPExcel->getActiveSheet()->setCellValue('B'. $i, $data[1]);
            $objPHPExcel->getActiveSheet()->setCellValue('C'. $i, $data[2]);
            $objPHPExcel->getActiveSheet()->setCellValue('D'. $i, $data[3]);
            $objPHPExcel->getActiveSheet()->setCellValue('E'. $i, $data[4]);
            $objPHPExcel->getActiveSheet()->setCellValue('F'. $i, $data[5]);
            $objPHPExcel->getActiveSheet()->setCellValue('G'. $i, $data[6]);
            $objPHPExcel->getActiveSheet()->setCellValue('H'. $i, $data[7]);
            $objPHPExcel->getActiveSheet()->setCellValue('I'. $i, $data[8]);
            $objPHPExcel->getActiveSheet()->setCellValue('J'. $i, $data[9]);
            $i ++;
        }
        
        // 输出Excel表格到浏览器下载
        ob_end_clean();
//        header('Content-Type: application/vnd.ms-excel');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$name.'.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        $objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
        $objWriter->save('php://output');
    }
    
    
    public function getStatisticsData($data){
        if(empty($data)){
            $a = 0;$b = 0;$c = 0;
        } else {
            $a = 0;$b = 0;$c = 0;
            foreach ($data as $key => $value) {
                if($value['userType'] == 0){
                    $a++;
                }elseif ($value['userType'] == 1) {
                    $b++;
                } else {
                    $c++;
                }
            }
        }
        $arr = [$a , $b , $c];
        return $arr;
    }
    
    public function getwe(){
        $list = model("AaUid")->limit(0,2000)->select();
        foreach ($list as $key => $value) {
            $isFollow = isFollow($value['openid'],getAccessTokenFromFile());
            if($isFollow){
                $isFollow = $value['uid'].",已关注";
            }else{
                $isFollow = $value['uid'].",未关注";
            }
            echo $isFollow."<br/>";
        }
        
    }
    
}