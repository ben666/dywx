<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/9/21
 * Time: 14:41
 */
namespace app\admin\controller;

use think\Controller;
use app\admin\model\Picture;
use app\admin\model\News as NewsModel;
use think\Config;
/**
 * Class News
 * @package 新闻动态 控制器
 */
class News extends Admin {

    /**
     * 新闻发布 主页列表
     */
    public function index(){
        $map = array(
            'type' => 1 , // 新闻发布
            'status' => array('egt',0),
        );
        $search = input('search');
        if ($search != '') {
            $map['title'] = ['like','%'.$search.'%'];
        }
        $list = $this->lists('News',$map);
        int_to_string($list,[
            'status' => array(0=>"已发布",1=>"已发布"),
            'recommend' => [0 => "否" , 1 => "是"],
            'push' => [0 => '否' , 1 => '是']
        ]);
        $this->assign('list',$list);
        return $this->fetch();
    }
    /**
     * 活动情况  主页
     */
    public function activity(){
        $map = array(
            'type' => 2 , // 活动情况
            'status' => array('egt',0),
        );
        $search = input('search');
        if ($search != '') {
            $map['title'] = ['like','%'.$search.'%'];
        }
        $list = $this->lists('News',$map);
        int_to_string($list,[
            'status' => array(0=>"已发布",1=>"已发布"),
            'recommend' => [0 => "否" , 1 => "是"],
            'push' => [0 => '否' , 1 => '是']
        ]);
        $this->assign('list',$list);
        return $this->fetch();
    }
    /**
     * 新闻发布  活动情况   添加  修改
     */
    public function edit(){
        $News = new NewsModel();
        if (IS_POST){
            $data = input('post.');
            $result = $News->get_save($data);
            if($result) {
                if ($data['type'] == 1){
                    return $this->success('操作成功', Url('News/index'));
                }else{
                    return $this->success('操作成功', Url('News/activity'));
                }
            }else{
                $this->error($News->getError());
            }
        }else{
            // 添加页面
            $this->assign('msg', $News->get_content(input('get.id')));
            $this->assign('type',input('type'));
            return $this->fetch();
        }
    }
    /**
     * 删除功能
     */
    public function del(){
        $id = input('id');
        if (empty($id)){
            $this->error('系统参数错误');
        }
        $News = new NewsModel();
        $info = $News->get_status($id);
        if($info) {
            return $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }

    }
}