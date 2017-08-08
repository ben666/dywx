<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/2/8
 * Time: 14:52
 */

namespace app\admin\validate;
use think\Validate;

class Wish extends Validate {
    protected $rule = [
        'front_cover' => 'require',
        'title' => 'require',
        'description' => 'require',
        'content' => 'require',
        'publisher' => 'require',
    ];

    protected $message = [
        'title' =>  '标题不能为空',
        'description' => '简介不能为空',
        'content'  =>  '内容不能为空',
        'publisher'  =>  '发布人不能为空',
        'front_cover' => '封面图不能为空'
    ];
    protected $scene = [
        'other' => ['title','description','content','publisher'],
        'another' => ['title','front_cover','content','publisher'],
    ];
}