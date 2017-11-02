<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2016/11/2
 * Time: 13:21
 */
namespace app\home\controller;
use app\home\model\WechatUser;
use app\home\model\WechatDepartment;
class Structure extends Base{
    /*
     * 组织架构主页
     */
    public function index(){
        $Dep = WechatDepartment::where(['id' => ['neq',1],'status' => 1])->order('id asc')->select();
        $this->assign('list',$Dep);
        return $this->fetch();
    }
    /*
     * 组织架构详情页
     */
    public function detail(){
        $this ->checkAnonymous();
        $pid = input('pid');
        $list = WechatUser::where(['department' => $pid,'state' => 1])->order('id desc')->select();
        $this->assign('list',$list);
        return $this->fetch();
    }
}
