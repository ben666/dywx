<?php
/**
 * Created by PhpStorm.
 * User: laowang <958364865@qq.com>
 * Date: 2017/8/14
 * Time: 17:27
 */

namespace app\home\model;
use think\Model;

class BranchVote extends Model
{
    protected $insert = [
        'create_time' => NOW_TIME,
    ];
    //  获取 是否 同意
    public function getLike($aid,$uid) {
        $map = array(
            'vote_id' => $aid,
            'userid' => $uid,
        );
        $res = $this->where($map)->field('status')->find();  // 0 是未投票 1 是赞成 2 反对
        if (empty($res)){
            $res['status'] = 0;
        }
        return $res['status'];
    }
}