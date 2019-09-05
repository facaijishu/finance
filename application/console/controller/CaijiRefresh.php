<?php

namespace app\console\controller;

class CaijiRefresh extends ConsoleBase{
    public function dingzeng () {
        $zfmxes_add = model('ZfmxesAdd');
        $list = $zfmxes_add->getZfmxesAddList();
        foreach ($list as $key => $value) {
            $zfmxes_info = model('Zfmxes')->where(['neeq' => $value['neeq'], 'plannoticeddate' => $value['plannoticeddate']])->order('id desc')->limit(1)->find();
            $lirun = model('Cwbbsjs')->where(['neeq' => $value['neeq'], 'type' => 1, 'bbxm' => 'NETPROFIT'])->order('bbrq desc')->limit(1)->find();
            $shouru = model('Cwbbsjs')->where(['neeq' => $value['neeq'], 'type' => 1, 'bbxm' => 'OPERATEREVE'])->order('bbrq desc')->limit(1)->find();
            if ($value['lirun'] == $lirun['xmsz'] && $value['shouru'] == $shouru['xmsz'] && $value['sublevel'] == $zfmxes_info['sublevel'] && $value['sec_uri_tyshortname'] == $zfmxes_info['sec_uri_tyshortname']) {
                echo '无需更新:'.$value['id'].'-'.$value['neeq'];
            } else {
                $lirun = empty($lirun) ? 0 : $lirun['xmsz'];
                $shouru = empty($shouru) ? 0 : $shouru['xmsz'];
                $result = $zfmxes_add->where(['id' => $value['id']])->update(['lirun' => $lirun, 'shouru' => $shouru, 'sublevel' => $zfmxes_info['sublevel'], 'sec_uri_tyshortname' => $zfmxes_info['sec_uri_tyshortname']]);
                if($result){
                    echo $result.'is ok';
                } else {
                    echo $result.'is error';
                }
            }
        }
    }
    public function dazong () {
        $zryxes_effect = model('ZryxesEffect');
        $list = $zryxes_effect->getZryxesEffectList();
        foreach ($list as $key => $value) {
            $lirun = model('Cwbbsjs')->where(['neeq' => $value['neeq'], 'type' => 1, 'bbxm' => 'NETPROFIT'])->order('bbrq desc')->limit(1)->find();
            $shouru = model('Cwbbsjs')->where(['neeq' => $value['neeq'], 'type' => 1, 'bbxm' => 'OPERATEREVE'])->order('bbrq desc')->limit(1)->find();
            if ($value['lirun'] == $lirun['xmsz'] && $value['shouru'] == $shouru['xmsz']) {
                echo '无需更新:'.$value['id'].'-'.$value['neeq'];
            } else {
                $lirun = empty($lirun) ? 0 : $lirun['xmsz'];
                $shouru = empty($shouru) ? 0 : $shouru['xmsz'];
                $result = $zryxes_effect->where(['id' => $value['id']])->update(['lirun' => $lirun, 'shouru' => $shouru]);
                if($result){
                    echo $result.'is ok';
                } else {
                    echo $result.'is error';
                }
            }
        }
    }
}