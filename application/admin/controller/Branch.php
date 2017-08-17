<?php
/**
 * Created by PhpStorm.
 * User: laowang <958364865@qq.com>
 * Date: 2017/8/8
 * Time: 17:34
 */

namespace app\admin\controller;
use app\admin\model\Branch as BranchModel;
use app\admin\model\WechatDepartment;
use app\admin\model\WechatUser;
use app\admin\model\BranchReceive;
/**
 * 支部建设 控制器
 * @package app\admin\controller
 */
class Branch extends  Admin
{
    /*
      * 活动 列表 主页
      */
    public function index(){
        $map = array(
            'type' => 1, // 活动
            'status' => array('egt',0),
        );
        $search = input('search');
        if ($search != '') {
            $map['title'] = ['like','%'.$search.'%'];
        }
        $list = $this->lists('Branch',$map);
        int_to_string($list,array(
            'status' => array(0=>"已发布",1=>"已发布"),
        ));
        foreach ($list as $key => $value) {
            $msg = array(
                'rid' => $value['id'],
                'status' => 0,
            );
            $info = BranchReceive::where($msg)->select();
            if($info) {
                $value['is_enroll'] = 1;
            }else{
                $value['is_enroll'] = 0;
            }
        }
        $this->assign('list',$list);
        return $this->fetch();
    }
    /**
     * 活动报道  主页
     */
    public function report(){
        $map = array(
            'type' => 3, // 报道
            'status' => array('egt',0),
        );
        $search = input('search');
        if ($search != '') {
            $map['title'] = ['like','%'.$search.'%'];
        }
        $list = $this->lists('Branch',$map);
        int_to_string($list,array(
            'status' => array(0=>"已发布",1=>"已发布"),
//            'recommend' => [0 => "否" , 1 => "是"],
            'push' => [0 => '否' , 1 => '是']
        ));
        $this->assign('list',$list);
        return $this->fetch();
    }
    /**
     * 活动报道  添加  修改
     */
    public function add(){
        $wish = new BranchModel();
        if (IS_POST){
            $data = input('post.');
            if ($data['department'] == -1){
                $this->error('请选择所属支部');
            }
            $result = $wish->get_save($data);
            if($result) {
                return $this->success('操作成功', Url('Branch/report'));
            }else{
                $this->error($wish->getError());
            }
        }else{
            // 添加页面
            $this->assign('msg', $wish->get_content(input('get.id')));
            $Department = WechatDepartment::where(['status' => 1 ,'id' => ['neq',1]])->field('id,name')->select();
            $this->assign('info',$Department);
            return $this->fetch();
        }
    }
    /**
     * 活动列表 添加  修改
     */
    public function edit() {
        $id = input('id/d');
        if ($id){
            // 修改
            if(IS_POST) {
                $data = input('post.');
                $result = $this->validate($data,'Branch.other');
                if(true !== $result){
                    // 验证失败 输出错误信息
                    $this->error($result);
                }else{
                    if ($data['department'] == -1){
                        $this->error('请选择所属支部');
                    }
                    $wishModel = new BranchModel();
                    $model = $wishModel->save($data,['id'=>$data['id']]);
                    if($model) {
                        return $this->success("修改成功",Url('Branch/index'));
                    }else{
                        $this->error($wishModel->getError());
                    }
                }
            }else {
                $Department = WechatDepartment::where(['status' => 1 ,'id' => ['neq',1]])->field('id,name')->select();
                $this->assign('info',$Department);
                $id = input('id');
                $msg = BranchModel::get($id);
                $this->assign('msg',$msg);
                return $this->fetch();
            }
        }else{
            // 添加
            if(IS_POST) {
                $data = input('post.');
                $result = $this->validate($data,'Branch.other');
                if(true !== $result){
                    // 验证失败 输出错误信息
                    $this->error($result);
                }else{
                    unset($data['id']);
                    if ($data['department'] == -1){
                        $this->error('请选择所属支部');
                    }
                    $wishModel = new BranchModel();
                    $model = $wishModel->save($data);
                    if($model) {
                        return $this->success("新增成功",Url('Branch/index'));
                    }else{
                        $this->error($wishModel->getError());
                    }
                }
            }else {
                $Department = WechatDepartment::where(['status' => 1 ,'id' => ['neq',1]])->field('id,name')->select();
                $this->assign('info',$Department);
                $this->assign('msg',null);
                return $this->fetch('edit');
            }
        }
    }
    /**
     * 领取列表
     */
    public function receive() {
        $id = input('id');
        $map = array(
            'rid' => $id,
            'status' => array('egt',0),
        );
        $list = $this->lists('BranchReceive',$map);
        $this->assign('list',$list);
        return $this->fetch();
    }
    /**
     * 活动  删除
     */
    public function del() {
        $id = input('id');
        $map = array(
            'status' => -1,
        );
        $wishModel = new BranchModel();
        $model = $wishModel->where(['id' => $id])->update($map);
        if($model) {
            $result = BranchReceive::where('rid',$id)->count();
            if ($result != 0){
                $res = BranchReceive::where('rid',$id)->update($map);
                if ($res){
                    return $this->success("删除成功");
                }else{
                    $this->error("删除失败");
                }
            }else{
                return $this->success("删除成功");
            }
        }else{
            $this->error("删除失败");
        }
    }
    /*
      * 投票  主页
      */
    public function vote(){
        $map = array(
            'type' => 2 ,  // 投票
            'status' => array('egt',0),
        );
        $search = input('search');
        if ($search != '') {
            $map['content'] = ['like','%'.$search.'%'];
        }
        $list = $this->lists('Branch',$map);
        foreach($list as $value){
            $User = WechatUser::where('userid',$value['create_user'])->field('name,department')->find();
            if (empty($value['publisher'])){
                $value['name'] = $User['name'];
            }else{
                $value['name'] = $value['publisher'];  // 获取发布人 姓名
            }
            $Department = WechatDepartment::where('id',$User['department'])->field('name')->find();
            $value['dep'] = $Department['name'];  // 获取发布人 组别
            $value['images'] = json_decode($value['images']);
        }
        $this->assign('list',$list);
        return $this->fetch();
    }
    /*
      * 投票 删除
      */
    public function votedel(){
        $id = input('id/d');
        $opinion = new Wish();
        $res = $opinion->where(['id' => $id,'type' => 2])->update(['status' => '-1']);
        if ($res){
            return $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
}