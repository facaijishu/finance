<?php
namespace app\console\controller;

class Dividend extends ConsoleBase{
    public function index(){
        $arr    = [];
        //$date   = config("start_time");
        $model  = model("Dividend");
        $month  = $model->order("month desc")->find();
        $date   = $month['month'];
        $now    = date('Y-m' , time());
        $result = $this->getMonthArray($arr , $date , $now);
     
        $asd = true;
        foreach ($result as $key => $value) {
            $dividend = $model->where(['month' => $value])->find();
            if(empty($dividend)){
                $asd = $model->createMonthData($result , $key , true);
            } else {
                if($dividend['accounting'] == -1){
                    $asd = $model->createMonthData($result , $key , false);
                }
            }
        }
        if($asd){
            $this->assign('result' , 'true');
        } else {
            $this->assign('result' , 'false');
        }
        return view();
    }
    
    public function read(){
        $return     = [];
        $order      = $this->request->get('order/a', []);
        $start      = $this->request->get('start', 0);
        $length     = $this->request->get('length', config('paginate.list_rows'));
        $dividend   = model('Dividend');
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $dividend->order($_order);
        }
        $dividend->field('SQL_CALC_FOUND_ROWS *');
        $list = $dividend->limit($start, $length)->select();
        $result = $dividend->query('SELECT FOUND_ROWS() as count');
        $total = $result[0]['count'];
        $data = [];
        foreach ($list as $key => $item) {
            if($item['ratio'] == 0.00){
                $list[$key]['ratio'] = '-';
            }
            if($item['total_money'] == 0.00){
                $list[$key]['total_money'] = '-';
            }
            $data[] = $item->toArray();
        }
        $return['data'] = $data;
        $return['recordsFiltered'] = $total;
        $return['recordsTotal'] = $total;

        echo json_encode($return);
    }
    
    public function detail(){
        $id         = $this->request->param('id/d');
        $model      = model("Dividend");
        $info       = $model->getDividendInfoById($id);
        $start_time = strtotime($info['month'].'-01 00:00:00');
        $end_time   = date('Y-m-d H:i:s',strtotime('+1 month' , $start_time));
        
        $result     = model("IntegralRecord")->alias("ir")
                                             ->join("Member m","ir.openId=m.openId")
                                             ->field("ir.*")
                                             ->where('ir.through','gt', $info['month'].'-01 00:00:00')
                                             ->where('ir.through','lt', $end_time)
                                             ->where('m.userType','eq',2)
                                             ->select();
        $model      = model("Member");
        foreach ($result as $key => $value) {
            $user = $model->getMemberInfoById($value['uid']);
            $result[$key]['uid'] = $user['realName'];
        }
        
        $flg   = 0;
        $nowy  = date('Y',time());
        $gety  = date('Y',$start_time);
        if($gety<$nowy){
            $flg = 1;
        }else if($gety=$nowy){
            $nowm  = date('m',time());
            $getm  = date('m',$start_time);
            if($getm<$nowm){
                $flg = 1;
            }
        }
        $this->assign('flg' , $flg);
        
        $this->assign('list' , $result);
        $this->assign("info" , $info);
        return view();
    }
    
    public function account(){
        $data = input();
        $model = model("Dividend");
        if($model->accountDividend($data)){
            $data['url'] = url('Dividend/index');
            $this->result($data, 1, '核算成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '核算失败';
            $this->result('', 0, $error, 'json');
        }
    }
    
    public function grant(){
        $id = $this->request->param('id/d');
        $model = model("Dividend");
        if($result = $model->grantDividend($id)){
            $data['url'] = url('Dividend/index');
            $this->result($result, 1, '发放成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '发放失败';
            $this->result('', 0, $error, 'json');
        }
    }

    public function getMonthArray($arr , $date , $now){
        if($date <= $now){
            array_push($arr, $date);
            $time = strtotime($date.'-01 00:00:00');
            $time = strtotime('+1 month' , $time);
            $date = date('Y-m' , $time);
            $arr = $this->getMonthArray($arr , $date , $now);
        }
        return $arr;
    }
}