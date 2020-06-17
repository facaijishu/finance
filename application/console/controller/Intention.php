<?php
namespace app\console\controller;

class Intention extends ConsoleBase{
    public function index(){
        return view();
    }
    
    public function zryxes(){
        return view();
    }
    
    /**
     * 定增意向列表
     */
    public function zfmxesread(){
        $return         = [];
        $order          = $this->request->get('order/a', []);
        $start          = $this->request->get('start', 0);
        $length         = $this->request->get('length', config('paginate.list_rows'));
        $create_date1   = $this->request->get('create_date1', '');
        $create_date2   = $this->request->get('create_date2', '');
        $neeq           = $this->request->get('neeq', '');

        $zfmxes_intention = model('ZfmxesIntention');
        //搜索条件
        if('' !== $create_date1 && '' !== $create_date2) {
            $zfmxes_intention->where('create_time','between',[strtotime($create_date1.'00:00:00') , strtotime($create_date2.'23:59:59')]);
        }elseif ('' !== $create_date1 && '' == $create_date2) {
            $zfmxes_intention->where('create_time','>',strtotime($create_date1.'00:00:00'));
        }elseif ('' == $create_date1 && '' !== $create_date2) {
            $zfmxes_intention->where('create_time','<',strtotime($create_date2.'23:59:59'));
        }
        //搜索条件
        if('' !== $neeq) {
            $zfmxes_intention->where('neeq','like','%'.$neeq.'%');
        }
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $zfmxes_intention->order($_order);
        }
        $zfmxes_intention->field('SQL_CALC_FOUND_ROWS *');
        $list   = $zfmxes_intention->limit($start, $length)->select();
        $result = $zfmxes_intention->query('SELECT FOUND_ROWS() as count');
        $total  = $result[0]['count'];
        $data   = [];
        $model  = model("Member");
        foreach ($list as $key => $item) {
            $info = $model->getMemberInfoById($item['create_uid']);
            $list[$key]['userName'] = $info['userName'];
            $data[] = $item->toArray();
        }
        $return['data']             = $data;
        $return['recordsFiltered']  = $total;
        $return['recordsTotal']     = $total;

        echo json_encode($return);
    }
    
    /**
     * 大宗意向列表
     */
    public function zryxesread(){
        $return         = [];
        
        $order          = $this->request->get('order/a', []);
        $start          = $this->request->get('start', 0);
        $length         = $this->request->get('length', config('paginate.list_rows'));
        $create_date1   = $this->request->get('create_date1', '');
        $create_date2   = $this->request->get('create_date2', '');
        $neeq           = $this->request->get('neeq', '');

        $zryxes_intention = model('ZryxesIntention');
        //搜索条件
        if('' !== $create_date1 && '' !== $create_date2) {
            $zryxes_intention->where('create_time','between',[strtotime($create_date1.'00:00:00') , strtotime($create_date2.'23:59:59')]);
        }elseif ('' !== $create_date1 && '' == $create_date2) {
            $zryxes_intention->where('create_time','>',strtotime($create_date1.'00:00:00'));
        }elseif ('' == $create_date1 && '' !== $create_date2) {
            $zryxes_intention->where('create_time','<',strtotime($create_date2.'23:59:59'));
        }
        //搜索条件
        if('' !== $neeq) {
            $zryxes_intention->where('neeq','like','%'.$neeq.'%');
        }
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $zryxes_intention->order($_order);
        }
        $zryxes_intention->field('SQL_CALC_FOUND_ROWS *');
        $list   = $zryxes_intention->limit($start, $length)->select();
        $result = $zryxes_intention->query('SELECT FOUND_ROWS() as count');
        $total  = $result[0]['count'];
        $data   = [];
        $model  = model("Member");
        foreach ($list as $key => $item) {
            $info = $model->getMemberInfoById($item['create_uid']);
            $list[$key]['userName'] = $info['userName'];
            $data[] = $item->toArray();
        }
        $return['data']             = $data;
        $return['recordsFiltered']  = $total;
        $return['recordsTotal']     = $total;

        echo json_encode($return);
    }
    
    
    /**
     * 定增意向详细
     */
    public function detail_zfmxes(){
        $id     = $this->request->param('id/d');
        
        $model  = model("ZfmxesIntention");
        $info   = $model->getZfmxesIntentionInfoById($id);
        
        $model  = model("CaijiRemark");
        $remark = $model->getRemarkByCodeDateAndType($info['neeq'] , $info['plannoticeddate'] , 'Intention' , 0);
        
        $info['remark'] = $remark;
        $this->assign("info" , $info);
        return view();
    }
    
    
    /**
     * 大宗意向详细
     */
    public function detail_zryxes(){
        $id     = $this->request->param('id/d');
        
        $model  = model("ZryxesIntention");
        $info   = $model->getZryxesIntentionInfoById($id);
        
        $model  = model("CaijiRemark");
        $remark = $model->getRemarkByCodeDateAndType($info['neeq'] , '' , 'Intention' , $info['id']);
        
        $info['remark'] = $remark;
        $this->assign("info" , $info);
        return view();
    }
}