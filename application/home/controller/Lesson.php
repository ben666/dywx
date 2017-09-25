<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/25 0025
 * Time: 上午 10:58
 */

namespace app\home\controller;


class Lesson extends Base
{
    public function index(){

        return $this ->fetch();
    }

    public function noticemore(){

        return $this ->fetch();
    }

    public function meetingmore(){

        return $this ->fetch();
    }
}