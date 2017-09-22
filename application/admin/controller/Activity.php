<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/5/12
 * Time: 10:46
 */
namespace app\admin\controller;
use app\admin\model\Affiche;
 /**
  * 通知公告  控制器
  */
 class Activity extends Admin{
     /*
      * 活动 列表 主页
      */
     public function index(){
         $map = array(
             'status' => array('egt',0),
         );
         $search = input('search');
         if ($search != '') {
             $map['title'] = ['like','%'.$search.'%'];
         }
         $list = $this->lists('Affiche',$map);
         int_to_string($list,array(
             'status' => array(0=>"已发布",1=>"已发布"),
         ));
         $this->assign('list',$list);
         return $this->fetch();
     }
     /**
      * 活动报道  添加  修改
      */
     public function edit(){
         $affiche = new Affiche();
         if (IS_POST){
             $data = input('post.');
             $result = $affiche->get_save($data);
             if($result) {
                 return $this->success('操作成功', Url('Activity/index'));
             }else{
                 $this->error($affiche->getError());
             }
         }else{
             // 添加页面
             $this->assign('msg', $affiche->get_content(input('get.id')));
             return $this->fetch();
         }
     }
     /**
      * 通知公告 删除
      */
     public function del() {
         $id = input('id');
         if (empty($id)){
             return $this->error('系统参数错误');
         }
         $Affiche = new Affiche();
         $res = $Affiche->get_status($id);
         if($res){
             return $this->success('删除成功');
         }else{
             return $this->error('删除失败');
         }
     }
 }