<?php
/**
 * Created by PhpStorm.
 * User: laowang <958364865@qq.com>
 * Date: 2017/8/11
 * Time: 9:30
 */
namespace app\home\controller;
use app\home\model\WechatDepartment;
use app\home\model\Branch as  BranchModel;
use app\home\model\Comment;
use app\home\model\BranchVote;
use app\home\model\Picture;
use app\user\model\WechatUser;
/**
 * Class Branch
 * @package 支部建设
 */
class Branch extends Base
{
    /**
     * 频道  主页
     */
    public function index(){
        // 去掉 协会
        $depart = WechatDepartment::where(['status' =>1 ,'id' => ['neq',1]])->select();
        $this->assign('depart',$depart);
        return $this->fetch();
    }
    /**
     * 数据 主页面
     */
    public function branch(){
        $this->checkAnonymous();
        $dep = input('dep');
        $userId = session('userId');
        $Branch = new BranchModel();
        $list = $Branch->where(['department' => $dep ,'type' => 1,'status' => 0])->order('id desc')->limit(7)->select();  // 活动列表
        $lists = $Branch->where(['department' => $dep ,'type' => 2 ,'status' => 0])->order('id desc')->limit(5)->select(); // 投票
        $report = $Branch->where(['department' => $dep ,'type' => 3 ,'status' => ['egt',0]])->order('id desc')->limit(7)->select(); // 投票
        foreach($lists as $value){
            $User = WechatUser::where('userid',$value['create_user'])->field('department,headimgurl')->find();
            $Depart = WechatDepartment::where('id',$User['department'])->field('name')->find();
            $value['head'] = $User['headimgurl'];
            $value['department'] = $Depart['name'];
            //  获取  图片
            $value['images'] = json_decode($value['images']);
            // 获取  赞成 或者  反对
            $likeModel = new BranchVote();
            $like = $likeModel->getLike($value['id'],$userId);
            $value['is_like'] = $like;  //  0 未投票    1  赞成  2 反对
            // 获取评论
            $commentModel = new Comment();
            $comment = $commentModel->getComment(6,$value['id'],$userId);
            $value['comment'] = $comment;
        }
        $this->assign('list',$list);
        $this->assign('lists',$lists);
        $this->assign('report',$report);
        $this->assign('depart',$dep);
        return $this ->fetch();
    }
    /*
     * 活动  列表 更多
     */
    public function morelist(){
        $this->checkAnonymous();
        $userId = session('userId');
        $Wish = new BranchModel();
        $type = input('post.type');  // 0 活动列表 1 活动报道  2 投票
        $len = input('post.length');
        $dep = input('post.dep');
        if ($type == 0 || $type == 1){
            if ($type == 0){
                $con = 1;
            }else{
                $con = 3;
            }
            // 活动  列表
            $list = $Wish->where(['department' => $dep,'type' => $con,'status' => ['egt',0]])->order('id desc')->limit($len,5)->select();  // 活动列表
            foreach($list as $value){
                $Pic = Picture::where('id',$value['front_cover'])->find();
                $value['front_cover'] = $Pic['path'];
                $value['time'] = date('Y-m-d',$value['create_time']);
            }
        }else{
            // 投票
            $list = $Wish->where(['department' => $dep,'type' => 2,'status' => 0])->order('id desc')->limit($len,5)->select();  // 投票
            foreach($list as $value){
                $User = WechatUser::where('userid',$value['create_user'])->field('department,headimgurl')->find();
                $Depart = WechatDepartment::where('id',$User['department'])->field('name')->find();
                $value['head'] = $User['headimgurl'];
                $value['department'] = $Depart['name'];
                //  获取  图片
                $value['images'] = json_decode($value['images']);
                if (!empty($value['images'])){
                    $image =array();
                    foreach ($value['images'] as $k=>$val){
                        $img = Picture::get($val);
                        $image[$k] = $img['path'];
                    }
                    $value['images'] = $image;
                }
                $value['create_time'] = date('Y-m-d',$value['create_time']);
                // 获取  赞成 或者  反对
                $likeModel = new BranchVote();
                $like = $likeModel->getLike($value['id'],$userId);
                $value['is_like'] = $like;  //  0 未投票    1  赞成  2 反对
                // 获取评论
                $commentModel = new Comment();
                $comment = $commentModel->getComment(6,$value['id'],$userId);
                $value['comment'] = $comment;
            }
        }
        if ($list){
            return $this->success('加载成功','',$list);
        }else{
            $this->error('加载失败');
        }
    }
    // 活动报道  详情
    public function detail(){
        //判断是否是游客
        $this ->anonymous();
        $this->checkAnonymous();
        //获取jssdk
        $this ->jssdk();
        $id = input('id');
        $this->assign('new',$this->content(2,$id));
        return $this->fetch();
    }
    /* 活动发起   详情 */
    public function activitydetails(){
        $userId = session('userId');
        $this->checkAnonymous();
        $id = input('get.id/d');
        $list = BranchModel::where(['id' => $id,'status' => 0])->find();
        if (empty($list)){
            $this->error('系统错误,数据不存在');
        }
        // 已认领名单
        $Receive = db('branch_receive')->where(['rid' => $id,'status' => 0])->select();
        foreach($Receive as $key => $value){
            $User = WechatUser::where('userid',$value['userid'])->field('name,department,headimgurl')->find();
            $Receive[$key]['name'] = $User['name'];
            $Receive[$key]['head'] = $User['headimgurl'];
            $Receive[$key]['department'] = WechatDepartment::where('id',$User['department'])->value('name');
        }
        $list['receive'] = $Receive;
        // 认领权限  本支部党员
        $User = WechatUser::where(['userid' => $userId , 'department' => $list['department'] ,'party' => 1])->find();
        $list['review'] = 1;
        if (empty($User)){
            // 没有认领权限
            $list['review'] = 0;
        }
        // 有认领权限  再判断是否已经认领
        $infoes = db('branch_receive')->where(['rid' => $id,'userid' => $userId,'status' => 0])->find();
        if ($infoes){
            // 自己已经认领
            $list['is_receive'] = 1;
        }else{
            $list['is_receive'] = 0;  // 未认领
        }
        //活动基本信息
        $list['user'] = $userId;
        //分享图片及链接及描述
        $list['share_image'] = "http://".$_SERVER['SERVER_NAME'].'/home/images/feedback/feedback.jpg';
        $list['link'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'];
        $list['desc'] = $list['description'];

        $this->assign('info',$list);
        return $this ->fetch();
    }
    /*
     * 活动 认领
     */
    public function enroll(){
        $this->checkAnonymous();
        $id = input('post.id/d');
        $list = BranchModel::where(['id' => $id,'status' => 0])->find();
        if (empty($list)){
            $this->error('系统错误,数据不存在');
        }
        $userId = session('userId');
        $department = WechatUser::where('userid',$userId)->value('department');
        $res = db('branch_receive')->insert(['rid' => $id,'userid' => $userId,'department' => $department,'create_time' => time(),'status' => 0]);
        if ($res){
            // 返回 用户数据
            $User = WechatUser::where('userid',$userId)->field('name,department,headimgurl')->find();
            $User['department'] = WechatDepartment::where('id',$User['department'])->value('name');
            $User['time'] = date('Y-m-d',db('branch_receive')->where(['rid' => $id,'userid' => $userId])->value('create_time'));
            return $this->success('认领成功','',$User);
        }else{
            $this->error('认领失败');
        }
    }
    /*
     * 投票
     */
    public function vote(){
        $this->checkAnonymous();
        $userId = session('userId');
        $id = input('post.id');
        $status = input('post.status');
        $Vote = new  BranchVote();
        $res = $Vote->save(['userid' => $userId,'vote_id' => $id,'status' => $status]);
        if ($res){
            if ($status == 2){
                // 反对
                BranchModel::where(['id' => $id,'status' => 0])->setInc('likes_no');
            }else{
                // 赞成
                BranchModel::where(['id' => $id,'status' => 0])->setInc('likes_yes');
            }
            return $this->success('成功');
        }else{
            $this->error('失败');
        }
    }
    /*
     * 投票  发布
     */
    public function publish(){
        $this->checkAnonymous();
        $data = input('post.');
        $dep = input('get.dep');
        $uid = session('userId');
        if(empty($data))
        {
            $this->assign('dep',$dep);
            return $this ->fetch();
        }else{
            $wishModel = new BranchModel();
            $data['type'] = 2;
            $data['images'] = json_encode($data['images']);
            $data['publisher'] = get_name($uid);
            $data['create_time'] = time();
            $data['create_user'] = $uid;
            $data['status'] = 0;
            $wishModel ->data($data) ->save();
            if($wishModel ->id)
            {
                return $this ->success('发布成功!');
            }else{
                $this ->error('发布失败!');
            }
        }
    }
}