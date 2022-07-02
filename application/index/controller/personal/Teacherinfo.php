<?php
namespace app\index\controller\personal;
use think\Request;
use app\index\model\UserInfo;
use app\index\controller\Common;
use think\Db;

# 之前的查询个人详细信息页面分离成两个页面：教师、学生查询自己信息页面
# 本页面是教师查询自己的个人详细信息页面
# 权限：（包括超级管理员、教师管理员）
# 从两个表里取数据，user_info取出基本信息，user_contact取出联系信息
# 教师的导师、导师联系方式、紧急联系人、紧急联系人联系方式、居住地址均为空，前端表单不显示这些字段
# 有日志记录功能，记录本人查看了自己的个人信息
# 本地与云开发的跳转链接统一格式：使用./ 或者 ../

class TeacherInfo extends Common
{
    public function index()
    {
        // $user = userinfo::get($this->auth->getUserid());
        $user_id = $this->auth->getUserid();

        $user = Db::query("select * from user_info where id=?",[$user_id]);
        $user_contact = Db::query("select * from user_contact where id=?",[$user_id]);
 

        $this->assign("no",$user[0]["no"]);
        $this->assign("name",$user[0]["name"]);
        $this->assign("sex",$user[0]["sex"]);
       
        $this->assign("birth",$user[0]["birth"]);
        $this->assign("phone",$user_contact[0]["phone"]);
        $this->assign("email",$user_contact[0]["email"]);
        // $this->assign("address",$user[0]["address"]);
        
        $this->assign("vaccines",$user[0]["vaccines"]);

        // $manage_info = Db::query('select * from user_manage_department where user_id=?',[$user_id]);
        // $manage_id = $manage_info[0]["department"];
        // $department_info = Db::query('select * from department where id=?',[$manage_id]);
        // $this->assign("department",$department_info["name"]);
        
        $department_name = Db::table('user_manage_department g')
            ->field('d.name as department_name')
            ->join('department d','d.id=g.department','LEFT')
            ->where('g.user_id = ?',[$user_id])
            ->where('del = 0')
            ->select();
        // foreach($department_name as $di){
        //     foreach($di as $ddi){
        //         echo $ddi, '\n';
        //     }
        // }

        $this->assign('student',$department_name);


        $major_info = Db::query('select * from major where id=?',[$user[0]["major"]]);
        $this->assign("major",$major_info[0]["name"]);

        $status_info = Db::query('select * from status where status_id=?',[$user_contact[0]["status"]]);
        $this->assign("status",$status_info[0]["status_name"]);

        $this->assign("hesuan",$user[0]["last_hesuan"]);

        // $hesuan_info = Db::query('select * from hesuan where user_id=?',[$user_id]);
        // $this->assign("hesuan",$hesuan_info[0]["cov_time"]);

        // $this->assign('no',$user->no);
        // $this->assign('name',$user->name);
        // $this->assign('sex',$user->sex);
        // // $this->assign('major',$user->major);
        // $this->assign('professor',$user->professor);
        // $this->assign('professor_phone',$user->professor_phone);
        // // $this->assign('department',$user->department);
        // $this->assign('birth',$user->birth);
        // $this->assign('phone',$user->phone);
        // $this->assign('email',$user->email);
        // $this->assign('address',$user->address);
        // // $this->assign('status',$user->status);
        // $this->assign('parent',$user->parent);
        // $this->assign('parent_phone',$user->parent_phone);

        // $this->assign('vaccines',$user->vaccines);

        // $class_info = Db::query('select * from department where id=?',[$user->department]);
        // $this->assign("department",$class_info[0]["name"]);

        // $major_info = Db::query('select * from major where id=?',[$user->major]);
        // $this->assign("major",$major_info[0]["name"]);

        // $status_info = Db::query('select * from status where status_id=?',[$user->status]);
        // $this->assign("status",$status_info[0]["status_name"]);

        return $this->fetch();
    }
    
    
}