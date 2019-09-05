<?php

namespace app\console\model;

use think\Model;

class StYanbaos extends Model
{
    public function getStYanbaosInfoById($id){
        $info = $this->where(['id' => $id])->find();
        $authors = db("st_yb_authors")->where(['yb_id' => $info['yb_id']])->select();
        $author = '';
        foreach ($authors as $key => $value) {
            $author .= ','.$value['auth'];
        }
        $author = trim($author, ',');
        $info['author'] = $author;
        return $info;
    }
}