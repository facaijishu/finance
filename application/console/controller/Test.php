<?php
namespace app\console\controller;

use \sys\Tree;

class Test extends ConsoleBase
{
  
    public function wechat(){
        $list = model("aa_uid")->limit(0,10)->select();
        foreach ($list as $key => $value) {
            $isFollow = isFollow($value['openId'],getAccessTokenFromFile());
            if($isFollow){
                $isFollow = $value['uid'].",已关注";
            }else{
                $isFollow = $value['uid'].",未关注";
            }
            echo $isFollow;
        }
    }

}
