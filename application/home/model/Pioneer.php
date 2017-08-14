<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/6/20
 * Time: 15:11
 */
namespace app\home\model;
use think\Model;
use app\home\model\PioneerStory;
class Pioneer extends Model{
    // 获取内容
    public function get_content($id){
        $list = $this->where(['id' => $id,'status' => ['egt',0]])->find();
        if (empty($list)){
            return false;
        }else{
            return $list;
        }
    }
}