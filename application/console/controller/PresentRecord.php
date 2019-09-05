<?php
namespace app\console\controller;

class PresentRecord extends ConsoleBase{
    public function index(){
        return view();
    }
    public function read(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));

        $partner_trade_no = $this->request->get('partner_trade_no', '');

        $present_record = model('PresentRecord');
        //搜索条件
        if('' !== $partner_trade_no) {
            $present_record->where('partner_trade_no','like','%'.$partner_trade_no.'%');
        }
        $present_record->where('status','eq',0);
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $present_record->order($_order);
        }
        $present_record->field('SQL_CALC_FOUND_ROWS *');
        $list = $present_record->limit($start, $length)->select();
        $result = $present_record->query('SELECT FOUND_ROWS() as count');
        $total = $result[0]['count'];
        $data = [];
        $model = model("Member");
        foreach ($list as $key => $item) {
            $list[$key]['amount'] = floatval($item['amount']/100);
            $user = $model->getMemberInfoById($item['create_uid']);
            $list[$key]['create_uid'] = $user['realName'];
            $data[] = $item->toArray();
        }
        $return['data'] = $data;
        $return['recordsFiltered'] = $total;
        $return['recordsTotal'] = $total;

        echo json_encode($return);
    }
    public function already(){
        return view();
    }

    public function alreadyread(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));

        $create_date1 = $this->request->get('create_date1', '');
        $create_date2 = $this->request->get('create_date2', '');
        $partner_trade_no = $this->request->get('partner_trade_no', '');

        $present_record = model('PresentRecord');
        //搜索条件
        if('' !== $create_date1 && '' !== $create_date2) {
            $present_record->where('create_time','between',[strtotime($create_date1.'00:00:00') , strtotime($create_date2.'23:59:59')]);
        }elseif ('' !== $create_date1 && '' == $create_date2) {
            $present_record->where('create_time','>',strtotime($create_date1.'00:00:00'));
        }elseif ('' == $create_date1 && '' !== $create_date2) {
            $present_record->where('create_time','<',strtotime($create_date2.'23:59:59'));
        }
        if('' !== $partner_trade_no) {
            $present_record->where('partner_trade_no','like','%'.$partner_trade_no.'%');
        }
        $present_record->where('status','eq',1);
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $present_record->order($_order);
        }
        $present_record->field('SQL_CALC_FOUND_ROWS *');
        $list = $present_record->limit($start, $length)->select();
        $result = $present_record->query('SELECT FOUND_ROWS() as count');
        $total = $result[0]['count'];
        $data = [];
        $model = model("Member");
        foreach ($list as $key => $item) {
            $list[$key]['amount'] = floatval($item['amount']/100);
            $user = $model->getMemberInfoById($item['create_uid']);
            $list[$key]['create_uid'] = $user['realName'];
            $data[] = $item->toArray();
        }
        $return['data'] = $data;
        $return['recordsFiltered'] = $total;
        $return['recordsTotal'] = $total;

        echo json_encode($return);
    }
    public function deal(){
        $id = $this->request->param('id/d', 0);
        $model = model('PresentRecord');
        if($result = $model->dealPresentRecord($id)){
            $this->result($result, 1, '处理成功！', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '处理失败!';
            $this->result('', 0, $error, 'json');
        }
    }
    public function downLoadPresentRecord(){
        $arr = input();
        $date1 = $arr['date1'];
        $date2 = $arr['date2'];
        $partner_trade_no = $arr['partner_trade_no'];
        $present_record = model('PresentRecord');
        //搜索条件
        if('' !== $date1 && '' !== $date2) {
            $present_record->where('create_time','between',[strtotime($date1.'00:00:00') , strtotime($date2.'23:59:59')]);
        }elseif ('' !== $date1 && '' == $date2) {
            $present_record->where('create_time','>',strtotime($date1.'00:00:00'));
        }elseif ('' == $date1 && '' !== $date2) {
            $present_record->where('create_time','<',strtotime($date2.'23:59:59'));
        }
        if('' !== $partner_trade_no) {
            $present_record->where('partner_trade_no','like','%'.$partner_trade_no.'%');
        }
        vendor('PHPExcel.PHPExcel');
        $objPHPExcel = new \PHPExcel();
        $list = $present_record->where(['status' => 1])->order("pr_id desc")->select();
        $member = model("Member");
        $user = model("User");
        foreach ($list as $key => $value) {
            $list[$key]['amount'] = round($value['amount']/100 , 2).'元';
            $list[$key]['pay_time'] = date('Y-m-d H:i:s' , $value['pay_time']);
            $mem = $member->where(['openId' => $value['openid']])->find();
            $list[$key]['openid'] = $mem['realName'];
            $usr = $user->getUserById($value['pay_uid']);
            $list[$key]['pay_uid'] = $usr['real_name'];
        }
        
        $objPHPExcel->getProperties()
                ->setCreator("Financial PresentRecord")
                ->setTitle("金融合伙人提现统计表")
                ->setSubject("金融合伙人提现统计表")
                ->setDescription("金融合伙人提现统计表");
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(18);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(19);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(22);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(18);
        $objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('J')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('C')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('E')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('F')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('G')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('H')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('J')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('C')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('E')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('F')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('G')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('H')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('E1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('F1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('G1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('H1')->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '序号')
                ->setCellValue('B1', 'nonce_str')
                ->setCellValue('C1', 'partner_trade_no')
                ->setCellValue('D1', 'openid')
                ->setCellValue('E1', 'amount')
                ->setCellValue('F1', '创建时间')
                ->setCellValue('G1', '支付时间')
                ->setCellValue('H1', '操作用户');
        $objPHPExcel->getActiveSheet()->setTitle(date('Y-m-d' , time()));
        $objPHPExcel->setActiveSheetIndex(0);
        
        $i = 2;
        foreach($list as $data){
            $objPHPExcel->getActiveSheet()->setCellValue('A'. $i, $data['pr_id']);
            $objPHPExcel->getActiveSheet()->setCellValue('B'. $i, $data['nonce_str']);
            $objPHPExcel->getActiveSheet()->setCellValue('C'. $i, $data['partner_trade_no']);
            $objPHPExcel->getActiveSheet()->setCellValue('D'. $i, $data['openid']);
            $objPHPExcel->getActiveSheet()->setCellValue('E'. $i, $data['amount']);
            $objPHPExcel->getActiveSheet()->setCellValue('F'. $i, $data['create_time']);
            $objPHPExcel->getActiveSheet()->setCellValue('G'. $i, $data['pay_time']);
            $objPHPExcel->getActiveSheet()->setCellValue('H'. $i, $data['pay_uid']);
            $i ++;
        }
        if($date1 != '' && $date2 != ''){
            $name = '（'.$date1.'至'.$date2.'）';
        }elseif ($date1 == '' && $date2 != '') {
            $name = '直至'.$date2;
        }elseif ($date1 != '' && $date2 == '') {
            $name = '（'.$date1.'至今日）';
        } else {
            $name = '';
        }
        $name = '金融合伙人提现统计表_'.$name;
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
}