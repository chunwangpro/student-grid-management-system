<?php
namespace app\index\controller\personal;
use think\Request;
use app\index\model\UserInfo;
use app\index\controller\Common;
use think\Db;


# 之前的查询个人详细信息页面分离成两个页面：教师、学生查询自己信息页面
# 本页面是学生查询自己的个人详细信息页面
# 权限：（包括学生、学生管理员）
# 从两个表里取数据，user_info取出基本信息，user_contact取出联系信息
# 在联系信息中，包含自己的信息，自己的联络人姓名，联络人的联系方式、邮箱
# 有日志记录功能，记录本人查看了自己的个人信息
# 本地与云开发的跳转链接统一格式：使用./ 或者 ../

class StudentInfo extends Common
{
    public function index()
    {
        // $user = userinfo::get($this->auth->getUserid());
        $user_id = $this->auth->getUserid();

        $user = Db::query("select * from user_info where id=? and del=?",[$user_id,0]);
        $user_contact = Db::query("select * from user_contact where id=?",[$user_id]);
 

        $this->assign("no",$user[0]["no"]);
        $this->assign("name",$user[0]["name"]);
        $this->assign("sex",$user[0]["sex"]);
        $this->assign("professor",$user_contact[0]["professor"]);
        $this->assign("professor_phone",$user_contact[0]["professor_phone"]);

        if($user_contact[0]["contact_pid"] == null){ $this->assign("helper", " ");}
        else{
            $helper_id = $user_contact[0]["contact_pid"];
            $helper = Db::query("select * from user_info where id=?",[$helper_id]);
            $helper_contact = Db::query("select * from user_contact where id=?",[$helper_id]);
            // echo "123";
            // echo $helper_id;
            $this->assign("helper",$helper[0]["name"]);
            $this->assign("helper_contact",$helper_contact[0]["phone"]);
        }
        
        
        $department_id = $user[0]["department"];
        $stu_manager = Db::table('user_manage_department d')
            ->where('d.department = ?',[$department_id])
            ->where('d.del = 0')
            ->join('rbac_user_has_roles r','r.user_id=d.user_id','LEFT')
            ->where('r.role_id = 4')
            ->join('user_info u','u.id=d.user_id','LEFT')
            ->join('user_contact c','c.id=d.user_id','LEFT')
            ->join('status s','s.status_id=c.status','LEFT')
            ->field('u.name as name, c.phone as phone, c.address as address, 
                    s.status_name as status_name')
            ->select();
        if(count($stu_manager)!=0){
            // echo count($stu_manager);
            // foreach($stu_manager as $di){
            //     foreach($di as $ddi){
            //         echo $ddi, '\n';
            //     }
            // }
             $this->assign('stu_manager',$stu_manager);
        }else{
            
            $this->assign('stu_manager',[]);
        }



        $department_id = $user[0]["department"];
        $tea_manager = Db::table('user_manage_department d')
            ->where('d.department = ?',[$department_id])
            ->where('d.del = 0')
            ->join('rbac_user_has_roles r','r.user_id=d.user_id','LEFT')
            ->where('r.role_id = 2')
            ->join('user_info u','u.id=d.user_id','LEFT')
            ->join('user_contact c','c.id=d.user_id','LEFT')
            ->field('u.name as name, c.phone as phone')
            ->select();
        if(count($tea_manager)!=0){
            // echo count($stu_manager);
            // foreach($stu_manager as $di){
            //     foreach($di as $ddi){
            //         echo $ddi, '\n';
            //     }
            // }
             $this->assign('tea_manager',$tea_manager);
        }else{
            
            $this->assign('tea_manager',[]);
        }
        





        $this->assign("birth",$user[0]["birth"]);
        $this->assign("phone",$user_contact[0]["phone"]);
        $this->assign("email",$user_contact[0]["email"]);
        $this->assign("address",$user_contact[0]["address"]);
        $this->assign("parent",$user_contact[0]["parent"]);
        $this->assign("parent_phone",$user_contact[0]["parent_phone"]);
        $this->assign("vaccines",$user[0]["vaccines"]);

        $class_info = Db::query('select * from department where id=?',[$user[0]["department"]]);
        $this->assign("department",$class_info[0]["name"]);

        $major_info = Db::query('select * from major where id=?',[$user[0]["major"]]);
        $this->assign("major",$major_info[0]["name"]);

        $status_info = Db::query('select * from status where status_id=?',[$user_contact[0]["status"]]);
        $this->assign("status",$status_info[0]["status_name"]);

        $this->assign("hesuan",$user[0]["last_hesuan"]);

        // $sql = "INSERT INTO log (log_user_id0, log_user_id1)VALUES ($user_id, $user_id)";



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