<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/6/20
 * Time: 15:50
 */
namespace app\home\controller;
use app\home\model\Pioneer as PioneerModel;
use app\home\model\PioneerStory;
use app\home\model\Browse;
use app\home\model\Comment;
use app\home\model\Like;
use app\home\model\Picture;
use app\home\model\WechatUser;
//先锋引领
class Pioneer extends Base {
    /**
     * 主页
     */
    public function home()
    {
        return $this ->fetch();
    }
    /**
     * 先锋引领首页
     * @return mixed
     */
    public function index(){
        $this ->anonymous();
        $userId = session('userId');
        $type = input('type');
        $pioneer = new PioneerModel();
        // 所有数据
        $list1 = $pioneer ->where(['type' => $type,'class' => 1,'status' => ['egt',0]]) ->order('id desc') ->select();  // 个人
        $list2 = $pioneer ->where(['type' => $type,'class' => 2,'status' => ['egt',0]]) ->order('id desc') ->select();  // 集体.
        $list3 = $pioneer ->where(['type' => $type,'class' => 3,'status' => ['egt',0]]) ->order('id desc') ->select();  // 单位
        //非游客判断是否点赞
        if($userId != 'visitor'){
            $list1 = $this ->checkLIke($list1);
            $list2 = $this ->checkLIke($list2);
            $list3 = $this ->checkLIke($list3);
        }
        $this ->assign('list1',$list1);
        $this ->assign('list2',$list2);
        $this ->assign('list3',$list3);
        return $this ->fetch();
    }

    /**
     * 个人 集体 单位  详情
     * @return mixed
     */
    public function deeds()
    {
        $id = input('id');
        $Pioneer = new PioneerModel();
        $Story = new PioneerStory();
        if (!$Pioneer->get_content($id)){
            $this->error('系统参数错误');
        }
        $this->assign('info',$Story->get_content($id));
        $this->assign('list',$Pioneer->get_content($id));
        return $this ->fetch();
    }
    /**
     * 先锋事迹详情页
     */
    public function detail()
    {
        //游客模式
        $this ->anonymous();
        $this ->jssdk();
        $id = input('get.id');
        if (empty($id)){
            $this ->error('参数错误!');
        }
        $this->assign('new',$this->content(5,$id));
        return $this->fetch();
    }
    /**
     * 判断今日导师点赞
     * @param $data
     * @return mixed
     */
    public function checkLIke($data){
        //获取点赞
        $userId = session('userId');
        $likeModel = new Like;
        foreach($data as $v)
        {
            $like = $likeModel ->checkLike(7,$v['id'],$userId);
            $v['is_like'] = $like;
        }
        return $data;
    }

    /**
     * 导师点赞
     * @return array|void
     */
    public function like()
    {
        $data = input('post.');
        $like = new Like();
        $uid = session('userId');
        $dateStr = date('Y-m-d', time());
        //获取当天0点的时间戳
        $timestamp0 = strtotime($dateStr);
        $map = array(
            'create_time' => ['egt', $timestamp0],
            'type' => 7,
            'aid' => $data['aid'],
            'uid' => $uid
        );
        $res = $like->where($map)->find();
        //今日已点赞
        if (!empty($res)) {
            $this->error('今日已点赞!');
        } else {
            $data['table'] = 'pioneer';
            $data['uid'] = $uid;
            $res = $like->data($data)->save();
            //点赞成功积分+1
            if ($res) {
                //判断今日积分是否超出
                $check = $this ->score_up();
                if($check){
                    WechatUser::where('userid', $uid)->setInc('score', 1);
                }
                PioneerModel::where('id', $data['aid'])->setInc('likes', 1);
                return $this->success("点赞成功");
            } else {
                $this->error("点赞失败!");
            }
        }
    }
        /**
         * 加载更多
         * @return array|void
         */
        public function moreList()
        {
            $len = input("length");
            $pid = input('pid');
            $Story = new PioneerStory();
            $list = $Story->get_content($pid,$len);
            if(!empty($list))
            {
                return $this->success("加载成功",Null,$list);
            }else {
                $this->error("加载失败");
            }
        }
}