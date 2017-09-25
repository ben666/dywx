<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/22 0022
 * Time: 下午 4:39
 */
namespace app\home\controller;
use app\home\model\News;
/**
 * Class Focus
 * @package app\home\controller  第一聚焦
 */
class Focus extends Base{
    /**
     * 主页
     */
    public function index(){
        $News = new News();
        $map = array(
            'status' => ['egt',0],
        );
        $maps = array(
            'status' => ['egt',0],
            'recommend' => 0
        );
        $list = $News->get_list($map);  // 列表
        $top = $News->get_list($maps); // 推荐
        $this->assign('list',$list);
        $this->assign('top',$top);
        return $this->fetch();
    }
    /**
     * 详情页面
     */
    public function detail(){
        //游客模式
        $this ->anonymous();
        $this ->jssdk();
        $id = input('get.id');
        if (empty($id)){
            $this ->error('参数错误!');
        }
        $this->assign('detail',$this->content(1,$id));
        return $this->fetch();
    }
}