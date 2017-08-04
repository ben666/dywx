<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/10/8
 * Time: 10:29
 */

namespace app\admin\controller;
use app\admin\model\Notice as NoticeModel;
use app\admin\model\Picture;
use think\Url;

/**
 * Class Notice
 * @package 支部活动
 */
class Notice extends Admin {
    /**
     * 相关通知
     */
    public function index(){
        $map = array(
            'type' => 2,
            'status' => array('egt',0),
        );
        $search = input('search');
        if ($search != '') {
            $map['title'] = ['like','%'.$search.'%'];
        }
        $list = $this->lists('Notice',$map);
        int_to_string($list,array(
            'recommend' => array(0=>"否",1=>"是"),
            'push' => array(0=>"否",1=>"是")
        ));
        $this->assign('list',$list);
        return $this->fetch();
    }
    /**
     * 学习资料
     */
    public function learn(){
        $map = array(
            'type' => 1,
            'status' => array('egt',0),
        );
        $search = input('search');
        if ($search != '') {
            $map['title'] = ['like','%'.$search.'%'];
        }
        $list = $this->lists('Notice',$map);
        int_to_string($list,array(
            'recommend' => array(0=>"否",1=>"是"),
            'push' => array(0=>"否",1=>"是")
        ));
        $this->assign('list',$list);
        return $this->fetch();
    }
    /**
     * 学习资料 添加修改
     */
    public function add(){
        $id = input('id/d');
        if ($id){
            // 修改
            if (IS_POST){
                $data = input('post.');
                $noticeModel = new NoticeModel();
                $result = $this->validate($data,'Notice');  // 验证  数据
                if (true !== $result) {
                    $this->error($result);
                }else{
                    $res = $noticeModel->save($data,['id' => $data['id']]);
                    if ($res){
                        return $this->success('修改成功',Url('learn'));
                    }else{
                        $this->error('修改失败');
                    }
                }
            }else{
                $id = input('id');
                $msg = NoticeModel::get($id);
                $this->assign('msg',$msg);
                return $this->fetch();
            }
        }else{
            //添加
            if (IS_POST){
                $data = input('post.');
                if (empty($data['id'])){
                    unset($data['id']);
                }
                $noticeModel = new NoticeModel();
                $result = $this->validate($data,'Notice');  // 验证  数据
                if (true !== $result) {
                    $this->error($result);
                }else{
                    $res = $noticeModel->save($data);
                    if ($res){
                        return $this->success('添加成功',Url('learn'));
                    }else{
                        $this->error('添加失败');
                    }
                }
            }else{
                $this->assign('msg','');
                return $this->fetch();
            }
        }
    }
    /**
     * 相关通知 添加
     */
    public function indexadd(){
        if(IS_POST) {
            $data = input('post.');
            $result = $this->validate($data,'Notice');  // 验证  数据
            if (true !== $result) {
                $this->error($result);
            }else{
                $noticeModel = new NoticeModel();
                if (!empty($data['start_time']) && empty($data['end_time'])){
                    $this->error('请添加结束时间');
                }
                if (empty($data['start_time']) && !empty($data['end_time'])){
                    $this->error('请添加开始时间');
                }
                if (!empty($data['start_time']) && !empty($data['end_time']) && $data['end_time'] <= $data['start_time']){
                    $this->error('结束时间有错误');
                }
                $data['start_time'] = strtotime($data['start_time']);
                $data['end_time'] = strtotime($data['end_time']);
                $res = $noticeModel->save($data);
                if ($res){
                    return $this->success("新增通知成功",Url('Notice/index'));
                }else{
                    $this->error($noticeModel->getError());
                }
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
            $result = $this->validate($data,'Notice');  // 验证  数据
            if (true !== $result) {
                $this->error($result);
            }else{
                $noticeModel = new NoticeModel();
                if (!empty($data['start_time']) && empty($data['end_time'])){
                    $this->error('请添加结束时间');
                }
                if (empty($data['start_time']) && !empty($data['end_time'])){
                    $this->error('请添加开始时间');
                }
                if (!empty($data['start_time']) && !empty($data['end_time']) && $data['end_time'] <= $data['start_time']){
                    $this->error('结束时间有错误');
                }
                $data['start_time'] = strtotime($data['start_time']);
                $data['end_time'] = strtotime($data['end_time']);
                $res = $noticeModel->save($data,['id'=>$data['id']]);
                if ($res){
                    return $this->success("修改通知成功",Url('Notice/index'));
                }else{
                    return $this->get_update_error_msg($noticeModel->getError());
                }
            }
        }else{
            $id = input('id');
            $msg = NoticeModel::get($id);
            $this->assign('msg',$msg);
            return $this->fetch();
        }
    }
    /**
     * 删除
     */
    public function del(){
        $id = input('id');
        $map['status'] = "-1";
        $info = NoticeModel::where('id',$id)->update($map);
        if($info) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }
}