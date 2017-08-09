<?php
/**
 * Created by PhpStorm.
 * User: laowang <958364865@qq.com>
 * Date: 2017/8/8
 * Time: 17:53
 */

namespace app\admin\model;
use think\Model;

class BranchReceive extends Model
{
    /**
     * 获取微信用户信息
     */
    public function user() {
        return $this->hasOne('WechatUser','userid','userid');
    }

    /**
     * 获取订单信息
     */
    public function order() {
        return $this->hasOne('Branch','id','rid');
    }
}