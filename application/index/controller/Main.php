<?php
namespace app\index\controller;
use think\Db;
use app\index\controller\Common;
use app\index\model\RbacUser;
use app\index\model\RbacRole;
use app\index\model\UserInfo;
use app\index\model\RbacUserHasRoles;
use app\index\model\Log;

class Main extends Common
{
    public function index()
    {
        $dateMsg = date("Y年n月d日");
        $hour = date("G");
        $month = date("n");
        
        if($month>2 && $month <=5){
            $msg2 = "换季空气干燥，要记得多喝水哦~";
        }
        else if($month >5 && $month<=8){
            $msg2 = "天气炎热，请注意防暑防晒~";
        }
        else if($month>8 && $month<=11){
            $msg2 = "天气转凉，请注意添衣保暖~";
        }
        else{
            $msg2 = "天气寒冷，请注意御寒保暖~";
        }

        if( $hour>5 && $hour<=11){
            $msg1 = "上午好！";
            $msg2 = "祝您度过美好的一天！";
        }
        else if ($hour>11 && $hour <=14 ){
            $msg1 = "中午好！";
        }
        else if($hour>14 && $hour <= 18){
            $msg1 = "下午好！";
        }
        else if($hour>18 && $hour <= 22){
            $msg1 = "晚上好！";
        }
        else{
            $msg1 = "";
            $msg2 = "夜深了，请注意早点休息~";
        }
        $this->assign('dateMsg', $dateMsg);
        $this->assign('msg1', $msg1);
        $this->assign('msg2', $msg2);
        
        
        $userid = $this->auth->getUserid();
        $userName = $this->auth->getUserName();
        $log = Db::table('log l')
        ->join('user_info u0', 'u0.id=l.log_user_id0')
        
        ->join('user_info u1', 'u1.id=l.log_user_id1')
        ->join('user_contact c1','u1.id=c1.id')
        ->join('department d1','d1.id=u1.department')
        ->join('major m1','m1.id=u1.major')
        ->join('rbac_user_has_roles ur1', 'ur1.user_id = u1.id')
        ->join('rbac_role r1', 'r1.id = ur1.role_id')
        
        ->field 
        ('
            l.log_id as id,
            l.time as time,
            l.log_type as type,
            l.attribute_name as attribute_name,
            l.new_info as new_info,
            
            u0.id as id0,
            u0.name as name0,
            
            u1.id as id1,
            u1.name as name1,
            u1.del as del1
        ')
        
        ->where
            ([
                'l.log_user_id0' => $userid

            ])
        ->order('l.log_id desc' )
        ->limit(10)
        ->select();
        
        $this->assign('log' , $log);
        return $this->fetch();
    }
}