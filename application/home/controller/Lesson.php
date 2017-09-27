<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/25 0025
 * Time: 上午 10:58
 */

namespace app\home\controller;
use app\home\model\Party;
/**
 * Class Lesson
 * @package app\home\controller  三会一课
 */
class Lesson extends Base
{
    /**
     * @return mixed  主页
     */
    public function index(){
        $this->checkAnonymous();
        $Party = new Party();
        $notice = $Party->get_list(['type' => 1,'status' => ['egt',0]]);  // 相关通知
        $meet = $Party->get_list(['type' => 2,'status' => ['egt',0]]);  // 会议情况
        $party = $Party->get_list(['type' => 3,'status' => ['egt',0]]); // 党课情况
        $this->assign('notice',$notice);
        $this->assign('meet',$meet);
        $this->assign('party',$party);
        return $this ->fetch();
    }

    /**
     * @return mixed 更多通知
     */
    public function noticemore(){
        $this->checkAnonymous();
        $Party = new Party();
        $list = $Party->get_list(['type' => 1,'status' => ['egt',0]],0,true);
        $this->assign('list',$list);
        return $this ->fetch();
    }
    /**
     * 加载更多   type : 1 相关通知 2  会议情况  3 党课情况
     */
    public function more(){
        $this->checkAnonymous();
        $type = input('post.type');
        $len = input('post.length');
        $Party = new  Party();
        $list = $Party->get_list(['type' => $type,'status' => ['egt',0]],$len);
        if ($list){
            return $this->success('加载成功','',$list);
        }else{
            return $this->error('没有更多了');
        }
    }
    /**
     * @return mixed 更多  会议情况 2  党课情况 3
     */
    public function meetingmore(){
        $this->checkAnonymous();
        $type = input('get.type');
        $Party = new Party();
        $list = $Party->get_list(['type' => $type,'status' => ['egt',0]],0,true);
        $this->assign('list',$list);
        $this->assign('type',$type);
        return $this ->fetch();
    }
    /**
     * 相关通知  详情
     */
    public function noticedetail(){
        $this->checkAnonymous();
        //游客模式
        $this ->anonymous();
        $this ->jssdk();
        $id = input('get.id');
        if (empty($id)){
            $this ->error('参数错误!');
        }
        $this->assign('detail',$this->content(5,$id));
        return $this->fetch();
    }
    /**
     * 会议 党课 详情
     */
    public function meetingdetail(){
        $this->checkAnonymous();
        //游客模式
        $this ->anonymous();
        $this ->jssdk();
        $id = input('get.id');
        if (empty($id)){
            $this ->error('参数错误!');
        }
        $this->assign('detail',$this->content(5,$id));
        return $this->fetch();
    }
}