<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/9/12
 * Time: 16:12
 */

namespace app\home\controller;
use app\home\model\Affiche;
use think\Db;

/**
 * Class Notice
 * @package 通知公告
 */
class Notice extends Base {
    /**
     * 主页
     */
    public function index(){
        $this->anonymous(); //判断是否是游客
        //学习资料
        $map = array(
            'status' => array('egt',0)
        );
        $Affiche = new Affiche();
        $this->assign('list',$Affiche->get_list($map));
        return $this->fetch();
    }

    /**
     * 更多  通知
     */
    public function listmore(){
        $len = input('length');
        $map = array(
            'status' => array('egt',0),
        );
        $Affiche = new Affiche();
        $list = $Affiche->get_list($map,$len);
        if($list){
            return $this->success("加载成功",'',$list);
        }else{
            $this->error("加载失败");
        }
    }
    /**
     *  相关通知  活动通知 详细页
     */
    public function forumnotice(){
        //判断是否是游客
        $this->anonymous();
        $this->jssdk();
        $id = input('id');
        $this->assign('info',$this->content(2,$id));
        return $this->fetch();
    }

}