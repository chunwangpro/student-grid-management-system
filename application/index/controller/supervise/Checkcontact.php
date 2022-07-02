<?php
namespace app\index\controller\supervise;
use app\index\model\RbacUser;
use app\index\model\UserContact;
use think\Db;
use think\Request;
use app\index\controller\Common;
use app\index\model\UserInfo;


# 本页面为学生管理员查看自己负责联络的学生信息
# 联络名单页面中无查询组件，有表单，显示苑委自己负责联络的学生（role_id=3）名单
# 无搜索功能、无导出功能
# 对学生有放大镜功能、无修改功能、无删除功能
# 无导出功能
# 有日志记录功能，记录本人查看了联络名单页面，记录本人查看了哪个学生的个人信息
# 本地与云开发的跳转链接统一格式：使用./ 或者 ../

class Checkcontact extends Common
{
    public function index()
    {
        $user = rbacuser::get($this->auth->getUserid());
        $contact = $user->user_id;
        $student = Db::table('user_info u')
            ->field('u.id,u.name as name,u.no as no,z.name as role_name,m.name as r_major,d.name as r_department, 
                      c.professor as professor, s.status_name, c.phone, c.address')
            ->join('user_contact c','u.id=c.id','LEFT')
            ->join('status s','s.status_id=c.status','LEFT')
            ->join('department d','d.id=u.department','LEFT')
            ->join('major m','m.id=u.major','LEFT')
            ->join('rbac_user r','r.user_id=u.id','LEFT')
            ->join('rbac_user_has_roles j','j.user_id=u.id','LEFT')
            ->join('rbac_role z','z.id=j.role_id','LEFT')
            ->where('c.contact_pid',($contact))
            ->select();

        $this->assign('list',$student);

        return $this->fetch();
    }

    public function info()

    {   $request = Request::instance();

        $userinfo = UserInfo::get($request->param("id"));
        $this->assign('name',$userinfo->name);
        $this->assign('no',$userinfo->no);
        $this->assign('sex',$userinfo->sex);
        $this->assign('birth',$userinfo->birth);
        $this->assign('vaccines',$userinfo->vaccines);
        $this->assign('last_hesuan',$userinfo->last_hesuan);

        $usercontact = UserContact::get($request->param("id"));
        $this->assign('phone',$usercontact->phone);
        $this->assign('professor',$usercontact->professor);
        $this->assign('professor_phone',$usercontact->professor_phone);
        $this->assign('email',$usercontact->email);
        $this->assign('address',$usercontact->address);
        $this->assign('parent',$usercontact->parent);
        $this->assign('parent_phone',$usercontact->parent_phone);

        $class_info = Db::query('select * from department where id=?',[$userinfo->department]);
        $this->assign("class",$class_info[0]["name"]);

        $status_info = Db::query('select * from status where status_id=?',[$usercontact->status]);
        $this->assign("status",$status_info[0]["status_name"]);

        $major_info = Db::query('select * from major where id=?',[$userinfo->major]);
        $this->assign("major",$major_info[0]["name"]);

        $teacher = Db::table('user_manage_department t')
            ->field('u.name as tea_name,c.phone as tea_phone')
            ->join('user_info u','u.id=t.user_id','LEFT')
            ->join('user_contact c','t.user_id=c.id','LEFT')
            ->join('rbac_user_has_roles j','j.user_id=t.user_id','LEFT')
            ->where('j.role_id = 2 ')
            ->where('t.department = ?',[$userinfo->department])
            ->where('t.del = 0')
            ->select();

        $this->assign('teacher',$teacher);


        return $this->fetch();

    }
    



        

}