<?php
/**
 * Created by PhpStorm.
 * User: 虚空之翼 <183700295@qq.com>
 * Date: 16/5/20
 * Time: 09:14
 */

namespace app\admin\controller;
use app\admin\model\WechatDepartment;
use app\admin\model\WechatUser;

/**
 * Class Wechat 通讯录
 * @package app\admin\controller
 */
class Wechat extends Admin{
    //用户页面
    public function user() {
        $name = input('name');
        $map = ['name' => ['like', "%$name%"],'state' => 1];
        $order = 'review desc,status';
        $list = $this->lists("WechatUser",$map,$order);

        //部门进行转换
        foreach ($list as $key=>$value) {
            $departmentId = $value['department'];
            if($departmentId){
                $record = WechatDepartment::where('id',$departmentId) ->find();
                $list[$key]['department'] = $record['name'];
            }else{
                $list[$key]['department'] = "暂无";
            }
            if ($value['party'] == 1){
                $value['party'] = '是';
            }else{
                $value['party'] = '否';
            }
        }
       // 状态转化
        wechat_status_to_string($list);
        $this->assign('list',$list );
        return $this->fetch();
    }
    //部门页面
    public function department(){
        $list = $this->lists("WechatDepartment",['status' => 1]);
        $this->assign('list', $list);

        return $this->fetch();
    }
    /**
     * 部门添加跟修改
     */
    public function add_department(){
        $data = input('post.');
        if(input('post.')){
            $department = new WechatDepartment();
            $result = $department ->add($data);
            if($result['code']){
                return $this ->success($result['msg']);
            }else{
                return $this ->error($result['msg']);
            }
        }else{
            $id = input('id');
            //$type 1为修改 0为新增
            if($id){
                $type = 1;
                $record = WechatDepartment::where('id',$id) ->field('name') ->find();
                $this ->assign('name',$record['name']);
            }else{
                $type = 0;
            }
            $this ->assign('id',$id);
            $this ->assign('type',$type);
            return $this->fetch();
        }
    }

    /**
     * 删除部门
     */
    public function del_department(){
        $id = input('get.id');
        if($id){
            //该部门已经由成员就禁止删除
            $record = WechatUser::where(['department' => $id,'state' => 1]) ->find();
            if($record){
                return $this ->error('该部门已经存在用户,静止删除!');
            }else{
                $wd = new WechatDepartment();
                $wd ->save(['status' => 0,], ['id' => $id]);
                return $this ->success('删除成功!');
            }
        }else{
            return $this ->error('参数错误!');
        }
    }
    /**
     * 用户的增加与修改
     */
    public function add_user(){
        $data = input('post.');
        $user = new WechatUser();
        if(input('post.')){
            $info = $user ->add($data);
            if($info['code'] == 1){
                return $this ->success($info['msg']);
            }else{
                return $this ->error($info['msg']);
            }
        }else{
            $id = input('id');
            //$type 1为修改 0为新增
            if($id){
                $type = 1;
                $info = $user ->where('id',$id) ->find();
                $this ->assign('info',$info);
            }else{
                $type = 0;
            }
            $department = WechatDepartment::where('status',1) ->select();
            $this ->assign('department',$department);
            $this ->assign('type',$type);
            return $this->fetch();
        }
    }
    /**
     * 删除用户
     */
    public function del_user(){
        $id = input('get.id');
        if($id){
            $user = new WechatUser();
            $record = $user ->save(['state' => 0],['id' => $id]);
            if($record){
                return $this ->success('删除成功');
            }else{
                return $this ->error('删除失败');
            }
        }else{
            return $this ->error('参数错误');
        }
    }
    /**
     * 通讯录excel导入
     */
    public function inserExcel(){
        //引用PHPExcel
        vendor("PHPExcel.Classes.PHPExcel.IOFactory.PHPExcel_IOFactory");
        vendor('PHPExcel.Classes.PHPExcel');
        vendor('PHPExcel.Classes.PHPExcel.Reader.Excel5');
        //获取表单上传文件
        $file = request() ->file('excel');
        $result = $file ->getInfo()['name'];
        $info = $file ->move(ROOT_PATH . 'public' . DS . 'uploads');//上传之后移动地址
        if ($info) {
            $exclePath = $info->getPathName();  //上传文件的地址
            $objReader = \PHPExcel_IOFactory::createReader('Excel5');
            $obj_PHPExcel = $objReader ->load($exclePath, $encode = 'utf-8');  //加载文件内容,编码utf-8
            $excel_array = $obj_PHPExcel ->getsheet(0) ->toArray();   //转换为数组格式
            array_shift($excel_array);  //删除第一个数组(标题);
            $result = $this ->add_excel($excel_array);
            unlink($exclePath);//完成后删除该文件
            return $result;
         } else {
              return $file ->getError();
         }
    }
    /**
     * 通讯录导入处理函数
     */
    public function add_excel($data){
        $user = new WechatUser();
        $wp = new WechatDepartment();
        $sum1 = 0;//记录新增用户记录
        $sum2 = 0;//记录修改用户记录
        $all = array();
        $update = array();//更新数据
        $new = array();//新增数据
        $check = array();//检查新增是否存在相同数据
        foreach($data as $k => $v){
            if (empty($v[0]) && empty($v[1]) && empty($v[2]) && empty($v[3])){
                // 无数据  不做处理
                return ['code' => 0, 'msg' => '必填字段没有填写'];
            }else{
                //前4个字字段为必填字段 名称 性别 手机号码 所属部门
                if (empty($v[0]) || empty($v[1]) || empty($v[2]) || empty($v[3])) {
                    return ['code' => 0, 'msg' => '第' . ($k + 2) . '行必填字段没有填写'];
                }
                //性别数据转化
                if ($v[1] == '男') {
                    $v[1] = 1;
                } else {
                    $v[1] = 2;
                }
                //出生年月
                if ($v[5]) {
                    $temp = mb_ereg_replace('[^0-9]', '/', $v[5]);
                    $v[5] = strtotime(substr($temp,0,-1));
                }
                // 是否 党员
                if($v[7] == '是' || $v[7] == '党员' || $v[7] == '1'){
                    $v[7] = 1;
                }else{
                    $v[7] = 0;
                }
                //入党时间
                if ($v[8]) {
                    $temp = mb_ereg_replace('[^0-9]', '/', $v[8]);
                    $v[8] = strtotime(substr($temp,0,-1));
                }
                $info = array(
                    'name' => $v[0],   //名称
                    'gender' => $v[1], //性别
                    'mobile' => $v[2], //手机号码
                    'department' => $v[3], //部门
                    'position' => $v[4], //职位
                    'birthday' => $v[5],   //出生年月
                    'education' => $v[6],  //学历
                    'party' => $v[7], // 是否党员
                    'partytime' => $v[8],  //入党时间
                );
                array_push($all, $info);
            }
        }
        //转换部门
        foreach ($all as $k =>$v) {
            //数据转化
            $result = WechatDepartment::where(['name' => $all[$k]['department'], 'status' => 1])->find();
            //部门数据转化 对应部门不存在就增加部门
            if ($result) {
                //部门id
                $all[$k]['department'] = $result['id'];
            } else {
                $dp = array(
                    'name' => $all[$k]['department'],
                    'id' => null
                );
                $record = $wp->add($dp);
                $all[$k]['department'] = $record['id'];
            }
        }
        //数据储存
        foreach ($all as $k =>$v) {
            // 同手机号进行覆盖
            $map = ['mobile' => $all[$k]['mobile'], 'state' => 1];
            $tel = WechatUser::where($map)->find();
            //存在更新 不存在新增
            if ($tel) {
                $sum2++;
                $all[$k]['id'] = $tel['id'];
                array_push($update, $all[$k]);
            } else {
                $sum1++;
                $tel = $all[$k]['mobile'];
                if(!isset($check[$tel])){
                    $check[$tel] = true;
                    array_push($new, $all[$k]);
                }
            }
        }
        //新增用户保存
        $user ->saveAll($new);
        //更新用户保存
        foreach($update as $data){
            $id = $data['id'];
            unset($data['id']);
            $user ->where('id',$id) ->update($data);
        }
        return  $this ->success("导入成功,新增数据{$sum1}条,修改数据{$sum2}条!");
    }

}