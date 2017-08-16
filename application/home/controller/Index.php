<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/4/20
 * Time: 13:47
 */

namespace app\home\controller;
use app\admin\model\Picture;
use think\Controller;
use app\home\model\News;
use app\home\model\Learn;
use app\home\model\Notice;
use app\home\model\PioneerStory;
use app\home\model\Wish;
use app\home\model\Branch;

/**
 * 党建主页
 */
class Index extends Base {
    /**
     * 首页
     * @return mixed
     */
    public function index(){
        $this ->anonymous();
        $uid = session('userId');
        $len = array('news' => 0,'learn' => 0,'notice' => 0,'pioneer_story' => 0,'wish' => 0,'branch' => 0);
        $list2 = $this ->getDataList($len);
        $this ->assign('user',$uid);
        $this ->assign('list2',$list2['data']);
        return $this->fetch();
    }

    /**
     * 获取数据列表 信息驿站 notice  两学一做 learn 先锋引领 pioneer 红色足迹 news 活动发起 wish 支部建设 branch
     * @param $len
     */
    public function getDataList($len)
    {
        //从第几条开始取数据
        $count1 = $len['news'];   // 红色足迹
        $count2 = $len['learn'];  // 两学一做
        $count3 = $len['notice'];  // 信息驿站
//        $count4 = $len['pioneer_story']; // 先锋引领   事迹
//        $count5 = $len['wish'];  // 活动发起
//        $count6 = $len['branch'];  // 支部建设
        $news = new News();
        $learn = new Learn();
        $notice = new Notice();
//        $pioneer = new PioneerStory();
//        $wish = new Wish();
//        $branch = new Branch();
        $news_check = false; //新闻数据状态 true为取空
        $learn_check = false;
        $notice_check = false;
//        $pioneer_check = false;
//        $wish_check = false;
//        $branch_check = false;
        $all_list = array();
        //获取数据  取满6条 或者取不出数据退出循环
        while(true)
        {
            // 红色足迹
            if (!$news_check && count($all_list) < 6){
                $res1 = $news->getDataList($count1);
                if (empty($res1)){
                    $news_check = true;
                }else{
                    $count1 ++ ;
                    $all_list = $this->changeTpye($all_list,$res1,1);
                }
            }
            // 两学一做
            if(!$learn_check &&
                count($all_list) < 6)
            {
                $res2 = $learn ->getDataList($count2);
                if(empty($res2))
                {
                    $learn_check = true;
                }else {
                    $count2 ++;
                    $all_list = $this ->changeTpye($all_list,$res2,2);
                }
            }
            // 心息驿站
            if(!$notice_check &&
                count($all_list) < 6)
            {
                $res3 = $notice ->getDataList($count3);
                if(empty($res3))
                {
                    $notice_check = true;
                }else {
                    $count3 ++;
                    $all_list = $this ->changeTpye($all_list,$res3,3);
                }
            }
            // 先锋引领
//            if (!$pioneer_check && count($all_list) < 6){
//                $res4 = $pioneer->getDataList($count4);
//                if (empty($res4)){
//                    $pioneer_check = true;
//                }else{
//                    $count4 ++;
//                    $all_list = $this->changeTpye($all_list,$res4,4);
//                }
//            }
            // 活动发起
//            if (!$wish_check && count($all_list) < 6){
//                $res5 = $wish->getDataList($count5);
//                if (empty($res5)){
//                    $wish_check = true;
//                }else{
//                    $count5++;
//                    $all_list = $this->changeTpye($all_list,$res5,5);
//                }
//            }
            // 支部建设
//            if (!$branch_check && count($all_list) < 6){
//                $res6 = $branch->getDataList($count6);
//                if (empty($res6)){
//                    $branch_check = true;
//                }else{
//                    $count6 ++;
//                    $all_list = $this->changeTpye($all_list,$res6,6);
//                }
//            }
            if(count($all_list) >= 6 || ($news_check && $notice_check && $learn_check))
            {
                break;
            }
        }
        if (count($all_list) != 0)
        {
            return ['code' => 1,'msg' => '获取成功','data' => $all_list];
        }else{
            return ['code' => 0,'msg' => '获取失败','data' => $all_list];
        }
    }

    /**
     * 进行数据区分
     * @param $list
     * @param $type 1红色足迹  2两学一做 3 信息驿站 4 先锋引领 事迹 5 活动发起 6 支部建设
     */
    private function changeTpye($all,$list,$type){
        $list['class'] = $type;
        array_push($all,$list);
        return $all;
    }
    /**
     * 首页加载更多新闻列表
     * @return array
     */
    public function moreDataList(){
        $len = input('get.');
        $list = $this ->getDataList($len);
        //转化图片路径 时间戳
        foreach ($list['data'] as $k => $v)
        {
            $img_path = Picture::get($list['data'][$k]['front_cover']);
            $list['data'][$k]['time'] = date('Y-m-d',$v['create_time']);
            $list['data'][$k]['path'] = $img_path['path'];
        }
        return $list;
    }
}