<?php
/**
 * Created by PhpStorm.
 * User: laowang <958364865@qq.com>
 * Date: 2017/8/9
 * Time: 11:28
 */

namespace app\home\model;
use think\Model;

class PioneerStory extends Model
{
// 获取内容
    public function get_content($id){
        $list = $this->where(['pid' => $id,'status' => ['egt',0]])->order('id desc')->limit(7)->select();
        if (empty($list)){
            return false;
        }else{
            return $list;
        }
    }
}