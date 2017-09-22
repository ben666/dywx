<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/9/12
 * Time: 16:12
 */

namespace app\home\controller;
use app\home\model\Browse;
use app\home\model\Comment;
use app\home\model\Like;
use app\home\model\Picture;
use app\home\model\WechatUser;
use think\Controller;

use app\home\model\Notice as NoticeModel;
use think\Db;

/**
 * Class Notice
 * @package 信息驿站
 */
class College extends Base {
    /**
     * 主页
     */
    public function index(){
        $this->anonymous(); //判断是否是游客
        //学习资料
        $map = array(
            'type' => 1,
            'status' => array('egt',0)
        );
        $maps = array(
            'type' => 2,  // 通知
            'status' => array('egt',0)
        );
        $Notice = new NoticeModel();
        $this->assign('list',$Notice->get_list($map));
        $this->assign('fnotice',$Notice->get_list($maps));
        return $this->fetch();
    }

    /**
     * 更多  通知
     */
    public function leadlistmore(){
        $len = input('length');
        $type = input('type');  // 0 学习资料 1 通知
        $map = array(
            'type' => $type+1,
            'status' => array('egt',0),
        );
        $Notice = new NoticeModel();
        $list = $Notice->get_list($map,$len);
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
        $this->assign('info',$this->content(4,$id));
        return $this->fetch();
    }

}