<?php
/**
 * Created by PhpStorm.
 * User: WL
 * Date: 2017/3/10
 * Time: 22:56
 */
//后台管理公共应用函数

/**
 * 系统用户明文密码加密方法
 * @param  string $str 要加密的字符串
 * @return string
 */
function pwd_encrypt($str, $key = 'HLY'){
    return '' === $str ? '' : md5(sha1($str) . sha1($key) . $key);
    //php 5.5以上用password_hash
}
/**
  @param: text  此方法只针对于定增，大宗的项目导出excel
**/
function project_show($datas,$type){
	//加载第三方类库
	vendor('PHPExcel.PHPExcel');
	//实例化表格对象
	$objPHPExcel = new \PHPExcel();
	if($type == 1){
		$objPHPExcel->getProperties()
					->setCreator("Financial partner")
					->setTitle("金融合伙人定增审核状态的的企业信息统计表")
					->setSubject("金融合伙人定增审核状态的的企业信息统计表")
					->setDescription("金融合伙人定增审核状态的的企业信息统计表");
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
			$name = '金融合伙人定增审核状态的的企业信息统计表(截止时间：'. date('Y-m-d H:i:s' , time()).')';
			$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', $name)
					->setCellValue('A2', '企业ID')
					->setCellValue('B2', '企业名称')
					->setCellValue('C2', '股票代码')
					->setCellValue('D2', '审核状态')
					->setCellValue('E2', '展示状态')
					->setCellValue('F2', '融资状态')
					->setCellValue('G2', '签约状态')
					->setCellValue('H2', '预案公告日');
					
			$objPHPExcel->getActiveSheet()->setTitle(date('Y-m-d' , time()));
			$objPHPExcel->setActiveSheetIndex(0);
			
			$i = 3;
			foreach($datas as $data){
				$objPHPExcel->getActiveSheet()->setCellValue('A'. $i, $data['id']);
				$objPHPExcel->getActiveSheet()->setCellValue('B'. $i, $data['sec_uri_tyshortname']);
				$objPHPExcel->getActiveSheet()->setCellValue('C'. $i, $data['msecucode']);
				if($data['zfmx_examine'] == 1){
					$objPHPExcel->getActiveSheet()->setCellValue('D'. $i, "已审核");
				}else{
					$objPHPExcel->getActiveSheet()->setCellValue('D'. $i, "未审核");
				}
				if($data['zfmx_exhibition'] == 1){
					$objPHPExcel->getActiveSheet()->setCellValue('E'. $i, "已展示");
				}else{
					$objPHPExcel->getActiveSheet()->setCellValue('E'. $i, "未展示");
				}
				if($data['zfmx_financing'] == 1){
					$objPHPExcel->getActiveSheet()->setCellValue('F'. $i, "融资结束");
				}else{
					$objPHPExcel->getActiveSheet()->setCellValue('F'. $i, "融资中");
				}
				if($data['zfmx_sign'] == 1){
					$objPHPExcel->getActiveSheet()->setCellValue('G'. $i, "是");
				}else{
					$objPHPExcel->getActiveSheet()->setCellValue('G'. $i, "否");
				}
				$objPHPExcel->getActiveSheet()->setCellValue('H'. $i, $data['plannoticeddate']);
				$i ++;
			}
		}else if($type == 2){
			$objPHPExcel->getProperties()
				->setCreator("Financial partner")
				->setTitle("金融合伙人大宗发布企业信息统计表")
				->setSubject("金融合伙人大宗发布企业信息统计表")
				->setDescription("金融合伙人大宗发布企业信息统计表");
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
			$name = '金融合伙人大宗发布企业信息统计表(截止时间：'. date('Y-m-d H:i:s' , time()).')';
			$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', $name)
					->setCellValue('A2', '企业ID')
					->setCellValue('B2', '企业名称')
					->setCellValue('C2', '股票代码')
					->setCellValue('D2', '转让方向')
					->setCellValue('E2', '转让价格')
					->setCellValue('F2', '展示状态')
					->setCellValue('G2', '融资状态')
					->setCellValue('H2', '签约状态')
					->setCellValue('I2', '是否为精品')
					->setCellValue('J2', '发布时间');
					
			$objPHPExcel->getActiveSheet()->setTitle(date('Y-m-d' , time()));
			$objPHPExcel->setActiveSheetIndex(0);
			
			$i = 3;
			foreach($datas as $data){
				$objPHPExcel->getActiveSheet()->setCellValue('A'. $i, $data['id']);
				$objPHPExcel->getActiveSheet()->setCellValue('B'. $i, $data['sec_uri_tyshortname']);
				$objPHPExcel->getActiveSheet()->setCellValue('C'. $i, $data['secucode']);
				$objPHPExcel->getActiveSheet()->setCellValue('D'. $i, $data['direction']);
				$objPHPExcel->getActiveSheet()->setCellValue('E'. $i, $data['price']);
				
				if($data['zryx_exhibition'] == 1){
					$objPHPExcel->getActiveSheet()->setCellValue('F'. $i, "已展示");
				}else{
					$objPHPExcel->getActiveSheet()->setCellValue('F'. $i, "未展示");
				}
				if($data['zryx_financing'] == 1){
					$objPHPExcel->getActiveSheet()->setCellValue('G'. $i, "融资结束");
				}else{
					$objPHPExcel->getActiveSheet()->setCellValue('G'. $i, "融资中");
				}
				if($data['zryx_sign'] == 1){
					$objPHPExcel->getActiveSheet()->setCellValue('H'. $i, "是");
				}else{
					$objPHPExcel->getActiveSheet()->setCellValue('H'. $i, "否");
				}
				if($data['boutique'] == 1){
					$objPHPExcel->getActiveSheet()->setCellValue('I'. $i, "精品");
				}else{
					$objPHPExcel->getActiveSheet()->setCellValue('I'. $i, "否");
				}
				$objPHPExcel->getActiveSheet()->setCellValue('J'. $i, $data['create_time']);
				$i ++;
			}
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

/**
  @param: text  此方法只针对于机构中心的导出excel
**/
function organize_show($datas)
{
	//加载第三方类库
	vendor('PHPExcel.PHPExcel');
	//实例化表格对象
	$objPHPExcel = new \PHPExcel();
	
		$objPHPExcel->getProperties()
					->setCreator("Financial partner")
					->setTitle("金融合伙人机构中心的企业信息统计表")
					->setSubject("金融合伙人机构中心的企业信息统计表")
					->setDescription("金融合伙人机构中心的企业信息统计表");
			$objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
			$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(40);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(22);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('C')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('E')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('F')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('G')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('H')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('I')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $objPHPExcel->getActiveSheet()->getStyle('J')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('C')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('E')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('F')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('G')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('H')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('I')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $objPHPExcel->getActiveSheet()->getStyle('J')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('D2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('E2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('F2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('G2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('H2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('I2')->getFont()->setBold(true);
                        $objPHPExcel->getActiveSheet()->getStyle('J2')->getFont()->setBold(true);
			$name = '金融合伙人机构中心的企业信息统计表(截止时间：'. date('Y-m-d H:i:s' , time()).')';
			$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', $name)
					->setCellValue('A2', '机构名称')
					->setCellValue('B2', '管理规模')
					->setCellValue('C2', '投资企业')
					->setCellValue('D2', '投资方向')
					->setCellValue('E2', '所属区域')
					->setCellValue('F2', '联系人')
					->setCellValue('G2', '联系方式')
					->setCellValue('H2', '是否确认展示')
                                        ->setCellValue('I2', '客服ID')
					->setCellValue('J2', '状态');
			$objPHPExcel->getActiveSheet()->setTitle(date('Y-m-d' , time()));
			$objPHPExcel->setActiveSheetIndex(0);
			
			$i = 3;
			foreach($datas as $data){
				$objPHPExcel->getActiveSheet()->setCellValue('A'. $i, $data['org_name']);
				$objPHPExcel->getActiveSheet()->setCellValue('B'. $i, $data['scale_min'].'-'.$data['scale_max'].$data['unit']);
				$objPHPExcel->getActiveSheet()->setCellValue('C'. $i, $data['inc_target']);
				$objPHPExcel->getActiveSheet()->setCellValue('D'. $i, $data['inc_industry']);
				$objPHPExcel->getActiveSheet()->setCellValue('E'. $i, $data['inc_area']);
				$objPHPExcel->getActiveSheet()->setCellValue('F'. $i, $data['contacts']);
				$objPHPExcel->getActiveSheet()->setCellValue('G'. $i, $data['contact_tel']);
				if($data['show'] == 1){
					$objPHPExcel->getActiveSheet()->setCellValue('H'. $i, "已展示");
				}else{
					$objPHPExcel->getActiveSheet()->setCellValue('H'. $i, "未展示");
				}
				$objPHPExcel->getActiveSheet()->setCellValue('I'. $i, $data['customer_service_id']);
                                if($data['status'] == 2 && $data['flag'] == 1){
                                        $objPHPExcel->getActiveSheet()->setCellValue('J'. $i, "发布/显示");
				}elseif($data['status'] == 2 && $data['flag'] == 2){
                                        $objPHPExcel->getActiveSheet()->setCellValue('J'. $i, "发布/隐藏");
				}else{
					$objPHPExcel->getActiveSheet()->setCellValue('J'. $i, "草稿/隐藏");
				}
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

/**
  @param: text  此方法只针对于项目线索的导出excel
**/
function project_clue_show($datas)
{
	//加载第三方类库
	vendor('PHPExcel.PHPExcel');
	//实例化表格对象
	$objPHPExcel = new \PHPExcel();
	
		$objPHPExcel->getProperties()
					->setCreator("Financial partner")
					->setTitle("金融合伙人项目线索的企业信息统计表")
					->setSubject("金融合伙人项目线索的企业信息统计表")
					->setDescription("金融合伙人项目线索的企业信息统计表");
			$objPHPExcel->getActiveSheet()->mergeCells('A1:F1');
			$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(40);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('C')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('E')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('F')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('C')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('E')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('F')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->getStyle('A')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('D2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('E2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('F2')->getFont()->setBold(true);
			
			$name = '金融合伙人项目线索的信息统计表(截止时间：'. date('Y-m-d H:i:s' , time()).')';
			$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', $name)
					->setCellValue('A2', '项目名称')
					->setCellValue('B2', '项目来源')
					->setCellValue('C2', '状态')
					->setCellValue('D2', '融资计划')
					->setCellValue('E2', '联系人')
					->setCellValue('F2', '创建时间');		
			$objPHPExcel->getActiveSheet()->setTitle(date('Y-m-d' , time()));
			$objPHPExcel->setActiveSheetIndex(0);
			
			$i = 3;
			foreach($datas as $data){
				$objPHPExcel->getActiveSheet()->setCellValue('A'. $i, $data['pro_name']);
				$objPHPExcel->getActiveSheet()->setCellValue('B'. $i, $data['pro_origin']);
				$objPHPExcel->getActiveSheet()->setCellValue('C'. $i, $data['status']);
				$objPHPExcel->getActiveSheet()->setCellValue('D'. $i, $data['capital_plan']);
				$objPHPExcel->getActiveSheet()->setCellValue('E'. $i, $data['contacts']);
				$objPHPExcel->getActiveSheet()->setCellValue('F'. $i, $data['create_time']);
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
/**
  合伙人身份
  @param:int $role_type
  @return:string $result
**/
function checkRoleType($role_type)
{
	switch ($role_type) {
		case 1:
			$result = '投资人';
			break;
		case 2:
			$result = '项目方';
			break;
		default:
			$result = '未选择';
			break;
	}
	return $result;
}
/**
  @param:(array | object)$datas  此方法只针对于已审核的合伙人的导出excel
**/
function through_audited_show($datas)
{
	//加载第三方类库
	vendor('PHPExcel.PHPExcel');
	//实例化表格对象
	$objPHPExcel = new \PHPExcel();
	
		$objPHPExcel->getProperties()
					->setCreator("Financial partner")
					->setTitle("金融合伙人已审核认证合伙人信息统计表")
					->setSubject("金融合伙人已审核认证合伙人信息统计表")
					->setDescription("金融合伙人已审核认证合伙人信息统计表");
			$objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
			$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(22);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(22);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('C')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('E')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('F')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('G')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('H')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('I')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


			$objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('C')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('E')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('F')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('G')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);			
            $objPHPExcel->getActiveSheet()->getStyle('H')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('I')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);


			$objPHPExcel->getActiveSheet()->getStyle('A')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('D2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('E2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('F2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('G2')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('H2')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('I2')->getFont()->setBold(true);

			$name = '金融合伙人已审核认证合伙人的信息统计表(截止时间：'. date('Y-m-d H:i:s' , time()).')';
			$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', $name)
					->setCellValue('A2', 'ID')
					->setCellValue('B2', '认证身份')
					->setCellValue('C2', '姓名')
					->setCellValue('D2', '电话')
					->setCellValue('E2', '邮箱')
					->setCellValue('F2', '公司')
                    ->setCellValue('G2', '职位')
                    ->setCellValue('H2', '上级用户')
                    ->setCellValue('I2', '备注');
			$objPHPExcel->getActiveSheet()->setTitle(date('Y-m-d' , time()));
			$objPHPExcel->setActiveSheetIndex(0);
			
			$i = 3;
			foreach($datas as $data){
				$objPHPExcel->getActiveSheet()->setCellValue('A'. $i, $data['t_id']);
				$objPHPExcel->getActiveSheet()->setCellValue('B'. $i, $data['role_type']);
				$objPHPExcel->getActiveSheet()->setCellValue('C'. $i, $data['realName']);
				$objPHPExcel->getActiveSheet()->setCellValue('D'. $i, $data['phone']);
				$objPHPExcel->getActiveSheet()->setCellValue('E'. $i, $data['email']);
				$objPHPExcel->getActiveSheet()->setCellValue('F'. $i, $data['company']);
				$objPHPExcel->getActiveSheet()->setCellValue('G'. $i, $data['position']);
				$objPHPExcel->getActiveSheet()->setCellValue('H'. $i, $data['superior']);
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
