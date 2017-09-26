<?php
/**
 * Created by PhpStorm.
 * User: laowang <958364865@qq.com>
 * Date: 2017/9/26
 * Time: 9:13
 */

namespace app\home\model;
use think\Model;
/**
 * Class Party
 * @package app\home\model 三会一课  模型类
 */
class Party extends Model
{
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
        $limit = "$length,2";
        $list = $this ->where($map) ->order($order) ->limit($limit) ->select();
        if(!empty($list))
        {
            return $list[0] ->data;
        }else{
            return $list;
        }
    }
    // 获取列表数据
    public function get_list($where,$len=0,$res=false){
        if ($res){
            $num = 10;
        }else{
            $num = 2;
        }
        $list = $this->where($where)->order('id desc')->limit($len,$num)->select();
        foreach($list as $value){
            $value['create_time'] = date("Y-m-d",$value['create_time']);
            $Pic = Picture::where('id',$value['front_cover'])->field('path')->find();
            $value['front_cover'] = $Pic['path'];
            if ($value['type'] == 1){
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