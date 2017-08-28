<?php
/**
 * Created by PhpStorm.
 * User: 虚空之翼 <183700295@qq.com>
 * Date: 16/3/25
 * Time: 09:12
 * 微信事件接口
 */
namespace app\admin\controller;

use com\wechat\Wechat;
use com\wechat\TPWechat;
use think\Config;
use think\Controller;

class Service extends Controller
{

    // 服务号接收的应用
    public function event() {
        $Wechat = new TPWechat(Config::get('party'));
        $Wechat->valid();
        $type = $Wechat->getRev()->getRevType();
        switch ($type) {
            case Wechat::MSGTYPE_TEXT:
                $Wechat->text("您好！感谢关注！")->reply();
                break;
            case Wechat::MSGTYPE_EVENT:
                $event = $Wechat->getRev()->getRevEvent();
                switch ($event['event']) {
                    case 'subscribe':
//                        $newsData = array(
//                            '0'=> array(
//                                'Title' => "欢迎您关注“中国遥感应用协会党建”",
//                                'Description' => "",
//                                'PicUrl' => "",
//                                'Url' => "",
//                            ),
//                        );
//                        $Wechat->news($newsData)->reply();
                        $Wechat->text("您好！欢迎关注 中国遥感应用协会党建平台 ！")->reply();
                        break;
                }
                break;
            case Wechat::MSGTYPE_IMAGE:
                break;
            default:
                $Wechat->text("您好！感谢关注！")->reply();
        }

    }

    // 微信 验证
    public function oauth() {
        $weObj = new TPWechat(Config::get('party'));
        $weObj->valid();
    }
    
    // 创建订阅号菜单
    public function menu() {
        $menu["button"] = array(
            array(
                "type"=>"view",
                "name"=>"登台纵览",
                "url"=>"http://zgyg.0571ztnet.com/home/index/index"
            ),
            array(
                "type"=>"view",
                "name"=>"党的感召",
                "url"=>"http://zgyg.0571ztnet.com/home/index/index"
            ),
            array(
                "type"=>"view",
                "name"=>"习总思想",
                "url"=>"http://zgyg.0571ztnet.com/home/index/index"
            ),
        );
        $Wechat = new TPWechat(Config::get('party'));
        $result = $Wechat->createMenu($menu);

        if($result) {
            return $this->success('提交成功');
        } else {
            $this->error('错误代码：'.$Wechat->errCode.'，消息：'.$Wechat->errMsg);
        }
    }

    public function media() {
        $Wechat = new TPWechat(Config::get('news'));
        $list = $Wechat->getForeverList("news", 0, 20);
        dump($list);
    }
}