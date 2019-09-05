<?php
namespace app\console\controller;

class ActivitySign extends ConsoleBase{
    public function index(){
        
        $activity = model("Activity");
        $list = $activity->order("a_id desc")->select();
        
        $this->assign('list' , $list);
        return view();
    }
    
    public function read(){
        $return = [];
        $order  = $this->request->get('order/a', []);
        $start  = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));
        
        $a_id = $this->request->get('a_id', '');
        
        $activity_sign = model('ActivitySign');
        $activity      = model('Activity');
        if('' !== $a_id) {
            
            $info = $activity->where(['a_id'=>$a_id])->find();
            
            $activity_sign->where(['a_id'=>$a_id]);
            
            //排序
            if(!empty($order)) {
                foreach ($order as $item) {
                    $_order = [];
                    $_order[$item['column']] = $item['dir'];
                }
                $activity_sign->order($_order);
            }
            
            $activity_sign->field('SQL_CALC_FOUND_ROWS *');
            $list   = $activity_sign->limit($start, $length)->select();
            $result = $activity_sign->query('SELECT FOUND_ROWS() as count');
            $total  = $result[0]['count'];
            foreach ($list as $key => $item) {
                $list[$key]['act_name'] = $info['act_name'];
                if($item['sign_time']==0){
                    $list[$key]['sign_time'] = "未签到";
                }else{
                    $list[$key]['sign_time'] = date('Y-m-d H:i' , $item['sign_time']);
                }
                $data[] = $item->toArray();
            }
            if(empty($data)){
                $return['data']         = '';
                $return['recordsFiltered']  = 0;
                $return['recordsTotal']     = 0;
            }else{
                $return['data']             = $data;
                $return['recordsFiltered']  = $total;
                $return['recordsTotal']     = $total;
            }
        }else{
            $return['data']             = '';
            $return['recordsFiltered']  = 0;
            $return['recordsTotal']     = 0;
        }
        echo json_encode($return);
    }
    
    /**
     * 活动报名数据导入
     * @param unknown $datas
     */
    function upload()
    {
        vendor('PHPExcel.PHPExcel');
        $objPHPExcel = new \PHPExcel();
        
        $file = $_FILES['file'];
        $file_types = explode ( ".", $file['name'] );
        $type = end($file_types);
        $excel_type = array('xls','xlsx');
        if (!in_array(strtolower(end($file_types)),$excel_type)){
            $this->result('', 0, "不是Excel文件，请重新上传!", 'json');
        }
        
        $path = ROOT_PATH . 'public' . DS . 'uploads' . DS . 'activity' . DS . 'excel' ;
        if(!is_dir($path)){
            mkdir($path , 0777 , true);
        }
        $filename  = date('ymd').rand(1000, 9999).'.'.$type;
        $result    = move_uploaded_file($file['tmp_name'], $path.DS.$filename);
        $file_path = $path.DS.$filename;
        if($result){
            if ($type == 'xlsx') {
                $objReader   = \PHPExcel_IOFactory::createReader('Excel2007');
                $objPHPExcel = $objReader->load($file_path, 'utf-8');
            } elseif ($type == 'xls') {
                $objReader   = \PHPExcel_IOFactory::createReader('Excel5');
                $objPHPExcel = $objReader->load($file_path, 'utf-8');
            }
            $sheet          = $objPHPExcel->getSheet(0);
            $highestRow     = $sheet->getHighestRow(); // 取得总行数
            $highestColumn  = $sheet->getHighestColumn(); // 取得总列数
            $ar         = array();
            $i          = 0;
            $importRows = 0;
            $data       = [];
            $datas      = [];
            for ($j = 2; $j <= $highestRow; $j++) {
                $importRows++;
                $data['a_id']       = (int)$objPHPExcel->getActiveSheet()->getCell("A$j")->getValue();//需要导入的活动编号
                $data['mobile']     = (string)$objPHPExcel->getActiveSheet()->getCell("B$j")->getValue();//需要导入的手机
                $data['realName']   = (string)$objPHPExcel->getActiveSheet()->getCell("C$j")->getValue();//需要导入的姓名
                $data['company']    = (string)$objPHPExcel->getActiveSheet()->getCell("D$j")->getValue();//需要导入的公司
                $data['position']   = (string)$objPHPExcel->getActiveSheet()->getCell("E$j")->getValue();//需要导入的职位
                $data['channel']    = (string)$objPHPExcel->getActiveSheet()->getCell("F$j")->getValue();//需要导入的渠道来源
                
                array_push($datas,$data);
            }
            $model= model('ActivitySign');
            $res  = $model->addSign($datas);
            if($res){
                $this->result('', 1, '数据导入成功!', 'json');
            }else{
                $this->result('', 0, '数据导入发生错误，请重试！', 'json');
            }
        }else{
            $this->result('', 0, '上传文件失败!', 'json');
        }
    }
}