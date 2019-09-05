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
function pwd_encrypt($str, $key = 'HLY')
{
    return '' === $str ? '' : md5(sha1($str) . sha1($key) . $key);
    //php 5.5以上用password_hash
}

/**
  @param: text  此方法只针对于定增，大宗的项目导出excel
**/
function project_show($datas,$type)
{
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
 * @param $datas  此方法只针对于已审核的合伙人的导出excel
 */
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
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(22);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(22);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(22);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(22);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(22);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(22);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(22);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(22);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(22);
            
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
            $objPHPExcel->getActiveSheet()->getStyle('K')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('L')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('M')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('N')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('O')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('P')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('Q')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('R')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

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
            $objPHPExcel->getActiveSheet()->getStyle('K')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('L')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('M')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('N')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('O')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('P')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('Q')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('R')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

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
            $objPHPExcel->getActiveSheet()->getStyle('K2')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('L2')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('M2')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('N2')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('O2')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('P2')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('Q2')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('R2')->getFont()->setBold(true);

			$name = '金融合伙人已审核认证合伙人的信息统计表(截止时间：'. date('Y-m-d H:i:s' , time()).')';
			$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', $name)
					->setCellValue('A2', 'ID')
					->setCellValue('B2', '认证身份')
					->setCellValue('C2', '姓名')
					->setCellValue('D2', '微信ID')
					->setCellValue('E2', '电话')
					->setCellValue('F2', '邮箱')
					->setCellValue('G2', '公司')
                    ->setCellValue('H2', '职位')
                    ->setCellValue('I2', '公司简称')
                    ->setCellValue('J2', '上级用户')
                    ->setCellValue('K2', '提交日期')
                    ->setCellValue('L2', '个性标签')
                    ->setCellValue('M2', '业务类型')
                    ->setCellValue('N2', '行业偏好')
                    ->setCellValue('O2', '投融资规模')
                    ->setCellValue('P2', '所在省份')
                    ->setCellValue('Q2', '是否关注')
                    ->setCellValue('R2', '备注');
			$objPHPExcel->getActiveSheet()->setTitle(date('Y-m-d' , time()));
			$objPHPExcel->setActiveSheetIndex(0);
			
			$i = 3;
			foreach($datas as $data){
				$objPHPExcel->getActiveSheet()->setCellValue('A'. $i, $data['uid']);
				$objPHPExcel->getActiveSheet()->setCellValue('B'. $i, $data['role_type']);
				$objPHPExcel->getActiveSheet()->setCellValue('C'. $i, $data['realName']);
				$objPHPExcel->getActiveSheet()->setCellValue('D'. $i, $data['weixin_id']);
				$objPHPExcel->getActiveSheet()->setCellValue('E'. $i, $data['phone']);
				$objPHPExcel->getActiveSheet()->setCellValue('F'. $i, $data['email']);
				$objPHPExcel->getActiveSheet()->setCellValue('G'. $i, $data['company']);
				$objPHPExcel->getActiveSheet()->setCellValue('H'. $i, $data['position']);
				$objPHPExcel->getActiveSheet()->setCellValue('I'. $i, $data['company_jc']);
				$objPHPExcel->getActiveSheet()->setCellValue('J'. $i, $data['superior']);
				$objPHPExcel->getActiveSheet()->setCellValue('K'. $i, $data['createTime']);
				$objPHPExcel->getActiveSheet()->setCellValue('L'. $i, $data['person_label']);
				if(isset($data['service'])) {
				    $objPHPExcel->getActiveSheet()->setCellValue('M'. $i, $data['service']);
				}else{
				    $objPHPExcel->getActiveSheet()->setCellValue('M'. $i, "-");
				}
				if(isset($data['industry'])) {
				    $objPHPExcel->getActiveSheet()->setCellValue('N'. $i, $data['industry']);
				}else{
				    $objPHPExcel->getActiveSheet()->setCellValue('N'. $i, "-");
				}
				if(isset($data['size'])) {
				    $objPHPExcel->getActiveSheet()->setCellValue('O'. $i, $data['size']);
				}else{
				    $objPHPExcel->getActiveSheet()->setCellValue('O'. $i, "-");
				}
				if(isset($data['size'])) {
				    $objPHPExcel->getActiveSheet()->setCellValue('P'. $i, $data['province']);
				}else{
				    $objPHPExcel->getActiveSheet()->setCellValue('P'. $i, "-");
				}
				$objPHPExcel->getActiveSheet()->setCellValue('Q'. $i, $data['is_follow']);
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
  @param: text  此方法只针对于需求管理的发现管理导出excel
**/
function publish_show($datas)
{
	//加载第三方类库
	vendor('PHPExcel.PHPExcel');
	//实例化表格对象
	$objPHPExcel = new \PHPExcel();
	
		$objPHPExcel->getProperties()
					->setCreator("Financial partner")
					->setTitle("金融合伙人发布管理信息统计表")
					->setSubject("金融合伙人发布管理信息统计表")
					->setDescription("金融合伙人发布管理信息统计表");
			$objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
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
			$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(22);
			
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
			$objPHPExcel->getActiveSheet()->getStyle('K')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('L')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('M')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('N')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			
			
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
			$objPHPExcel->getActiveSheet()->getStyle('H')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('I')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('J')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('K')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('L')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('M')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('N')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			
			
			
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
			$objPHPExcel->getActiveSheet()->getStyle('K2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('L2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('M2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('N2')->getFont()->setBold(true);
			
			
			$name = '金融合伙人需求管理信息统计表(截止时间：'. date('Y-m-d H:i:s' , time()).')';
			$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', $name)
                    ->setCellValue('A2', '发布id')
                    ->setCellValue('B2', '发布人名称')
                    ->setCellValue('C2', '发布人id')
                    ->setCellValue('D2', '选择标签')
                    ->setCellValue('E2', '转发人数')
                    ->setCellValue('F2', '内部客服ID')
                    ->setCellValue('G2', '点赞数量')
                    ->setCellValue('H2', '内容虚假数量')
                    ->setCellValue('I2', '身份虚假数量')
                    ->setCellValue('J2', '诈骗造谣数量')
                    ->setCellValue('K2', '恶语相加数量')
                    ->setCellValue('L2', '不诚信数量')
                    ->setCellValue('M2', '发布内容')
                    ->setCellValue('N2', '发布图片张数');
                    
			$objPHPExcel->getActiveSheet()->setTitle(date('Y-m-d' , time()));
			$objPHPExcel->setActiveSheetIndex(0);
			
			$i = 3;
			foreach($datas as $data){
				$objPHPExcel->getActiveSheet()->setCellValue('A'. $i, $data['id']);
				$objPHPExcel->getActiveSheet()->setCellValue('B'. $i, $data['realName']);
				$objPHPExcel->getActiveSheet()->setCellValue('C'. $i, $data['uid']);
				$objPHPExcel->getActiveSheet()->setCellValue('D'. $i, $data['label']);
				$objPHPExcel->getActiveSheet()->setCellValue('E'. $i, $data['forward_num']);
				$objPHPExcel->getActiveSheet()->setCellValue('F'. $i, $data['point_num']);
				$objPHPExcel->getActiveSheet()->setCellValue('G'. $i, $data['point_num']);
				$objPHPExcel->getActiveSheet()->setCellValue('H'. $i, $data['content_false_num']);
				$objPHPExcel->getActiveSheet()->setCellValue('I'. $i, $data['id_false_num']);
				$objPHPExcel->getActiveSheet()->setCellValue('J'. $i, $data['fraud_rumor_num']);
				$objPHPExcel->getActiveSheet()->setCellValue('K'. $i, $data['bad_language_num']);
				$objPHPExcel->getActiveSheet()->setCellValue('L'. $i, $data['dishonesty_num']);
				$objPHPExcel->getActiveSheet()->setCellValue('M'. $i, $data['content']);
				$objPHPExcel->getActiveSheet()->setCellValue('N'. $i, $data['point_num']);
		
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
  @param: text  此方法只针对于需求管理的发布项目管理导出excel
**/
function project_require_show($datas)
{
	//加载第三方类库
	vendor('PHPExcel.PHPExcel');
	//实例化表格对象
	$objPHPExcel = new \PHPExcel();
	
		$objPHPExcel->getProperties()
					->setCreator("Financial partner")
					->setTitle("金融合伙人发布项目管理信息统计表")
					->setSubject("金融合伙人发布项目管理信息统计表")
					->setDescription("金融合伙人发布项目管理信息统计表");
			$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
			$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(40);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(44);
			$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(18);
			$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(10);
			
			
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
			$objPHPExcel->getActiveSheet()->getStyle('K')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('L')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('M')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('N')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('O')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('P')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('Q')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('R')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('S')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			
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
		    $objPHPExcel->getActiveSheet()->getStyle('K')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
		    $objPHPExcel->getActiveSheet()->getStyle('L')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
		    $objPHPExcel->getActiveSheet()->getStyle('M')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
		    $objPHPExcel->getActiveSheet()->getStyle('N')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
		    $objPHPExcel->getActiveSheet()->getStyle('O')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
		    $objPHPExcel->getActiveSheet()->getStyle('P')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
		    $objPHPExcel->getActiveSheet()->getStyle('Q')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
		    $objPHPExcel->getActiveSheet()->getStyle('R')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
		    $objPHPExcel->getActiveSheet()->getStyle('S')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
		    
			
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
			$objPHPExcel->getActiveSheet()->getStyle('k2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('L2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('M2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('N2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('O2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('P2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('Q2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('R2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('S2')->getFont()->setBold(true);
			
			
			$name = '金融合伙人发布项目管理信息统计表(截止时间：'.date('Y-m-d H:i:s' , time()).')';
			$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', $name)
					->setCellValue('A2', '发布项目id')
					->setCellValue('B2', '发布人id')
					->setCellValue('C2', '发布人名称')
					->setCellValue('D2', '业务类型')
					->setCellValue('E2', '项目名称')
					->setCellValue('F2', '业务描述')
					->setCellValue('G2', '项目亮点')
					->setCellValue('H2', '所在省份')
					->setCellValue('I2', '所属行业')
					->setCellValue('J2', '融资规模/交易规模/产品规模/所需资金/投入本金(万元)')
					->setCellValue('K2', '今年净利润')
					->setCellValue('L2', '融资用途')
					->setCellValue('M2', '业务有效期')
					->setCellValue('N2', '投资者偏好')
					->setCellValue('O2', '发布时间')
					->setCellValue('P2', '截止时间')
					->setCellValue('Q2', '内部客服id')
					->setCellValue('R2', '状态（展示/隐藏/删除）')
					->setCellValue('S2', '联系人身份');
					
					
			$objPHPExcel->getActiveSheet()->setTitle(date('Y-m-d' , time()));
			$objPHPExcel->setActiveSheetIndex(0);
			
			$i = 3;
			foreach($datas as $data){
				$objPHPExcel->getActiveSheet()->setCellValue('A'. $i, $data['pr_id']);
				$objPHPExcel->getActiveSheet()->setCellValue('B'. $i, $data['uid']);
				$objPHPExcel->getActiveSheet()->setCellValue('C'. $i, $data['realName']);
				$objPHPExcel->getActiveSheet()->setCellValue('D'. $i, $data['dict']);
				$objPHPExcel->getActiveSheet()->setCellValue('E'. $i, $data['name']);
				$objPHPExcel->getActiveSheet()->setCellValue('F'. $i, $data['business_des']);
				$objPHPExcel->getActiveSheet()->setCellValue('G'. $i, $data['pro_highlight']);
				$objPHPExcel->getActiveSheet()->setCellValue('H'. $i, $data['province']);
				$objPHPExcel->getActiveSheet()->setCellValue('I'. $i, $data['industry']);
				$objPHPExcel->getActiveSheet()->setCellValue('J'. $i, $data['financing_scale']);
				$objPHPExcel->getActiveSheet()->setCellValue('K'. $i, $data['ly_net_profit']);
				$objPHPExcel->getActiveSheet()->setCellValue('L'. $i, $data['financing_purpose']);
				$objPHPExcel->getActiveSheet()->setCellValue('M'. $i, $data['validity_period']);
				$objPHPExcel->getActiveSheet()->setCellValue('N'. $i, $data['value']);
				$objPHPExcel->getActiveSheet()->setCellValue('O'. $i, $data['create_time']);
				$objPHPExcel->getActiveSheet()->setCellValue('P'. $i, $data['deadline']);
				$objPHPExcel->getActiveSheet()->setCellValue('Q'. $i, $data['inner_cs']);
				$objPHPExcel->getActiveSheet()->setCellValue('R'. $i, $data['is_show']);
				$objPHPExcel->getActiveSheet()->setCellValue('S'. $i, $data['contact_status']);
				
				$i ++;
			}
		
        // 输出Excel表格到浏览器下载
        ob_end_clean();
        //header('Content-Type: application/vnd.ms-excel');
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
 * 资金需求管理excel导出
 * @param unknown $datas
 */
function organize_require_show($datas)
{
	//加载第三方类库
	vendor('PHPExcel.PHPExcel');
	//实例化表格对象
	$objPHPExcel = new \PHPExcel();
	
		$objPHPExcel->getProperties()					->setCreator("Financial partner")
					->setTitle("金融合伙人发布资金管理信息统计表")
					->setSubject("金融合伙人发布资金管理信息统计表")
					->setDescription("金融合伙人发布资金管理信息统计表");
			$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
			$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);
			
			//设置宽width
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(18);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(44);
			$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(18);
			$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(10);
			
			//设置水平对齐方式
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
			$objPHPExcel->getActiveSheet()->getStyle('K')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('L')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('M')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('N')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('O')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('P')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('Q')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('R')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			//设置垂直居中
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
			$objPHPExcel->getActiveSheet()->getStyle('K')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('L')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('M')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('N')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('O')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('P')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('Q')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('R')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			
			//第二三行设置加粗
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
			$objPHPExcel->getActiveSheet()->getStyle('K2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('L2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('M2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('N2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('O2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('P2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('Q2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('R2')->getFont()->setBold(true);
			
			//excel文件名表名
			$name = '金融合伙人发布资金管理信息统计表(截止时间：'.date('Y-m-d H:i:s' , time()).')';
			//excel标题
			$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', $name)
					->setCellValue('A2', '发布资金id')
					->setCellValue('B2', '发布人id')
					->setCellValue('C2', '发布人名称')
					->setCellValue('D2', '业务类型')
					->setCellValue('E2', '企业/项目/产品名称')
					->setCellValue('F2', '资金类型')
					->setCellValue('G2', '投资期限')
					->setCellValue('H2', '投资标准')
					->setCellValue('I2', '所在省份')
					->setCellValue('J2', '状态')
					->setCellValue('K2', '业务描述')
					->setCellValue('L2', '所属行业')
					->setCellValue('M2', '投资规模(万元)')
					->setCellValue('N2', '投资方式')
					->setCellValue('O2', '发布时间')
					->setCellValue('P2', '截止时间')
					->setCellValue('Q2', '内部客服id')
					->setCellValue('R2', '联系人身份');
			$objPHPExcel->getActiveSheet()->setTitle(date('Y-m-d' , time()));
			$objPHPExcel->setActiveSheetIndex(0);
			
			//第三行开始为输出数据
			$i = 3;
			foreach($datas as $data){
				$objPHPExcel->getActiveSheet()->setCellValue('A'. $i, $data['or_id']);
				$objPHPExcel->getActiveSheet()->setCellValue('B'. $i, $data['uid']);
				$objPHPExcel->getActiveSheet()->setCellValue('C'. $i, $data['realName']);
				$objPHPExcel->getActiveSheet()->setCellValue('D'. $i, $data['service_type']);
				$objPHPExcel->getActiveSheet()->setCellValue('E'. $i, $data['name']);
				$objPHPExcel->getActiveSheet()->setCellValue('F'. $i, $data['fund_type']);
				$objPHPExcel->getActiveSheet()->setCellValue('G'. $i, $data['financing_period']);
				$objPHPExcel->getActiveSheet()->setCellValue('H'. $i, $data['invest_target']);
				$objPHPExcel->getActiveSheet()->setCellValue('I'. $i, $data['province']);
				$objPHPExcel->getActiveSheet()->setCellValue('J'. $i, $data['is_show']);
				$objPHPExcel->getActiveSheet()->setCellValue('K'. $i, $data['business_des']);
				$objPHPExcel->getActiveSheet()->setCellValue('L'. $i, $data['industry']);
				$objPHPExcel->getActiveSheet()->setCellValue('M'. $i, $data['invest_scale']);
				$objPHPExcel->getActiveSheet()->setCellValue('N'. $i, $data['invest_mode']);
				$objPHPExcel->getActiveSheet()->setCellValue('O'. $i, $data['create_time']);
				$objPHPExcel->getActiveSheet()->setCellValue('P'. $i, $data['deadline']);
				$objPHPExcel->getActiveSheet()->setCellValue('Q'. $i, $data['inner_cs']);
				$objPHPExcel->getActiveSheet()->setCellValue('R'. $i, $data['contact_status']);
				$i ++;
			}
		
        //输出Excel表格到浏览器下载
        ob_end_clean();
        //header('Content-Type: application/vnd.ms-excel');
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
 * 日志写入
 * @param 信息内容  $message
 */
function faLog($message)
{
    if (is_array($message) && count($message) > 0) {
        $message = json_encode($message);
    }
    error_log("[".date('Y-m-d H:i:s')."] ".$message."\r\n", 3, LOG_PATH."/fa.log");
}