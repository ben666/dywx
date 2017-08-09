<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/2/8
 * Time: 13:52
 */
namespace app\admin\model;
use think\Model;
class Branch extends Model {
    protected $insert = [
        'create_time' => NOW_TIME,
        'status' => 0,
        'create_user' => UID
    ];
    protected $update = [
        'update_time' => NOW_TIME,
        'update_user' => UID
    ];
    public function user() {
        return $this->hasOne('Member','id','create_user');
    }
    public function depart(){
        return $this->hasOne('WechatDepartment','id','department');
    }
    //  获取该条数据 详情
    public function get_content($id){
        if (empty($id)){
            return false;
        }
        $info = $this->get($id);
        return $info;
    }
    // 添加 修改 数据
    public function get_save($data){
        if (empty($data['id'])){
            // 添加
            unset($data['id']);
            $res = $this->validate('Wish.another')->save($data);
            return  $res;
        }else{
            // 修改
            $res = $this->validate('Wish.another')->save($data,['id' => $data['id']]);
            return  $res;
        }
    }
}