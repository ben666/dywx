<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/6/17
 * Time: 17:40
 */
namespace app\home\controller;
use app\home\model\WechatUser;
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
        $userid = session('userId');
        $departmentid = WechatUser::where('userid',$userid)->value('department');  // 本部门
        // 发布权限
        $review = WechatUser::where('userid',$userid)->value('review');
        $Notice = new Notice();
        $map = array(
            'type' => 2,
            'status' => ['egt',0],
            'department' => ['in',[0,$departmentid]]
        );
        $list = $Notice->get_list($map);  // 活动通知
        $maps = array(
            'type' => 1,
            'status' => ['egt',0],
            'department' => ['in',[0,$departmentid]]
        );
        $lists = $Notice->get_list($maps); // 活动展示
        $this->assign('list',$list);
        $this->assign('lists',$lists);
        $this->assign('review',$review);
        return $this ->fetch();
    }
    /*
     * 活动  列表 更多
     */
    public function morelist(){
        $this->checkAnonymous();
        $userid = session('userId');
        $departmentid = WechatUser::where('userid',$userid)->value('department');  // 本部门
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
            'status' => ['egt',0],
            'department' => ['in',[0,$departmentid]]
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
    /**
     * 手机端 发布
     */
    public function publish(){
        if (IS_POST){
            $data = input('post.');
            $userid = session('userId');
            $data['department'] = WechatUser::where('userid',$userid)->value('department');  // 本部门
            $data['publisher'] = WechatUser::where('userid',$userid)->value('name');
            $data['front_cover'] = $this->default_pic();
            $data['create_time'] = time();
            if ($data['start_time']){
                $data['start_time'] = strtotime($data['start_time']);
            }
            if ($data['end_time']){
                $data['end_time'] = strtotime($data['end_time']);
            }
            if ($data['images']){
                $data['images'] = json_encode($data['images']);
            }
            $res = Notice::create($data);
            if ($res){
                return $this->success('添加成功');
            }else{
                return $this->error('添加失败');
            }
        }else{
            return $this->fetch();
        }
    }
}