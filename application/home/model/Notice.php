<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/10/12
 * Time: 10:23
 */

namespace app\home\model;
use think\Model;
use app\home\model\Picture;
class Notice extends Model {
    //首页获取推荐的数据
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
    // 获取列表数据
    public function get_list($where,$len=0){
        $list = $this->where($where)->order('id desc')->limit($len,10)->field('id,type,front_cover,title,publisher,create_time,end_time')->select();
        foreach($list as $value){
            $value['create_time'] = date("Y-m-d",$value['create_time']);
            $Pic = Picture::where('id',$value['front_cover'])->field('path')->find();
            $value['front_cover'] = $Pic['path'];
            if ($value['type'] == 2){
                // 通知
                $value['is_over'] = 0;  // 未结束
                if (!empty($value['end_time']) && $value['end_time'] < time()){
                    $value['is_over'] = 1;  // 已结束
                }
            }
        }
        return $list;
    }
}