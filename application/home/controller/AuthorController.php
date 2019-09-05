<?php

namespace app\home\controller;
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods:POST');
class AuthorController{
    public function collection_study(){
        $data = input();
        $model = model("Member");
        if($model->setCollectionStudy($data)){
            echo '阅读记录添加成功';
//            $this->result('', 1, '阅读记录添加成功', 'json');
        } else {
            $error = $model->getError() ? $model->getError() : '系统出错,请联系管理员';
            echo $error;
//            $this->result('', 0, $error, 'json');
        }
    }
}
    