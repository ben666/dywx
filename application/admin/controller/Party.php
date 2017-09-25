<?php
/**
 * Created by PhpStorm.
 * User: laowang <958364865@qq.com>
 * Date: 2017/9/22
 * Time: 13:58
 */

namespace app\admin\controller;
use app\admin\model\Party as PartyModel;
/**
 * Class Party
 * @package app\admin\controller  三会一课
 */
class Party extends Admin
{
    /**
     * 相关通知 首页
     */
    public function index(){
        $map = array(
            'type' => 1,
            'status' => array('egt',0),
        );
        $search = input('search');
        if ($search != '') {
            $map['title'] = ['like','%'.$search.'%'];
        }
        $list = $this->lists('Party',$map);
        int_to_string($list,array(
            'status' => array(0=>"已发布",1=>"已发布"),
            "recommend" => [0 => "否" , 1 =>"是"],
            "push" => [0 => "否" , 1 =>"是"]
        ));
        $this->assign('list',$list);
        return $this->fetch();
    }
    /**
     * 会议情况
     * type: 2
     */
    public function meet(){
        $map = array(
            'type' => 2,
            'status' => array('egt',0),
        );
        $search = input('search');
        if ($search != '') {
            $map['title'] = ['like','%'.$search.'%'];
        }
        $list = $this->lists('Party',$map);
        int_to_string($list,array(
            'status' => array(0=>"已发布",1=>"已发布"),
            "recommend" => [0 => "否" , 1 =>"是"],
            "push" => [0 => "否" , 1 =>"是"]
        ));

        $this->assign('list',$list);

        return $this->fetch();
    }
    /**
     * 党课情况
     * type: 3
     */
    public function lecture(){
        $map = array(
            'type' => 3,
            'status' => array('egt',0),
        );
        $search = input('search');
        if ($search != '') {
            $map['title'] = ['like','%'.$search.'%'];
        }
        $list = $this->lists('Party',$map);
        int_to_string($list,array(
            'status' => array(0=>"已发布",1=>"已发布"),
            "recommend" => [0 => "否" , 1 =>"是"],
            "push" => [0 => "否" , 1 =>"是"]
        ));

        $this->assign('list',$list);

        return $this->fetch();
    }
    /**
     * 相关通知 添加
     */
    public function indexadd(){
        if(IS_POST) {
            $data = input('post.');
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $partyModel = new PartyModel();
            if (!empty($data['start_time'])){
                $data['start_time'] = strtotime($data['start_time']);
            }
            if (!empty($data['end_time'])){
                $data['end_time'] = strtotime($data['end_time']);
            }
            if (!empty($data['start_time']) && !empty($data['end_time']) && $data['end_time'] <= $data['start_time']){
                return $this->error('截止时间错误');
            }
            $id = $partyModel->validate('Party.other')->save($data);
            if($id){
                return $this->success("新增相关通知成功",Url('Party/index'));
            }else{
                return $this->error($partyModel->getError());
            }
        }else {
            return $this->fetch();
        }
    }
    /**
     * 相关通知 修改
     */
    public function indexedit(){
        if(IS_POST) {
            $data = input('post.');
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $partyModel = new PartyModel();
           if (!empty($data['start_time'])){
                $data['start_time'] = strtotime($data['start_time']);
            }
            if (!empty($data['end_time'])){
                $data['end_time'] = strtotime($data['end_time']);
            }
            if (!empty($data['start_time']) && !empty($data['end_time']) && $data['end_time'] <= $data['start_time']){
                return $this->error('截止时间错误');
            }
            $id = $partyModel->validate('Party.other')->save($data,['id'=>$data['id']]);
            if($id){
                return $this->success("修改相关通知成功",Url('Party/index'));
            }else{
                return $this->error($partyModel->getError());
            }
        }else{
            $id = input('id');
            $msg = PartyModel::get($id);
            $this->assign('msg',$msg);
            return $this->fetch();
        }
    }
    /**
     * 会议情况 党课情况 添加
     */
    public function add(){
        if(IS_POST) {
            $data = input('post.');
            $partyModel = new PartyModel();
            if(empty($data['id'])) {
                unset($data['id']);
            }
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $model = $partyModel->validate('Party.another')->save($data);
            if($model){
                if ($data['type'] == 3){
                    return $this->success('新增党课情况成功',Url('Party/lecture'));
                }else{
                    return $this->success('新增会议情况成功',Url('Party/meet'));
                }
            }else{
                return $this->error($partyModel->getError());
            }
        }else{
            $msg = array();
            $msg['type'] = input('type');
            $msg['class'] = 1; // 1为添加 ，2为修改
            $this->assign('msg',$msg);
            return $this->fetch('edit');
        }
    }
    /**
     * 会议情况 党课情况 修改
     */
    public function edit(){
        if(IS_POST) {
            $partyModel = new PartyModel();
            $data = input('post.');
            if(empty($data['id'])) {
                unset($data['id']);
            }
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $model = $partyModel->validate('Notice.other')->save($data,['id'=> $data['id']]);
            if($model){
                if ($data['type'] == 3){
                    return $this->success('修改党课情况成功',Url('Party/lecture'));
                }else{
                    return $this->success('修改会议情况成功',Url('Party/meet'));
                }
            }else{
                return $this->get_update_error_msg($partyModel->getError());
            }
        }else{
            $id = input('id');
            $msg = PartyModel::get($id);
            $msg['class'] = 2;
            $this->assign('msg',$msg);

            return $this->fetch();
        }
    }

    /**
     * 删除
     */
    public function del(){
        $id = input('id');
        if (empty($id)){
            return $this->error('系统参数错误');
        }
        $map['status'] = "-1";
        $info = PartyModel::where('id',$id)->update($map);
        if($info) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }
}