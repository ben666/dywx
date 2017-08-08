<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/6/19
 * Time: 17:51
 */
namespace app\admin\model;
use think\Model;
use app\admin\model\PioneerStory;
class Pioneer extends Model{
    protected $insert = [
        'create_user' => UID,
        'create_time' => NOW_TIME,

    ];
    //获取后台用户名称
    public function user(){
        return $this->hasOne('Member','id','create_user');
    }
    //  获取该条数据 详情
    public function get_content($id){
        if (empty($id)){
            return false;
        }
        $info = $this->get($id);
        return $info;
    }
    // 获取 数量
    public function get_num(){
        $pid = $this->getData('id');
        $num = PioneerStory::where(['pid' => $pid,'status' => 0])->count();
        return $num;
    }
    // 添加 修改 数据
    public function get_save($data){
        if (empty($data['id'])){
            // 添加
            unset($data['id']);
            if ($data['class'] == 1){
                $res = $this->validate('Pioneer.other')->save($data);
            }else{
                $res = $this->validate('Pioneer.another')->save($data);
            }
            return  $res;
        }else{
            // 修改
            if ($data['class'] == 1){
                $res = $this->validate('Pioneer.other')->save($data,['id' => $data['id']]);
            }else{
                $res = $this->validate('Pioneer.another')->save($data,['id' => $data['id']]);
            }
            return  $res;
        }
    }
    // 删除
    public function get_status($id){
        $res = $this->where('id',$id)->update(['status' => -1]);
        if ($res){
            $info = PioneerStory::where('pid',$id)->find();
            if ($info){
                $result = PioneerStory::where('pid',$id)->update(['status' => -1]);
            }else{
                $result = true;
            }
        }else{
            $result = false;
        }
        return $result;
    }
}