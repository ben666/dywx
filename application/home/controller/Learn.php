<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/5/11
 * Time: 14:21
 */

namespace app\home\controller;
use app\home\model\WechatUser;
use think\Controller;
use app\admin\model\Question;
use app\home\model\Answers;
use app\home\model\Browse;
use app\home\model\Comment;
use app\admin\model\Picture;
use app\home\model\Learn as LearnModel;
use app\home\model\Like;
use think\Request;

class Learn extends Base{
    /**
     * 两学一做
     */
    public function index(){
        $this->anonymous();
        //每日一课数据
        $request = Request::instance() ->domain();
        $this ->assign('request',$request);
        $userid = session('userId');
        $map = array(
            'userid' => $userid,
        );
        $Answers = Answers::where($map)->whereTime('create_time','d')->find();
        $this->assign('check',0);//1为答过题
        //两学一做数据
        //党员先锋  列表
        $map3 = array(
            'type' => 3,
            'status' => array('egt',0),
        );
        $list3 = LearnModel::where($map3)->limit(10)->order('id desc')->select();
        $this->assign('list3',$list3);
        //手机党校  列表
        $map2 = array(
            'type' => array('in',[1,2]),
            'status' => array('egt',0),
        );
        $list2 = LearnModel::where($map2)->limit(7)->order('id desc')->select();
        $this->assign('list2',$list2);
        if(empty($Answers)){   // 没有数据
            //取单选
            $arr=Question::all(['type'=>0]);
            foreach($arr as $value){
                $ids[]=$value->id;
            }
            //随机获取单选的题目
            $num=5;//题目数目
            $data=array();
            while(true){
                if(count($data) == $num){
                    break;
                }
                $index=mt_rand(0,count($ids)-1);
                $res=$ids[$index];
                if(!in_array($res,$data)){
                    $data[]=$res;
                }
            }
            foreach($data as $value){
                $question[]=Question::get($value);
            }
            $this->assign('question',$question);
            return $this->fetch();
        }else{  //  有数据
            // 当天已经答过题
            $Qid = json_decode($Answers->question_id);
            $rights=json_decode($Answers->value);
            $re = array();
            foreach($Qid as $key => $value){
                $re[$key] = Question::get($value);
                $re[$key]['right'] = $rights[$key];
            }
            $this->assign('question',$re);
            $this->assign('check',1);//1为答过题
            return $this->fetch();
        }
    }
    /*
   * 每日一课 提交
   */
    public function commit(){
        // 获取用户提交数据
        $data = input('post.');
        $arr = $data['data'];
        $question = array();
        $status = array();
        $options = array();
        $Right = array();
        $score = 0;
        foreach($arr as $key => $value){
            $Question=Question::get($value[0]);
            switch($Question->value){
                case 1:
                    $right = "A";
                    break;
                case 2:
                    $right = "B";
                    break;
                case 3:
                    $right = "C";
                    break;
                case 4:
                    $right = "D";
                    break;

            }
            $Right[$key+1] = $right;
            $question[$key] = $value[0];
            $options[$key] = $value[1];
            // 判断 题目的对错
            if($value[1] == $Question->value){
                $status[$key] = 1;
                $score ++;
            }else{
                $status[$key] = 0;
            }
        }
        //将获取的数据进行json格式转化
        $questions = json_encode($question);
        $rights = json_encode($options);
        $status = json_encode($status);
        $users = session('userId');
        //将分数添加至用户积分排名
        $wechatModel = new WechatUser();
        $wechatModel->where('userid',$users)->setInc('score',$score);
        //  存储 表
        $Answers = new Answers();
        $Answers->userid = $users;
        $Answers->question_id = $questions;
        $Answers->value = $rights;
        $Answers->status = $status;
        $Answers->score = $score;
        $Answers->create_time = time();
        $res = $Answers->save();
        if($res){
            return $this->success('提交成功',array('id' =>$res,'score'=>$score,'right'=>$Right));
        }else{
            return $this->error('提交失败');
        }
    }
    /*
    * 每日一课  查看详情
    */
    public function scan(){
        $id = input('id/d');
        $Answers = Answers::get($id);
        if (empty($Answers)){
            return $this->error('系统错误,不存在该条数据',Url('Constitution/course'));
        }
        $Qid = json_decode($Answers->question_id);
        $rights=json_decode($Answers->value);
        $re = array();
        foreach($Qid as $key => $value){
            $re[$key] = Question::get($value);
            $re[$key]['right'] = $rights[$key];
        }
        $this->assign('question',$re);
        $this->assign('check',1);//1为答过题
        return $this->fetch();
    }
    /**
     * 主页加载更多
     */
    public function indexmore(){
        $len = input('length');
        $type = input('type');
        if ($type == 0){
            $map = array(
                'type' => array('in',[1,2]),
                'status' => array('egt',0),
            );
        }else{
            $map = array(
                'type' => 3,
                'status' => array('egt',0),
            );
        }
        $list = LearnModel::where($map)->order('id desc')->limit($len,7)->select();
        foreach($list as $value){
            $img = Picture::get($value['front_cover']);
            $value['path'] = $img['path'];
            $value['time'] = date("Y-m-d",$value['create_time']);
        }
        if($list){
            return $this->success("加载成功",'',$list);
        }else{
            return $this->error("加载失败");
        }
    }
    /**
     * 视频课程
     */
    public function video(){
        $this->anonymous();        //判断是否是游客
        $this->jssdk();
        $id = input('id');
        if (empty($id)){
            $this ->error('参数错误!');
        }
        $this->assign('detail',$this->content(3,$id));
        return $this->fetch();
    }

    /**
     * 图文课程
     */
    public function article(){
        $this->anonymous();        //判断是否是游客
        $this->jssdk();
        $id = input('id');
        if (empty($id)){
            $this ->error('参数错误!');
        }
        $this->assign('detail',$this->content(3,$id));
        return $this->fetch();
    }
    /**
     * 党员先锋   详情
     */
    public function detail(){
        $this->anonymous();        //判断是否是游客
        $this->jssdk();
        $id = input('id');
        if (empty($id)){
            $this ->error('参数错误!');
        }
        $this->assign('detail',$this->content(3,$id));
        return $this->fetch();
    }
}