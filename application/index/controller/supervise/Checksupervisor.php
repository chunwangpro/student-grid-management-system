<?php
namespace app\index\controller\supervise;
use think\Db;
use think\Request;
use app\index\controller\Common;
use app\index\model\UserInfo;
use app\index\model\RbacUserHasRoles;
use app\index\model\UserContact;
use think\Session;

# 本页面为教师管理员查看自己苑中的学生管理员及其对应负责联络的学生名单

# 院系名单页面中无查询组件，有表单，显示本苑中所有管理员（role_id=2 or 4）名单，编号、姓名、学号、学苑、当前状态
# 有放大镜功能、无修改功能、无删除功能（这里的放大镜显示该苑委的学号姓名、联系方式、手机、邮箱等联系信息，同时给出其负责联络的学生的学号和姓名，联系方式，无法查看个人基本信息）
# 无名单导出功能
# 有日志记录功能，记录本人查看了联络名单页面，记录本人查看了哪个学生的个人信息
# 本地与云开发的跳转链接统一格式：使用./ 或者 ../

class Checksupervisor extends Common
{
    public function index()
    {
        $user_id = $this->auth->getUserid();
        $department_id = Db::table('user_manage_department g')
            ->field('d.name as department_name, g.department as department')
            ->join('department d','d.id=g.department','LEFT')
            ->where('g.user_id = ?',[$user_id])
            ->where('del = 0')
            ->select();
        $department_manage = '';  
        if (count($department_id) != 0){
            $department_manage = 'u.department = '.$department_id[0]["department"];
            for ($i=1; $i<count($department_id); $i++){
            $department_manage = $department_manage.' or '.'u.department = '.$department_id[$i]["department"];
            }
        }
        $student = Db::table('user_info u')
            ->field('u.id, u.pinyin as pinyin, u.name as name, 
                     u.no as no, c.professor as professor, 
                     u.del as del, u.major as u_major, u.department as u_department, 
                     c.status as u_status, d.id as d_id, d.name as r_department,
                     s.status_id, s.status_name as r_status, m.id as m_id,
                     m.name as r_major, j.role_id as j_role, z.name as role_name,
                     u.sex as sex, c.phone as phone, c.email as email, 
                     c.address as address, u.birth as birth, 
                     u.vaccines as vaccines, c.parent, c.parent_phone, c.professor_phone,
                     c.contact_pid, u2.name as contact_name,
                     u2.pinyin as py, u2.id as id2, u2.name as name2,
                     u2.no as no2, u2.del as del2, u2.sex as sex2,
                     u2.vaccines as vaccines2')
            ->join('user_contact c','u.id=c.id','LEFT')
            ->join('department d','d.id=u.department','LEFT')
            ->join('status s','s.status_id=c.status','LEFT')
            ->join('major m','m.id=u.major','LEFT')
            ->join('rbac_user_has_roles j','j.user_id=u.id','LEFT')
            ->join('rbac_role z','z.id=j.role_id','LEFT')
            ->join('user_info u2','c.contact_pid=u2.id','LEFT')
            ->where('j.role_id = 2 or j.role_id = 4')
            ->where($department_manage)
            ->where('u.del = 0')
            //->where('u.id = ?',[$user_id])
            ->order('no asce')
            ->paginate(30);
            
        $this->assign('list',$student);
        return $this->fetch();
    }
    public function info()
    {
        //拿到用户提交的数据
        $request = Request::instance();
        // 获取当前域名
        //var_dump($request->param()) ;
        //到数据库中取出相应id所对应的数据
        
        //echo $request->param("id");
        $user = UserInfo::get($request->param("id"));
        //echo $user->name;
        $this->assign('name',$user->name);
        $this->assign('no',$user->no);
        $user_id = $user["id"];
        
        $user = UserContact::get($request->param("id"));
        $this->assign('phone',$user->phone);
        $this->assign('email',$user->email);
        $this->assign('address',$user->address);
        $this->assign('parent',$user->parent);
        $this->assign('parent_phone',$user->parent_phone);

        $role = rbacuserhasroles::get(['user_id' => $user_id]);
        if($role->role_id == 1 or $role->role_id ==2){
            return view("teacher");
        }

        //获取联络的学生名单
        $student = Db::table('user_info u')
            ->field('u.id, u.pinyin as pinyin, u.name as name, 
                     u.no as no, c.professor as professor, 
                     u.del as del, u.major as u_major, u.department as u_department, 
                     c.status as u_status, d.id as d_id, d.name as r_department,
                     s.status_id, s.status_name as r_status, m.id as m_id,
                     m.name as r_major, j.role_id as j_role, z.name as role_name,
                     u.sex as sex, c.phone as phone, c.email as email, 
                     c.address as address, u.birth as birth, 
                     u.vaccines as vaccines, c.parent, c.parent_phone, c.professor_phone,
                     c.contact_pid, u2.name as contact_name,
                     u2.pinyin as py, u2.id as id2, u2.name as name2,
                     u2.no as no2, u2.del as del2, u2.sex as sex2,
                     u2.vaccines as vaccines2')
            ->join('user_contact c','u.id=c.id','LEFT')
            ->join('department d','d.id=u.department','LEFT')
            ->join('status s','s.status_id=c.status','LEFT')
            ->join('major m','m.id=u.major','LEFT')
            ->join('rbac_user_has_roles j','j.user_id=u.id','LEFT')
            ->join('rbac_role z','z.id=j.role_id','LEFT')
            ->join('user_info u2','c.id=u2.id','LEFT')
            ->where('u.del = 0')
            ->where('u.id <> ?',[$request->param("id")])
            ->where('c.contact_pid = ?',[$request->param("id")])
            ->paginate(30);
            
        $this->assign('list',$student);
        return $this->fetch();
    }

}