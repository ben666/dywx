<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/22 0022
 * Time: 下午 4:39
 */

namespace app\home\controller;


class Focus extends Base{

    public function index(){

        return $this->fetch();
    }
}