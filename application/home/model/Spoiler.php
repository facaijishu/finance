<?php

namespace app\home\model;

use think\Model;

class Spoiler extends Model
{
    public function getSpoilerInfo($id){
        $result = $this->where(['sp_id' => $id])->find();
        $img = $result['img'];
        if($img !== ''){
            $arr_file = [];
            $arr = explode(',', $img);
            $model = model("MaterialLibrary");
            $result['img_length'] = count($arr);
            if ($result['img_length'] == 1){
                $info = $model->getMaterialInfoById($arr[0]);
                //$result['img'] = $info['url'];
                //判断是img还是pdf
                $pos = strrpos($info['url'],'.');
                if ($pos) {
                  $suffix = substr($info['url'],$pos+1);
                  if ($suffix == 'pdf') {
                     $result['pdf'] = $info['url'];
                     //如果pdf的名字太长,以...省略
                       $result['name'] = substrPdfName($info['name']);
                     //获取pdf大小,单位为kb
                       $result['pdfsize'] = changeTokb($info['filesize']);      
                  } else {
                     $result['img'] = $info['url'];
                     
                  }
                }
            } else {
                foreach ($arr as $key1 => $value1) {
                    $info = $model->getMaterialInfoById($value1);
                   // $arr[$key1] = $info['url_thumb'];
                    //$arr_img[$key1] = $info['url'];
                     //判断是img还是pdf
                    $pos = strrpos($info['url'],'.');
                    if($pos){
                        $suffix = substr($info['url'],$pos+1);
                        if ($suffix == 'pdf') {
                            
                           //如果pdf的名字太长,以...省略
                           $name = substrPdfName($info['name']);
                           $pdfsize = changeTokb($info['filesize']);
                           $arr[$key1] = ['pdf'=>$info['url'],'name'=>$name,'pdfsize'=>$pdfsize]; 
                           $arr_file[$key1]['pdf'] = $info['url']; 
                       }else{
                           $arr[$key1] = ['img'=>$info['url_thumb']];
                           $arr_file[$key1]['img'] = $info['url'];
                       }
                    }
                }
                $result['img'] = $arr;
                //$result['img_url'] = $arr_img;
                $result['file_url'] = $arr_file;
            }
        }
        $thumbs_up = $result['thumbs_up'];
        if($thumbs_up != ''){
            $arr = explode(',', $thumbs_up);
            $result['thumbs_up'] = $arr;
            $model = model("Member");
            foreach ($arr as $key1 => $value1) {
                $info = $model->getMemberInfoById($value1);
                $arr[$key1] = $info['userPhoto'];
            }
            $result['thumbs_up_img'] = $arr;
        }
        $model = model("SpoilerComments");
        $list = [];
        $list = $model->getSpoilerCommentsList($result['sp_id']);
        if(!empty($list)){
            foreach ($list as $key => $value) {
                $list[$key]['list'] = $model->getSpoilerCommentsList($value['sp_id'] , $value['spc_id']);
            }
        }
        $result['comments'] = $list;
        return $result;
    }
    public function getSpoilerList($id = 0 , $num = 0){
        $num = $num== 0 ? config("ins_num") : $num;
        $result = [];
        if($id == 0){
            $result = $this->where(['status' => 1])->order("create_time desc")->limit($num)->select();
        } else {
            $result = $this->where(['status' => 1])->where('sp_id','lt',$id)->order("create_time desc")->limit($num)->select();
        }
        if(!empty($result)){
            //出去列表页二添加
            foreach ($result as $key => $value) {
                $result[$key] = $this->getSpoilerInfo($value['sp_id']);
            }
        }
        return $result;
    }
    public function createThumbsUp($id){
        if(empty($id)){
            $this->error = '剧透id不存在';
            return false;
        }
        
        //开启事务
        $this->startTrans();
        try{
            $info = $this->where(['sp_id' => $id])->find();
            $arr = explode(',', $info['thumbs_up']);
            if(in_array(session('FINANCE_USER.uid'), $arr)){
                $this->error = '不可重复点赞';
                return false;
            } else {
                array_push($arr, session('FINANCE_USER.uid'));
                $thumbs_up = trim(implode(',', $arr) , ',');
                $this->where(['sp_id' => $id])->update(['thumbs_up' => $thumbs_up]);
            }
                // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
}
