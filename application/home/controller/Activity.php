<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/6/17
 * Time: 17:40
 */
namespace app\home\controller;
use app\home\model\WechatDepartment;
use app\home\model\Comment;
use app\home\model\Picture;
use app\home\model\Notice;
/**
 *  组织活动  控制器
 */
class Activity extends Base{
    /*
     * 活动通知  主页
     */
    public function index(){
        $this->checkAnonymous();
        $Notice = new Notice();
        $map = array(
            'type' => 2,
            'status' => ['egt',0]
        );
        $list = $Notice->get_list($map);  // 活动通知
        $maps = array(
            'type' => 1,
            'status' => ['egt',0]
        );
        $lists = $Notice->get_list($maps); // 活动展示
        $this->assign('list',$list);
        $this->assign('lists',$lists);
        return $this ->fetch();
    }
    /*
     * 活动  列表 更多
     */
    public function morelist(){
        $this->checkAnonymous();
        $Notice = new Notice();
        $type = input('post.type');  // 0 活动通知 1 活动展示
        $len = input('post.length');
        if ($type == 0){
            $con = 2;
        }else{
            $con = 1;
        }
        $maps = array(
            'type' => $con,
            'status' => ['egt',0]
        );
        $list = $Notice->get_list($maps,$len);
        if ($list){
            return $this->success('加载成功','',$list);
        }else{
            $this->error('加载失败');
        }
    }
    // 活动通知  详情
    public function detail(){
        //判断是否是游客
        $this ->anonymous();
        $this->checkAnonymous();
        //获取jssdk
        $this ->jssdk();
        $id = input('id');
        $this->assign('detail',$this->content(4,$id));
        return $this->fetch();
    }
    /* 活动展示   详情 */
    public function showdetails(){
        //判断是否是游客
        $this ->anonymous();
        $this->checkAnonymous();
        //获取jssdk
        $this ->jssdk();
        $id = input('id');
        $this->assign('detail',$this->content(4,$id));
        return $this->fetch();
    }
}