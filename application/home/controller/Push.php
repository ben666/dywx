<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/6/8
 * Time: 15:28
 */
namespace app\home\controller;
use think\Controller;
use com\wechat\TPWechat;
use think\Config;
use app\home\model\News as NewsModel;
use app\home\model\Learn as LearnModel;
use app\home\model\PioneerStory as PioneerModel;
use app\home\model\Notice;
use app\home\model\Wish;
use app\home\model\Branch;
use app\home\model\Picture;

class Push extends Controller{
    /**
     * 订阅号每日定时推送
     */
    public function cron(){
        $Wechat = new TPWechat(Config::get('party'));
        $author = '中国遥感应用协会';//推送作者
        $request = 'http://zgyg.0571ztnet.com';//域名
        $answer = '/home/constitution/course';//每日一课路径
        //每日一课的图片ID
        $image_id = "65s5Dd8jcGNvhC9izswZtFKnYGIP_Wp9OT1-3zUmyuQ";
        $openid = 'ome1gxJkdYt9Ji1LZjvl4d2d-6Fk';//王志超
        //获取需要推送的数据
        $list = $this ->pushList();
        //没有需要推送的消息,就只推每日一课
        if(empty($list)){
//            $info['media_id'] = '65s5Dd8jcGNvhC9izswZtFEHs-SRAE-JxAqQCoP7g_g';
        }else{
//            //先上传素材 media_id
            foreach($list as $k => $v){
                //class 1红色足迹  2两学一做 3 先锋引领 事迹  4 信息驿站 5 活动发起 6 支部建设
                $class = $v['class'];
                $data = array(
                    "media" => '@.'.$v['img']
                );
                $img = $Wechat ->uploadForeverMedia($data,'thumb');
                $v['thumb_media_id'] = $img['media_id'];
                $id = $v['id'];
                if($class == 1)
                {
                    $link = 'news/detail';
                } else if($class == 2){
                    $link = 'learn/article';
                }else if($class == 3){
                    $link = 'pioneer/detail';
                }else if($class == 4){
                    $link = 'college/forumnotice';
                }else if($class == 5){
                    $link = 'activity/detail';
                }else if($class == 6){
                    $link = 'branch/detail';
                }
                $v['content_source_url'] = "$request/home/$link/id/$id";
            }
            //图文素材列表
            $article = array();
            foreach ($list as $k =>$v ){
                $article['articles'][$k] = [
                    'thumb_media_id' => $v['thumb_media_id'],
                    'author' => $v['publisher'],
                    'title' => $v['title'],
                    'content_source_url' => $v['content_source_url'],
                    "content" => $v['content'],
                    "digest" => $v['title'],
                    "show_cover_pic" => 0,
                ];
            }
            //最后一条加入每日一课
//            $article['articles'][count($article['articles'])] = [
//                    'thumb_media_id' => $image_id,
//                    'author' => $author,
//                    'title' =>'每日一课',
//                    'content_source_url' => "$request.$answer",
//                    "content" => "每日一课已经等候你多时了,点阅读全文开始答题!",
//                    "digest" => "休息一下,去答一下题吧",
//                    "show_cover_pic" => 1,
//                ];
            $lists =  $article;
            //上传多条图文素材
            $info = $Wechat ->uploadForeverArticles($lists);
            //消息群发
            $send = [
                "filter" => [
                    "is_to_all" =>true
                ],
                "mpnews" =>[
                    "media_id" => $info['media_id']
                ],
                "msgtype" => "mpnews",
                "send_ignore_reprint" => 0
            ];
            $res = $Wechat ->sendGroupMassMessage($send);
            //发送成功 修改对应数据状态
            if($res['errcode'] == 0){
                return  $this->success('推送成功');
            }
        }
        //预览图文通知
//        $notice = array(
//            "touser" => $openid,
//            "mpnews" =>[
//                "media_id" => $info['media_id']
//            ],
//            "msgtype" => "mpnews"
//        );
//        $info = $Wechat ->previewMassMessage($notice);
//        dump( $info);
//        //上传图片获得缩略图片的media_id
//        $file_path = './home/images/constitution/3.png';
//        $data = array(
//            "media" => '@'.$file_path
//        );
//        $info = $Wechat ->uploadForeverMedia($data,'thumb');
        

//        $info = $Wechat ->getUserInfo("ome1gxJkdYt9Ji1LZjvl4d2d-6Fk");
//        dump($info);
//        //上传图文消息素材
//        $article = array(
//            "articles" => [
//                [
//                    'thumb_media_id' => $image_id,
//                    'author' => $author,
//                    'title' =>'每日一课',
//                    'content_source_url' => "$request.$answer",
//                    "content" => "每日一课已经等候你多时了,点阅读全文开始答题!",
//                    "digest" => "休息一下,去答一下题吧",
//                    "show_cover_pic" => 1,
//                ]
//            ]
//        );
//        $info = $Wechat ->uploadForeverArticles($article);
//
//        $answer_id = '65s5Dd8jcGNvhC9izswZtFEHs-SRAE-JxAqQCoP7g_g';//每日一课素材id
////        //预览图文通知
//        $notice = array(
//            "touser" => $openid,
//            "mpnews" =>[
//                "media_id" => $answer_id
//            ],
//            "msgtype" => "mpnews"
//        );
//        $info = $Wechat ->previewMassMessage($notice);
//        dump($info);
    }
    /**
     * 获取待推送的8条数据
     * @return array
     */
    public function pushList(){
        $count = 8; //总数据数量
        $count1 = 0;  //从第几条开始取数据
        $count2 = 0;
        $count3 = 0;
        $count4 = 0;
        $count5 = 0;
        $count6 = 0;

        $news = new NewsModel();  // 红色足迹
        $learn = new LearnModel();  // 两学一做
        $notice = new Notice();  // 信息驿站
        $pioneer = new PioneerModel();  // 先锋引领
        $wish = new Wish();  // 活动发起
        $branch = new Branch();  // 支部建设

        $news_check = false; //新闻数据状态 true为取空
        $learn_check = false;
        $pioneer_check = false;
        $wish_check = false;
        $branch_check = false;
        $notice_check = false;

        $all_list = array();
        //获取数据  取满8条 或者取不出数据退出循环
        while(true)
        {
            // 红色足迹
            if(!$news_check &&
                count($all_list) < $count)
            {
                //获取一条数据
                $res = $news->where(['status' => ['egt',0],'push' => 1])->whereTime('create_time','d')->order('id desc')->limit($count1,1)->select();
                if(empty($res))
                {
                    $news_check = true;
                }else {
                    $count1 ++;
                    $all_list = $this ->changeTpye($all_list,$res,1); //给每条数据增加类别判断
                }
            }
            // 两学一做
            if(!$learn_check &&
                count($all_list) < $count)
            {
                $res = $learn ->where(['status' => ['egt',0],'push' => 1])->whereTime('create_time','d')->order('id desc')->limit($count2,1)->select();
                if(empty($res))
                {
                    $learn_check = true;
                }else {
                    $count2 ++;
                    $all_list = $this ->changeTpye($all_list,$res,2);
                }
            }
            // 先锋引领
            if(!$pioneer_check &&
                count($all_list) < $count)
            {
                $res = $pioneer ->where(['status' => ['egt',0],'push' => 1])->whereTime('create_time','d')->order('id desc')->limit($count3,1)->select();
                if(empty($res))
                {
                    $pioneer_check = true;
                }else {
                    $count3 ++;
                    $all_list = $this ->changeTpye($all_list,$res,3);
                }
            }
            // 信息驿站
            if(!$notice_check &&
                count($all_list) < $count)
            {
                $res = $notice ->where(['status' => ['egt',0],'push' => 1])->whereTime('create_time','d')->order('id desc')->limit($count4,1)->select();
                if(empty($res))
                {
                    $notice_check = true;
                }else {
                    $count4 ++;
                    $all_list = $this ->changeTpye($all_list,$res,4);
                }
            }
            // 活动发起
            if(!$wish_check &&
                count($all_list) < $count)
            {
                $res = $wish ->where(['status' => ['egt',0],'push' => 1])->whereTime('create_time','d')->order('id desc')->limit($count5,1)->select();
                if(empty($res))
                {
                    $wish_check = true;
                }else {
                    $count5 ++;
                    $all_list = $this ->changeTpye($all_list,$res,5);
                }
            }
            // 支部建设
            if(!$branch_check &&
                count($all_list) < $count)
            {
                $res = $branch ->where(['status' => ['egt',0],'push' => 1])->whereTime('create_time','d')->order('id desc')->limit($count6,1)->select();
                if(empty($res))
                {
                    $branch_check = true;
                }else {
                    $count6 ++;
                    $all_list = $this ->changeTpye($all_list,$res,6);
                }
            }

            if(count($all_list) >= $count ||
                ($news_check && $learn_check && $pioneer_check && $wish_check && $branch_check && $notice_check))
            {
                break;
            }
        }
        return $all_list;
    }
    /**
     * 进行数据区分
     * @param $list
     * @param $type 1红色足迹  2两学一做 3 先锋引领 事迹  4 信息驿站 5 活动发起 6 支部建设
     */
    private function changeTpye($all,$list,$type){
        //图片进行转化
        $img = Picture::get($list['front_cover']);
        $list['img'] = $img['path'];
        //增加类别
        $list['class'] = $type;
        array_push($all,$list);
        return $all;
    }
}