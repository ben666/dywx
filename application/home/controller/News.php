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

/**
 * Class News
 * @package 红色足迹
 */
class News extends Base {
    //首页
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
        if (empty($News->get_list($map))){
            $list1 = $News->get_list($map1);
        }else{
            $list1 = $News->get_list($map);
        }
        //数据列表
        $this ->assign('list1',$list1); // 新闻发布 轮播
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
        $type = input('type');
        $news = new NewsModel();
        $map = array(
            'type' => $type+1,
            'status' => ['egt',0]
        );
        $list = $news->get_list($map,$len);
        if($list)
        {
            return $this->success("加载成功",Null,$list);
        }else {
            $this->error("加载失败");
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
        $id = input('id');
        $this->assign('new',$this->content(1,$id));
        return $this->fetch();
    }
    public function history(){
        return $this->fetch();
    }
}