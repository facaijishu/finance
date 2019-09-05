<?php
namespace app\console\controller;

class CaijiRemark extends ConsoleBase{
    public function add(){
        $data = input();
        $model = model("CaijiRemark");
        if($model->createCaijiRemark($data)){
            $data['url'] = url($data['type'].'/'.$data['detail'] , ['id' => $data['id']]);
            $this->result($data, 1, '添加备注成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '添加备注失败';
            $this->result('', 0, $error, 'json');
        }
    }
}