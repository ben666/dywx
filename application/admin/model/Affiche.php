<?php
/**
 * Created by PhpStorm.
 * User: laowang <958364865@qq.com>
 * Date: 2017/9/22
 * Time: 11:05
 */

namespace app\admin\model;
use think\Model;
/**
 * Class Affiche
 * @package app\admin\model  通知公告
 */
class Affiche extends Model
{
    public $insert = [
        'create_time' => NOW_TIME,
        'create_user' => UID
    ];
    //获取后台用户名称
    public function user(){
        return $this->hasOne('Member','id','create_user');
    }
    // 添加 修改 数据
    public function get_save($data){
        if (empty($data['id'])){
            // 添加
            unset($data['id']);
            $res = $this->validate(true)->save($data);
            return  $res;
        }else{
            // 修改
            $res = $this->validate(true)->save($data,['id' => $data['id']]);
            return  $res;
        }
    }
    //  获取该条数据 详情
    public function get_content($id){
        if (empty($id)){
            return false;
        }
        $info = $this->get($id);
        return $info;
    }
    // 删除
    public function get_status($id){
        $res = $this->where('id',$id)->update(['status' => -1]);
        return $res;
    }
}