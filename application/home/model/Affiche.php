<?php
/**
 * Created by PhpStorm.
 * User: laowang <958364865@qq.com>
 * Date: 2017/8/11
 * Time: 9:33
 */

namespace app\home\model;
use  think\Model;

/**
 * Class Branch
 * @package 通知公告 模型类
 */
class Affiche extends Model
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
        $list = $this->where($where)->order('id desc')->limit($len,10)->field('id,title,publisher,create_time')->select();
        foreach($list as $value){
            $value['create_time'] = date("Y-m-d",$value['create_time']);
        }
        return $list;
    }
}