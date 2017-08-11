<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/6/1
 * Time: 16:32
 */
namespace app\home\model;
use think\Model;

class News extends Model{
    /**
     * 首页获取推荐的数据
     * @param $length
     * @param string $push 推送数据获取
     */
    public function getDataList($length,$push="0"){
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
        $list = $this->where($where)->order('id desc')->limit($len,7)->field('id,front_cover,title,publisher,create_time')->select();
        foreach($list as $value){
            $value['create_time'] = date("Y-m-d",$value['create_time']);
            $Pic = Picture::where('id',$value['front_cover'])->field('path')->find();
            $value['front_cover'] = $Pic['path'];
        }
        return $list;
    }
}