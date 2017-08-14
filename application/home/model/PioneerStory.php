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
    public function get_content($id,$len=0){
        $list = $this->where(['pid' => $id,'status' => ['egt',0]])->order('id desc')->limit($len,7)->select();
        if (empty($list)){
            return false;
        }else{
            foreach($list as $value){
                $img = Picture::get($value['front_cover']);
                $value['front_cover'] = $img['path'];
                $value['create_time'] = date("Y-m-d",$value['create_time']);
            }
            return $list;
        }
    }
    /**
     * 首页获取推荐的数据
     * @param $length
     * @param string $push 推送数据获取
     */
    public function getDataList($length,$push=0){
        $map = array(
            'status' => ['egt',0],
            'recommend' => 1,
            'push' => ['egt',$push]
        );
        $order = 'create_time desc';
        $limit = "$length,1";
        $list = $this ->where($map) ->order($order) ->limit($limit) ->select();
        if(!empty($list))
        {
            return $list[0] ->data;
        }else{
            return $list;
        }
    }
}