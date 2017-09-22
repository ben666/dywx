<?php
/**
 * Created by PhpStorm.
 * User: laowang <958364865@qq.com>
 * Date: 2017/9/22
 * Time: 15:29
 */

namespace app\admin\validate;
use think\Validate;
class Party extends Validate
{
    protected $rule = [
        'front_cover' => 'require',
        'title' => 'require',
        'description' => 'require',
        'start_time' => "require",
        'end_time' => "require",
        "address" => "require",
        "people" => "require",
        'content' => 'require',
        'publisher' => 'require',
    ];

    protected $message = [
        'title.require' => '标题不能为空',
        'front_cover.require' => '封面图片不能为空',
        'content.require' => '内容不能为空',
        'publisher.require' => '发布人不能为空',
        'description' => '简介不能为空',
        'start_time' => "开始时间不能为空",
        'end_time' => "截止时间不能为空",
        "address" => "地址不能为空",
        "people" => "参会支部不能为空",
    ];
    protected $scene = [
        'other' => ['front_cover','title','description','start_time','end_time','address','people','content','publisher'],
        'another' => ['front_cover','title','content','publisher']
    ];
}