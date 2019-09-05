<?php

namespace app\console\model;

use think\Model;
use sys\Wechat;

class QrcodeDirection extends Model
{
    public function addQrCode($data){
        //开启事务
        $this->startTrans();
        try{
            $filename = $data['id'].genRandomString(10).'.jpg';
            $weObj = new Wechat(config("WeiXinCg"));
            $access_token = getAccessTokenFromFile();
            $result = $weObj->checkAuth('', '', $access_token);
            $result = $weObj->getQRCode($data['id'], 1);
            $result = $weObj->getQRUrl($result['ticket']);
            $result = getImage($result, config("QRCodePath"), $filename, 1);
            if($result['error']){
                $this->error = $result['msg'];
                return false;
            } else {
                $this->where(['id' => $data['id']])->update(['qrcode_path' => $filename]);
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