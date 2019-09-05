<?php 
namespace app\home\model;
use think\Model;
class BaseModel extends Model
{   
    protected $resultSetType = 'collection';
    protected $code;
    protected $msg;
    protected function result($data = '',$code = 200,$msg = '')
    {  
       $arr = [];
       $this->code = $code;
       $this->msg = $msg;
       $arr['data'] = $data;
       $arr['code'] = $this->code;
       $arr['msg'] = $this->msg;
       echo json_encode($arr);
    }
}