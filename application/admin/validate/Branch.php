<?php
/**
 * Created by PhpStorm.
 * User: laowang <958364865@qq.com>
 * Date: 2017/8/9
 * Time: 9:22
 */

namespace app\admin\validate;
use think\Validate;

class Branch extends Validate
{
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