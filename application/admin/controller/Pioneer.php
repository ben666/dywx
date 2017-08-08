<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/6/19
 * Time: 16:38
 */
namespace app\admin\controller;
use app\admin\model\Pioneer as PionnerModel;
use app\admin\model\PioneerStory;
//先锋引领
class Pioneer extends Admin{
    /**
     * 国家先锋
     *
     */
    public function country(){
        $map = array(
            'status' => ['egt',0],
            'type' => 1,
        );
        $list = $this->lists('Pioneer',$map);
        int_to_string($list,[
            'class' => ['1' => '个人',2=>"集体" ,3=>"单位"]
        ]);
        $this->assign('list',$list);
        return $this ->fetch();
    }

    /**
     * 国防科技先锋
     */
    public function defence(){
        $map = array(
            'status' => ['egt',0],
            'type' => 2,
        );
        $list = $this->lists('Pioneer',$map);
        int_to_string($list,[
            'class' => ['1' => '个人',2=>"集体" ,3=>"单位"]
        ]);
        $this->assign('list',$list);
        return $this ->fetch();
    }

    /**
     * 中国遥感先锋
     */
    public function remote(){
        $map = array(
            'status' => ['egt',0],
            'type' => 3,
        );
        $list = $this->lists('Pioneer',$map);
        int_to_string($list,[
            'class' => ['1' => '个人',2=>"集体" ,3=>"单位"]
        ]);
        $this->assign('list',$list);
        return $this ->fetch();
    }

    /**
     * 协会先锋
     */
    public function association(){
        $map = array(
            'status' => ['egt',0],
            'type' => 4,
        );
        $list = $this->lists('Pioneer',$map);
        int_to_string($list,[
            'class' => ['1' => '个人',2=>"集体" ,3=>"单位"]
        ]);
        $this->assign('list',$list);
        return $this ->fetch();
    }
    /**
     * 先锋事迹展
     */
    public function story(){
        $pid = input('pid');
        $map = array(
            'status' => array('egt',0),
            'pid' => $pid
        );
        $list = $this->lists('PioneerStory',$map);
        int_to_string($list,array(
            'status' => array(0 =>"已发布",1=>"已发布"),
            'recommend' => array( 1=>"是" , 0=>"否"),
            'push' => array( 1=>"是" , 0=>"否"),
        ));
        $this->assign('type',input('type'));
        $this->assign('pid',$pid);
        $this->assign('list',$list);

        return $this->fetch();
    }
    /**
     *  新增 修改
     */
    public function addpb(){
        $Pioneer = new PionnerModel();
        if (IS_POST){
            $data = input('post.');
            $result = $Pioneer->get_save($data);
            if($result) {
                switch ($data['type']){
                    case 1:
                        return  $this->success('操作成功',Url('Pioneer/country'));
                    case 2:
                        return  $this->success('操作成功',Url('Pioneer/defence'));
                        break;
                    case 3:
                        return  $this->success('操作成功',Url('Pioneer/remote'));
                        break;
                    case 4:
                        return  $this->success('操作成功',Url('Pioneer/association'));
                        break;
                }
            }else {
                $this->error($Pioneer->getError());
            }
        }else{
            // 添加页面
            $this->assign('msg', $Pioneer->get_content(input('get.id')));
            $this->assign('type',input('get.type'));
            return $this->fetch();
        }
    }
    /**
     * 事迹 新增 修改
     */
    public function addsy(){
        $Story = new PioneerStory();
        if (IS_POST){
            $data = input('post.');
            if ($data['type']){
                $type = $data['type'];
            }
            unset($data['type']);
            $result = $Story->get_save($data);
            if($result) {
                return  $this->success('操作成功',Url('Pioneer/story?type='.$type.'&pid='.$data['pid']));
            }else {
                $this->error($Story->getError());
            }
        }else{
            // 添加 修改 页面
            $this->assign('msg', $Story->get_content(input('get.id')));
            $this->assign('type', input('type'));
            $this->assign('pid', input('pid'));
            return $this->fetch();
        }
    }
    /**
     * 事迹 删除
     */
    public function del(){
        $id = input('get.id');
        $Story = new PioneerStory();
        if(empty($id))
        {
            return $this ->error('参数错误!');
        }else{
            $res = $Story->get_status($id);
            if($res)
            {
                return $this ->success('删除成功!');
            }else{
                return $this ->error('删除失败!');
            }
        }
    }
    /**
     * 先锋引领   删除
     */
    public function dele(){
        $id = input('get.id');
        $Pioneer = new PionnerModel();
        if(empty($id))
        {
            return $this ->error('参数错误!');
        }else{
            $res = $Pioneer->get_status($id);
            if($res)
            {
                return $this ->success('删除成功!');
            }else{
                return $this ->error('删除失败!');
            }
        }
    }
}
