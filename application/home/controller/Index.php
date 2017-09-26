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
use app\home\model\Party;

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
        $len = array('news' => 0,'learn' => 0,'notice' => 0,'party' => 0);
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
        $count1 = $len['news'];   // 第一聚焦
        $count2 = $len['learn'];  // 两学一做
        $count3 = $len['notice'];  // 组织活动
        $count4 = $len['party']; // 三会一课
        $news = new News();
        $learn = new Learn();
        $notice = new Notice();
        $party = new Party();
        $news_check = false; //新闻数据状态 true为取空
        $learn_check = false;
        $notice_check = false;
        $party_check = false;
        $all_list = array();
        //获取数据  取满8条 或者取不出数据退出循环
        while(true)
        {
            // 第一聚焦
            if (!$news_check && count($all_list) < 8){
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
                count($all_list) < 8)
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
            // 组织活动
            if(!$notice_check &&
                count($all_list) < 8)
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
            //   三会一课
            if (!$party_check && count($all_list) < 8){
                $res4 = $party->getDataList($count4);
                if (empty($res4)){
                    $party_check = true;
                }else{
                    $count4 ++;
                    $all_list = $this->changeTpye($all_list,$res4,4);
                }
            }
            if(count($all_list) >= 8 || ($news_check && $notice_check && $learn_check && $party_check))
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
     * @param $type 1第一聚焦  2两学一做 3 组织活动 4 三会一课
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