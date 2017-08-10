<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/6/1
 * Time: 16:20
 */
namespace app\home\controller;
use app\home\model\News as NewsModel;
use app\home\model\Picture;

class News extends Base {
    //党建之家首页
    public function index()
    {
        $News = new NewsModel();
        //轮播推荐
        $map = array(
            'type' => 1,  // 新闻发布
            'recommend' => 1,
            'status' => ['egt',0]
        );
        $map1 = array(
            'type' => 1,  // 新闻发布
            'status' => ['egt',0]
        );
        $maps = array(
            'type' => 2,  // 活动情况
            'status' => ['egt',0]
        );
        //数据列表
        $this ->assign('list1',$News->get_list($map)); // 新闻发布 轮播
        $this ->assign('list',$News->get_list($map1)); //  新闻发布  列表
        $this ->assign('lists',$News->get_list($maps));
        return $this ->fetch();
    }
    /**
     * 加载更多
     * @return array|void
     */
    public function listmore()
    {
        $len = input("length");
        $news = new NewsModel();
        $map = array('status' => ['egt',0]);
        $order = 'create_time desc';
        $list = $news ->where($map) ->order($order) ->limit($len,5) ->select();
        //图片跟时间戳转化
        foreach($list as $value){
            //手机端上传的给默认图
            if(!empty($value['front_cover']))
            {
                $img = Picture::get($value['front_cover']);
            }else{
                $img['path'] = '';
            }
            $value['path'] = $img['path'];
            $value['time'] = date("Y-m-d",$value['create_time']);
        }
        if(!empty($list))
        {
            return $this->success("加载成功",Null,$list);
        }else {
            return $this->error("加载失败");
        }
    }
    /**
     * 新闻详情页
     * @return mixed
     */
    public function detail(){
        //判断是否是游客
        $this ->anonymous();
        //获取jssdk
        $this ->jssdk();
        $userId = session('userId');
        $id = input('id');
        $newsModel = new News();
        //浏览加一
        $info['views'] = array('exp','`views`+1');
        $newsModel::where('id',$id)->update($info);
        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = array(
                'user_id' => $userId,
                'news_id' => $id,
            );
            $history = Browse::get($con);
            if(!$history && $id != 0){
                $s['score'] = array('exp','`score`+1');
                if ($this->score_up()){
                    // 未满 15 分
                    Browse::create($con);
                    WechatUser::where('userid',$userId)->update($s);
                }
            }
        }

        //新闻基本信息
        $list = $newsModel::get($id);
        //党员发布的图片转化
        $list['images'] = json_decode($list['images']);
        $list['user'] = session('userId');
        //分享图片及链接及描述
        $image = Picture::where('id',$list['front_cover'])->find();
        $list['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
        $list['link'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'];
        $list['desc'] = str_replace('&nbsp;','',strip_tags($list['content']));

        //获取 文章点赞
        $likeModel = new Like;
        $like = $likeModel->getLike(1,$id,$userId);
        $list['is_like'] = $like;
        $this->assign('new',$list);

        //获取 评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(1,$id,$userId);
        $this->assign('comment',$comment);
        return $this->fetch();
    }

}