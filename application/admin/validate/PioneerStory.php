<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/6/19
 * Time: 17:57
 */
namespace app\admin\validate;
use think\Validate;

class PioneerStory extends Validate{
    protected $rule = [
        'front_cover' => 'require',
        'title' => 'require',
        'content' => 'require',
        'publisher' => 'require',
    ];

    protected $message = [
        'front_cover'  =>  '请上传图片！',
        'title' =>  '请输入标题！',
        'content'  =>  '请填写内容！',
        'publisher'  =>  '请填写发布人！',
    ];
}