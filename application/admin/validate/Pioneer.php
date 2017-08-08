<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/6/19
 * Time: 17:57
 */
namespace app\admin\validate;
use think\Validate;

class Pioneer extends Validate{
    protected $rule = [
        'front_cover' => 'require',
        'name' => 'require',
        'position' => 'require',
        'content' => 'require'
    ];

    protected $message = [
        'front_cover.require'  =>  '请上传头像！',
        'name.require' =>  '请输入名称！',
        'position.require'  =>  '请填写职位！',
        'content.require'  =>  '请填写简介！',
    ];
    protected $scene = [
        'other' => ['front_cover','name','position','content'],
        'another' => ['front_cover','name','content']
    ];
}