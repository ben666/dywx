<?php
/**
 * Created by PhpStorm.
 * User: laowang <958364865@qq.com>
 * Date: 2017/9/22
 * Time: 13:43
 */

namespace app\admin\validate;
use think\Validate;

class Affiche extends Validate
{
    protected $rule = [
        'title' => 'require',
        'content' => 'require',
        'publisher' => 'require',

    ];

    protected $message = [
        'title' =>  '标题不能为空',
        'content'  =>  '内容不能为空',
        'publisher' => '发布人不能为空',
    ];
}