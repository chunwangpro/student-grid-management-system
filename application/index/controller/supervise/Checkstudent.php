<?php
namespace app\index\controller\supervise;
use think\Db;
use think\Request;
use app\index\controller\Common;
use app\index\model\UserInfo;
use app\index\model\Department;
use app\index\model\Major;
use app\index\model\Status;
use app\index\model\RbacUserHasRoles;
use app\index\model\UserContact;
use app\index\model\UserVaccines;
use think\Session;

# 本页面为学苑班主任、学苑班委、联络员查看学苑中的学生信息页面

# 学苑名单页面中有查询组件和本苑学生管理员名单，教师可以管理哪个苑，从user_manage_department表中查询，取出教师管理的所有苑中学生的信息
# 进入页面需要在查询组件下显示本苑学生名单
# 所有的功能只针对本苑的学生，不显示教师和别的苑学生
# 可以搜索所有本苑的学生（包括学生和学生管理员）（模糊搜索、高级搜索）
# 对学生有放大镜功能、修改功能、无删除功能
# 在放大镜功能中，包含该学生的基本信息与联系信息，自己的联络人姓名，联络人的联系方式、邮箱
# 可以将某个学生的role修改为学生管理员（role_id = 4）（下拉菜单中只有学生和学生管理员两个选项）
# 可以修改学生的姓名拼音（其他页面无法修改，只能在这里修改用户的姓名拼音）
# 可以将某个学生的联络负责人修改为本学苑的一位学生管理员（下拉列表里有本苑所有role_id=4的学生管理员姓名）
# 有名单导出功能
# 有日志记录功能
  # 记录本人查看了联络名单页面
  # 记录本人查看了哪个学生的个人信息
  # 记录修改了哪个学生的哪个字段，修改了后的信息是什么
  # 记录用户搜索了什么信息
  # 记录是否导出了学生名单
# 本地与云开发的跳转链接统一格式：使用./ 或者 ../

class Checkstudent extends Common
{
    public function index()
    {
        // 教师和学生页面
        // 获取管理的学苑
        $user_id = $this->auth->getUserid();
        $systemUser = Db::table('rbac_user_has_roles u')
            ->field('u.user_id,u.role_id,r.id as id')
            ->join('rbac_role r','r.id=u.role_id','LEFT')
            ->where('u.user_id = ?',[$user_id])
            ->select();
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
        
        $department = Db::table('department d')
            ->field('d.name as department_name, d.id as department')
            ->select();
        $status = Db::query ('select * from status');
        $student = Db::table('user_info u')
            ->field('u.id, u.pinyin as pinyin, u.name as name, 
                     u.no as no, c.professor as professor, u.last_hesuan,
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
            ->where([
                'u.del' => 0
            ])
            ->where('j.role_id = 3 or j.role_id = 4')
            ->where($department_manage)
            ->order('no asce')
            ->paginate(30);
            // ->select();
        // 管理员下拉选项能选择所有学苑
        if (count($systemUser)){
            if ($systemUser[0]["id"] == 1){
                // 由于管理员管理的学苑太多，因此不显示管理的学苑
                $department2 = Db::table('department d')->where('d.id = -1')->select();
                $this->assign('department_id2',$department2);
                $this->assign('department_id',$department);
            }
            else{
                $this->assign('department_id2',$department_id);
                $this->assign('department_id',$department_id);
            }
        }
        $this->assign('student',$student);
        $this->assign('status',$status);
        return $this->fetch(); 
    }
	
	public function dosearch()
    {
        // 查询界面
        // 学苑信息、部门信息、状态信息与身份信息从数据库导出
        $user_id = $this->auth->getUserid();
        $systemUser = Db::table('rbac_user_has_roles u')
            ->field('u.user_id,u.role_id,r.id as id')
            ->join('rbac_role r','r.id=u.role_id','LEFT')
            ->where('u.user_id = ?',[$user_id])
            ->select();

        $department_id = Db::table('user_manage_department g')
            ->field('d.name as department_name, g.department as department')
            ->join('department d','d.id=g.department','LEFT')
            ->where('g.user_id = ?',[$user_id])
            ->where('del = 0')
            ->select();

        $department = Db::table('department d')
            ->field('d.name as department_name, d.id as department')
            ->select();
        
        // 管理员下拉选项能选择所有学苑
        if (count($systemUser)){
            if ($systemUser[0]["id"] == 1){
                $this->assign('department_id',$department);
            }
            else{
                $this->assign('department_id',$department_id);
            }
        }
    
        $student = Db::name('user_info')->where('del=0')->select();
        $major = Db::query ('select * from major');
        $status = Db::query ('select * from status');
        $this->assign('student',$student);
        $this->assign('major',$major);
        $this->assign('status',$status);
        return $this->fetch(); 
    }
    
    // public function index()
    // {
    //     // 管理系统用户界面
    //     // 学苑信息、部门信息、状态信息与身份信息从数据库导出
    //     // 不显示用户名单，只有查询组件，因此不设置分页显示
    //     $student = Db::name('user_info')->field('user_info.id as id')->where('del=0')->where('')->select();
    //     $major = Db::query ('select * from major');
    //     $department = Db::query ('select * from department');
    //     $status = Db::query ('select * from status');
    //     $role = Db::query ('select * from rbac_role');
    //     $this->assign('student',$student);
    //     $this->assign('major',$major);
    //     $this->assign('department',$department);
    //     $this->assign('status',$status);
    //     $this->assign('role',$role);
    //     return $this->fetch(); 
    // }
	
    public function search(){  // search.html 以学号搜索结果页面
        $request = Request::instance();
        $request = Request::instance();
        $modeRequested = $request->param("mode");
        $pinyinRequested = $request->param("pinyin");
        $nameRequested = $request->param("name");
        $numRequested = $request->param("num");
        $tutorRequested = $request->param("tutor");
        $majorRequested = $request->param("major");
        $departmentRequested = $request->param("department");
        $statusRequested = $request->param("status");
        $major_data = $request->param("major2/a");  
        $department_data = $request->param("department2/a");  
        $status_data = $request->param("status2/a");  
        $role_data = $request->param("role/a");  
        $covTime1Requested = $request->param("cov_time_1");
        $covTime2Requested = $request->param("cov_time_2");
        $quickRequested = $request->param("quicksearch");
        $managerRequested = $request->param("manager");
        //将查询数据写入session中
        $this->searchauth->setSearchSession($this->auth->getUserid(), $pinyinRequested, $nameRequested,$numRequested,$tutorRequested,
            $majorRequested,$departmentRequested,$statusRequested,$major_data, $department_data, $status_data, $role_data,
            $covTime1Requested, $covTime2Requested, $quickRequested, $managerRequested, $modeRequested);


        // 获取管理的学苑
        $user_id = $this->auth->getUserid();
        $department_id = Db::table('user_manage_department g')
            ->where('g.user_id = ?',[$user_id])
            ->where('del = 0')
            ->select();
        $department_manage = '';  
        if (count($department_id) != 0){
            $department_manage = 'u.department = '.$department_id[0]["department"];
        }
        for ($i=1; $i<count($department_id); $i++){
            $department_manage = $department_manage.' or '.'u.department = '.$department_id[$i]["department"];
        }






        
        // 模糊查询,模式为mode1
        // 模糊查询时，未输入数据将显示全体用户名单
        // 如果查询涉及的字段有空值时，该用户将不显示，因此需要在导入数据前进行数据完整性检查
        if ($modeRequested == "mode1") {
            $student = $this->dosearchmode1($department_manage, $pinyinRequested, $nameRequested, $numRequested, $tutorRequested, $majorRequested, $departmentRequested, $statusRequested); 
            $result_count = count(Db::table($student)->alias('stu')->select());
            $student=Db::table($student)->alias('stu')->paginate(30);
            if (count($student)){
                $this->assign('student',$student);
                $this->assign('result_count',$result_count);
                return $this->fetch();} 
            else{return $this-> error('系统中无此用户信息', '../index/supervise.checkstudent/index');} 
        }
        
        // 高级搜索,模式为mode2
        if ($modeRequested == "mode2") {
            
            $student = $this->dosearchmode2($department_manage, $major_data, $status_data, $department_data, $role_data, $covTime1Requested, $covTime2Requested, $pinyinRequested, $nameRequested, $numRequested, $tutorRequested);
            $result_count = count(Db::table($student)->alias('stu')->select());
            $student=Db::table($student)->alias('stu')->paginate(30);
            // 如果不涉及核酸信息的话
            if ($covTime2Requested == null){
                if (count($student)){
                    $this->assign('student',$student);
                    $this->assign('result_count',$result_count);
                    return $this->fetch();
                } 
                else {
                    return $this-> error('系统中无此用户信息', '../index/supervise.checkstudent/index');
                }  
            } else {//$covTime2Requested!=null  // 如果需要查询核酸信息// 查询的是该日期期间未做核酸的名单
                
                
                if (count($student)){
                    return view('advanced',['student'=>$student, 'result_count'=>$result_count]);
                } 
                else {
                    return $this-> error('系统中无此用户信息', '../index/supervise.checkstudent/index');
                }  
            }
        }
        
        if ($modeRequested == "mode3") {

            $student = $this->dosearchmode3($department_manage, $quickRequested, $managerRequested);
            $result_count = count(Db::table($student)->alias('stu')->select());
            $student=Db::table($student)->alias('stu')->paginate(30);

            if (count($student)){
                return view('search',['student'=>$student, 'result_count'=>$result_count]);
            } 
            else {
                return $this-> error('系统中无此信息', '../index/supervise.checkstudent/index');
            }   

        }
        return $this->fetch();
    }
    
    public function info(){  // 查看学生详细信息页面
        $request = Request::instance();
        $user = Db::table('user_info u')
            ->field('u.id, u.pinyin as pinyin, u.name as name, 
                     u.no as num, c.professor as professor, 
                     u.del as del, u.major as u_major, u.department as u_department, 
                     c.status as u_status, d.id as d_id, d.name as r_department,
                     s.status_id, s.status_name as r_status, m.id as m_id,
                     m.name as r_major, u.last_hesuan, j.role_id as j_role,
                     z.name as role_name, u.sex, c.professor_phone,
                     u.birth, c.parent, c.parent_phone,
                     c.phone, c.email, c.address')
            ->join('user_contact c','u.id=c.id','LEFT')
            ->join('department d','d.id=u.department','LEFT')
            ->join('status s','s.status_id=c.status','LEFT')
            ->join('major m','m.id=u.major','LEFT')
            ->join('rbac_user r','r.user_id=u.id','LEFT')
            ->join('rbac_user_has_roles j','j.user_id=u.id','LEFT')
            ->join('rbac_role z','z.id=j.role_id','LEFT')
            ->where('u.id = ?',[$request->param("id")])
            ->select();
        $this->assign('user',$user);
        return $this->fetch();
    }





    public function dosearchmode1($department_manage, $pinyin, $name, $num, $tutor, $major, $department, $status){
        $student = Db::table('user_info u')
            ->field('u.id, u.pinyin as pinyin, u.name as name, 
                     u.no as no, c.professor as professor, 
                     u.del as del, u.major as u_major, u.department as u_department, 
                     c.status as u_status, d.id as d_id, d.name as r_department,
                     s.status_id, s.status_name as r_status, m.id as m_id,
                     m.name as r_major, j.role_id as j_role, z.name as role_name,
                     u.sex as sex, c.phone as phone, c.email as email, c.address as address, u.birth as birth, 
                     u.vaccines as vaccines, c.parent, c.parent_phone, c.professor_phone,
                     u2.name as contact_name')
            ->join('user_contact c','u.id=c.id','LEFT')
            ->join('department d','d.id=u.department','LEFT')
            ->join('status s','s.status_id=c.status','LEFT')
            ->join('major m','m.id=u.major','LEFT')
            ->join('rbac_user_has_roles j','j.user_id=u.id','LEFT')
            ->join('rbac_role z','z.id=j.role_id','LEFT')
            ->join('user_info u2','c.contact_pid=u2.id','LEFT')
            ->where([
                'u.pinyin'  =>  ['like','%'.$pinyin.'%'],
                'u.name'  =>  ['like','%'.$name.'%'],
                'u.no' =>  ['like', $num.'%'],
                'professor' => ['like','%'.$tutor.'%'],
                'm.name' => ['like', '%'.$major.'%'],
                'd.name' => ['like', '%'.$department.'%'],
                's.status_name' => ['like', '%'.$status.'%'],
                'u.del' => 0
            ])
            ->where($department_manage)
            ->where('j.role_id = 3 or j.role_id = 4')
            ->order('no asce')
            ->buildSql();
            // ->select();
        return $student;
    }



    public function dosearchmode2($department_manage, $major_data, $status_data, $department_data, $role_data, $covTime1, $covTime2, $pinyin, $name, $num, $tutor){
        
        $student = null;
        
        $role_con = '';
        $major_con = '';
        $department_con = '';
        $status_con = '';
        if ($major_data[0] != null){
            $major_con = 'u.major = '.$major_data[0];
        }
        if ($status_data[0] != null){
            $status_con = 'c.status = '.$status_data[0];
        }
        if ($department_data[0] != null){
            $department_con = 'u.department = '.$department_data[0];
        }
        if ($role_data[0] != null){
            $role_con = 'j.role_id = '.$role_data[0];
        }
        for ($i=1; $i<count($major_data); $i++){
            $major_con = $major_con.' or '.'u.major = '.$major_data[$i];
        }
        for ($i=1; $i<count($department_data); $i++){
            $department_con = $department_con.' or '.'u.department = '.$department_data[$i];
        }
        for ($i=1; $i<count($status_data); $i++){
            $status_con = $status_con.' or '.'c.status = '.$status_data[$i];
        }
        for ($i=1; $i<count($role_data); $i++){
            $role_con = $role_con.' or '.'j.role_id = '.$role_data[$i];
        }
        
        // 如果不涉及核酸信息的话
        if ($covTime2 == null){
            $student = Db::table('user_info u')
            ->field('u.id, u.pinyin as pinyin, u.name as name, 
                 u.no as no, c.professor as professor, 
                 u.del as del, u.major as u_major, u.department as u_department, 
                 c.status as u_status, d.id as d_id, d.name as r_department,
                 s.status_id, s.status_name as r_status, m.id as m_id,
                 m.name as r_major, u.last_hesuan, j.role_id as j_role,
                 z.name as role_name,
                 u.sex as sex, c.phone as phone, c.email as email, c.address as address, u.birth as birth, 
                 u.vaccines as vaccines, c.parent, c.parent_phone, c.professor_phone,
                 u2.name as contact_name')
            ->join('user_contact c','u.id=c.id','LEFT')
            ->join('department d','d.id=u.department','LEFT')
            ->join('status s','s.status_id=c.status','LEFT')
            ->join('major m','m.id=u.major','LEFT')
            ->join('rbac_user r','r.user_id=u.id','LEFT')
            ->join('rbac_user_has_roles j','j.user_id=u.id','LEFT')
            ->join('rbac_role z','z.id=j.role_id','LEFT')
            ->join('user_info u2','c.contact_pid=u2.id','LEFT')
            ->where([
                'u.pinyin'  =>  ['like','%'.$pinyin.'%'],
                'u.name'  =>  ['like','%'.$name.'%'],
                'u.no' =>  ['like', $num.'%'],
                'professor' => ['like','%'.$tutor.'%'],
                // 'm.name' => ['like', '%'.$request->param("major").'%'],
                // 'd.name' => ['like', '%'.$request->param("department").'%'],
                // 's.status_name' => ['like', '%'.$request->param("status").'%'],
                'u.del' => 0
            ])
            ->where($status_con)
            ->where($major_con)
            ->where($department_con)
            ->where($role_con)
            ->where($department_manage)
            ->where('j.role_id = 3 or j.role_id = 4')
            ->order('no asce')
            ->buildSql();
            // ->select();

        }
        // 如果需要查询核酸信息
        // 查询的是该日期期间未做核酸的名单
        if ($covTime2 != null){
            $student = Db::table('user_info u')
            ->field('u.id as u_id, u.pinyin as pinyin, u.name as name, 
                 u.no as no, c.professor as professor, 
                 u.del as del, u.major as u_major, u.department as u_department, 
                 c.status as u_status, d.id as d_id, d.name as r_department,
                 s.status_id, s.status_name as r_status, m.id as m_id,
                 m.name as r_major, h.cov_time, h.cov_location,
                 u.last_hesuan, j.role_id as j_role,
                 z.name as role_name,
                 u.sex as sex, c.phone as phone, c.email as email, c.address as address, u.birth as birth, 
                 u.vaccines as vaccines, c.parent, c.parent_phone, c.professor_phone,
                 u2.name as contact_name')
            ->join('user_contact c','u.id=c.id','LEFT')
            ->join('department d','d.id=u.department','LEFT')
            ->join('status s','s.status_id=c.status','LEFT')
            ->join('major m','m.id=u.major','LEFT')
            ->join('rbac_user r','r.user_id=u.id','LEFT')
            ->join('rbac_user_has_roles j','j.user_id=u.id','LEFT')
            ->join('rbac_role z','z.id=j.role_id','LEFT')
            ->join('hesuan h','h.user_id=u.id','LEFT')
            ->join('user_info u2','c.contact_pid=u2.id','LEFT')
            ->where([
                'u.pinyin'  =>  ['like','%'.$pinyin.'%'],
                'u.name'  =>  ['like','%'.$name.'%'],
                'u.no' =>  ['like', $num.'%'],
                'professor' => ['like','%'.$tutor.'%'],
                // 'm.name' => ['like', '%'.$request->param("major").'%'],
                // 'd.name' => ['like', '%'.$request->param("department").'%'],
                // 's.status_name' => ['like', '%'.$request->param("status").'%'],
                'u.del' => 0
            ])
            ->where($status_con)
            ->where($major_con)
            ->where($department_con)
            ->where($role_con)
            ->where($department_manage)
            ->where('j.role_id = 3 or j.role_id = 4')
            ->whereTime('cov_time', 'not between', [$covTime2, $covTime1])
            ->group('u_id') // 去除某一个同学的多个重复数据
            ->order('no asce')
            ->buildSql();
            // ->select();
            
        }

        return $student;
    }





    public function dosearchmode3($department_manage, $quick, $manager){

        $student = null;
        if ($quick == null) {
            $student = Db::table('user_manage_department g')
            ->field('u.id, u.name as name, u.no as no,
                 u.del as del, u.major as u_major, u.department as u_department, 
                 d.id as d_id, d.name as r_department,
                 m.id as m_id, m.name as r_major,
                 g.department as m_department,
                 c.professor as professor,
                 s.status_name as r_status,
                 z.name as role_name,
                 u.sex as sex, c.phone as phone, c.email as email, c.address as address, u.birth as birth, 
                 u.vaccines as vaccines, c.parent, c.parent_phone, c.professor_phone,
                 u2.name as contact_name')
            ->join('user_info u','u.id=g.user_id','LEFT')
            ->join('department d','d.id=u.department','LEFT')
            ->join('major m','m.id=u.major','LEFT')
            ->join('user_contact c','u.id=c.id','LEFT')
            ->join('status s','s.status_id=c.status','LEFT')
            ->join('rbac_user_has_roles j','j.user_id=u.id','LEFT')
            ->join('rbac_role z','z.id=j.role_id','LEFT')
            ->join('user_info u2','c.contact_pid=u2.id','LEFT')
            ->where('g.department = ?',[$manager])
            ->where('j.role_id = 3 or j.role_id = 4')
            ->where('g.del = 0')
            ->order('no asce')
            ->buildSql();
            // ->select();
        }
        else {
            // 个人联系信息未更新的学生名单
            if ($quick == 1){
                $student = Db::table('user_info u')
                ->field('u.id, u.pinyin as pinyin, u.name as name, 
                     u.no as no, c.professor as professor, 
                     u.del as del, u.major as u_major, u.department as u_department, 
                     c.status as u_status, d.id as d_id, d.name as r_department,
                     s.status_id, s.status_name as r_status, m.id as m_id,
                     m.name as r_major, u.last_hesuan, j.role_id as j_role,
                     z.name as role_name, c.email as email,
                     u2.name as contact_name')
                ->join('user_contact c','u.id=c.id','LEFT')
                ->join('department d','d.id=u.department','LEFT')
                ->join('status s','s.status_id=c.status','LEFT')
                ->join('major m','m.id=u.major','LEFT')
                ->join('rbac_user r','r.user_id=u.id','LEFT')
                ->join('rbac_user_has_roles j','j.user_id=u.id','LEFT')
                ->join('rbac_role z','z.id=j.role_id','LEFT')
                ->join('user_info u2','c.contact_pid=u2.id','LEFT')
                ->where('c.phone|c.email|c.status|c.address|c.professor|c.professor_phone|c.parent|c.parent_phone|c.contact_pid','like','') // 查询为空的值，默认user_contact表是与user_info一一对应的
                ->where($department_manage)
                ->where('u.department = ?',[$manager])
                ->where('j.role_id = 3 or j.role_id = 4')
                ->where('u.del = 0')
                ->order('no asce')
                ->buildSql();
                // ->select();
            }
            // 当天未做核酸的用户名单
            if ($quick == 2){
                // 获取当前时间
                $setCurrentTime = time(); 
                $timevalue = date('Y-m-d',$setCurrentTime);
                // 查询
                $student = Db::table('user_info u')
                ->field('u.id, u.pinyin as pinyin, u.name as name, 
                     u.no as no, c.professor as professor, 
                     u.del as del, u.major as u_major, u.department as u_department, 
                     c.status as u_status, d.id as d_id, d.name as r_department,
                     s.status_id, s.status_name as r_status, m.id as m_id,
                     m.name as r_major, u.last_hesuan, j.role_id as j_role,
                     z.name as role_name, c.email as email,
                     u2.name as contact_name')
                ->join('user_contact c','u.id=c.id','LEFT')
                ->join('department d','d.id=u.department','LEFT')
                ->join('status s','s.status_id=c.status','LEFT')
                ->join('major m','m.id=u.major','LEFT')
                ->join('rbac_user r','r.user_id=u.id','LEFT')
                ->join('rbac_user_has_roles j','j.user_id=u.id','LEFT')
                ->join('rbac_role z','z.id=j.role_id','LEFT')
                ->join('user_info u2','c.contact_pid=u2.id','LEFT')
                ->where('u.del = 0')
                ->where($department_manage)
                ->where('u.department = ?',[$manager])
                ->where('j.role_id = 3 or j.role_id = 4')
                ->whereTime('u.last_hesuan', 'not between', [$timevalue, $timevalue])
                ->order('no asce')
                ->buildSql();
                // ->select();
            }
        }
        return $student;
    }




    public function export(){
        $currentUser = $this->auth->getUserid();
        $minuteInterval = 15;
  
        $request = Request::instance();
      //   var_dump($request->param("id"));
  
        if($currentUser!=$this->searchauth->getCaller()){
            return $this-> error('您无此权限');
        }
        if((time()-$this->searchauth->getTimestamp())/60>=$minuteInterval){
            return $this->error('查询结果已过期，请重新查询');
        }
  
        $this->doExport($request->param("exportmodeid"));
    }



    protected function doExport($exportmodeid){
        $modeRequested = $this->searchauth->getSearchMode();
        $pinyinRequested = $this->searchauth->getPinyin();
        $nameRequested = $this->searchauth->getName();
        $numRequested = $this->searchauth->getNum();
        $tutorRequested = $this->searchauth->getTutor();
        $majorRequested = $this->searchauth->getMajor();
        $departmentRequested = $this->searchauth->getDepartment();
        $statusRequested = $this->searchauth->getStatus();
        $major_data = $this->searchauth->getMajorData();
        $department_data = $this->searchauth->getDepartmentData(); 
        $status_data = $this->searchauth->getStatusData();
        $role_data = $this->searchauth->getRoleData();
        $covTime1Requested = $this->searchauth->getCovTime1();
        $covTime2Requested = $this->searchauth->getCovTime2();
        $quickRequested = $this->searchauth->getQuick();
        $managerRequested = $this->searchauth->getManager();
        // echo $modeRequested;
        // echo $nameRequested;

        // 获取管理的学苑
        $user_id = $this->auth->getUserid();
        $department_id = Db::table('user_manage_department g')
            ->where('g.user_id = ?',[$user_id])
            ->where('del = 0')
            ->select();
        $department_manage = '';  
        if (count($department_id) != 0){
            $department_manage = 'u.department = '.$department_id[0]["department"];
        }
        for ($i=1; $i<count($department_id); $i++){
            $department_manage = $department_manage.' or '.'u.department = '.$department_id[$i]["department"];
        }



        $xlsData = null;
        if ($modeRequested == "mode1") {
            $xlsSql = $this->dosearchmode1($department_manage, $pinyinRequested, $nameRequested, $numRequested, $tutorRequested, $majorRequested, $departmentRequested, $statusRequested);
            $xlsData = Db::table($xlsSql)->alias('xSql')->select();
        }else if ($modeRequested == "mode2") { 
            $xlsSql = $this->dosearchmode2($department_manage, $major_data, $status_data, $department_data, $role_data, $covTime1Requested, $covTime2Requested, $pinyinRequested, $nameRequested, $numRequested, $tutorRequested);
            $xlsData = Db::table($xlsSql)->alias('xSql')->select();
        } else if ($modeRequested == "mode3") {
            $xlsSql = $this->dosearchmode3($department_manage, $quickRequested, $managerRequested);
            $xlsData = Db::table($xlsSql)->alias('xSql')->select();
        }

        

        //这里引入PHPExcel文件
        vendor("PHPExcel");
        vendor("PHPExcel.Writer.Excel5");
        vendor("PHPExcel.Writer.Excel2007");
        vendor("PHPExcel.IOFactory");
        $objExcel = new \PHPExcel();
        $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
        $objActSheet = $objExcel->getActiveSheet();




        //设置每一列的信息（分情况，每种查询模式&每种导出模式）
        if($exportmodeid==1){//详细信息
            //设置Excel表格各列标题
            $letter = explode(',', "A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P");
            $arrHeader = array('序号', '学号', '姓名', '性别', '所属院系', '所属学苑','联系电话', '邮箱', '当前状态','校内住址', '出生日期','紧急联系人', '紧急联系人电话','疫苗接种','导师姓名','导师联系电话');
            $lenth = count($arrHeader);
            for ($i = 0; $i < $lenth; $i++) {
                $objActSheet->setCellValue("$letter[$i]1", "$arrHeader[$i]");
            };
    
            $a = 0; //序号
            //填充相应的数据到Excel表格中
            foreach ($xlsData as $k => $v) {
                $k += 2; //这个k代表$xlsData的索引,后面需要用它来表示应该存到Excel的第几行
                $a += 1;
                $objActSheet->setCellValue('A' . $k, $a);
                $objActSheet->setCellValue('B' . $k, $v['no']);//学号
                $objActSheet->setCellValue('C' . $k, $v['name']);//姓名
                $objActSheet->setCellValue('D' . $k, $v['sex']);
                $objActSheet->setCellValue('E' . $k, $v['r_major']);//所属院系专业
                $objActSheet->setCellValue('F' . $k, $v['r_department']);//所属学苑
                $objActSheet->setCellValue('G' . $k, $v['phone']);
                $objActSheet->setCellValue('H' . $k, $v['email']);
                $objActSheet->setCellValue('I' . $k, $v['r_status']);//状态
                $objActSheet->setCellValue('J' . $k, $v['address']);
                $objActSheet->setCellValue('K' . $k, $v['birth']);
                $objActSheet->setCellValue('L' . $k, $v['parent']);
                $objActSheet->setCellValue('M' . $k, $v['parent_phone']);
                $objActSheet->setCellValue('N' . $k, $v['vaccines']);
                $objActSheet->setCellValue('O' . $k, $v['professor']);//导师
                $objActSheet->setCellValue('P' . $k, $v['professor_phone']);
            
    
                $objActSheet->getColumnDimension('A')->setWidth(5);
                $objActSheet->getColumnDimension('B')->setWidth(15);
                $objActSheet->getColumnDimension('C')->setWidth(15);
                $objActSheet->getColumnDimension('D')->setWidth(4);
                $objActSheet->getColumnDimension('E')->setWidth(25);
                $objActSheet->getColumnDimension('F')->setWidth(14);
                $objActSheet->getColumnDimension('G')->setWidth(15);
                $objActSheet->getColumnDimension('H')->setWidth(15);
                $objActSheet->getColumnDimension('I')->setWidth(18);
                $objActSheet->getColumnDimension('J')->setWidth(20);
                $objActSheet->getColumnDimension('K')->setWidth(12);
                $objActSheet->getColumnDimension('L')->setWidth(15);
                $objActSheet->getColumnDimension('M')->setWidth(15);
                $objActSheet->getColumnDimension('N')->setWidth(4);
                $objActSheet->getColumnDimension('O')->setWidth(15);
                $objActSheet->getColumnDimension('P')->setWidth(15);
            } 


        } else {//简洁模式

            //设置excel表格各列标题
            $letter = explode(',', "A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P");
            $arrHeader = array('序号','学号','姓名','所属学苑');
            $lenth = count($arrHeader);
            for ($i = 0; $i < $lenth; $i++) {
                $objActSheet->setCellValue("$letter[$i]1", "$arrHeader[$i]");
            };
            $a = 0; //序号
            //填充数据到表
            foreach ($xlsData as $k => $v) {
                $k += 2; //这个k代表$xlsData的索引,后面需要用它来表示应该存到Excel的第几行
                $a += 1;
                $objActSheet->setCellValue('A' . $k, $a);
                $objActSheet->setCellValue('B' . $k, $v['no']);//学号
                $objActSheet->setCellValue('C' . $k, $v['name']);//姓名
                $objActSheet->setCellValue('D' . $k, $v['r_department']);//所属学苑
            }
            //设置表格各列的宽度
            $objActSheet->getColumnDimension('A')->setWidth(5);
            $objActSheet->getColumnDimension('B')->setWidth(15);
            $objActSheet->getColumnDimension('C')->setWidth(15);
            $objActSheet->getColumnDimension('D')->setWidth(15);

        }








        //设置输出文件信息
        date_default_timezone_set('PRC');
        $today = date('yymd_His');
        $outfile = "网格系统查询结果表_" . $today . ".xlsx";
        ob_end_clean();
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="' . $outfile . '"');
        header("Content-Transfer-Encoding: binary");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $objWriter->save('php://output');    //这里直接导出文件
    }

    // 管理员修改学生信息页面
    public function changeinfo(){
        $request = Request::instance();
        $user_id = $request->param("id");
        $this->assign("id",$user_id);
        // echo $user_id;
        $user = Db::query("select * from user_info where id=? and del=?",[$user_id,0]);
        $this->assign("no",$user[0]["no"]);
        $this->assign("name",$user[0]["name"]);
        $this->assign("pinyin",$this->check($user[0]["pinyin"]));
        $this->assign("sex",$user[0]["sex"]);
        $this->assign("birth",$user[0]["birth"]);
        $this->assign("vaccines",$user[0]["vaccines"]);
        $this->assign("last_hesuan",$user[0]["last_hesuan"]);

        $user_contact = Db::query("select * from user_contact where id=?",[$user_id]);
        $this->assign("professor",$this->check($user_contact[0]["professor"]));
        $this->assign("professor_phone",$this->check($user_contact[0]["professor_phone"]));
        $this->assign("phone",$this->check($user_contact[0]["phone"]));
        $this->assign("email",$this->check($user_contact[0]["email"]));
        $this->assign("address",$this->check($user_contact[0]["address"]));
        $this->assign("parent",$this->check($user_contact[0]["parent"]));
        $this->assign("parent_phone",$this->check($user_contact[0]["parent_phone"]));

        if($user_contact[0]["contact_pid"] == null){
            $this->assign("contactpid", '?');
        }else{
            $pid = userinfo::get($user_contact[0]["contact_pid"]);
            $contactor = usercontact::get($pid["id"]);
            $this->assign("contactpid", $pid["name"].'  '.$contactor["phone"]);
        }

        $class_info = Db::query('select * from department where id=?',[$user[0]["department"]]);
        $this->assign("department",$class_info[0]["name"]);

        $major_info = Db::query('select * from major where id=?',[$user[0]["major"]]);
        $this->assign("major",$major_info[0]["name"]);

        $status_info = Db::query('select * from status where status_id=?',[$user_contact[0]["status"]]);
        $this->assign("status",$status_info[0]["status_name"]);

        $role = rbacuserhasroles::get(['user_id' => $user_id]);
        if ($role->role_id == 1 or $role->role_id == 2){
            return view('changeteacher');
        }
        else{
            return view('changeinfo');
        }
        
        //return $this->fetch();
    }
    
    public function check($attr){
        if ($attr==null){
            return '?';
        }else{
            return $attr;
        }
    }

    public function update($attr, $old, $request){
        if($request->param($attr) == null){
            return $old;
        }else{
            return $request->param($attr);
        }
    }

    // 管理员提交修改学生信息功能
    public function updateinfo(){
        $request = Request::instance();
        // echo $request->param("id");
        $user = userinfo::get($request->param("id"));
        // 实现对单个字段的更新，其他字段留空时，不会更新这些留空的字段
        $no = $this->update("newno", $user->no, $request);
        $name = $this->update("newname", $user->name, $request);
        $pinyin = $this->update("newpinyin", $user->pinyin, $request);
        $sex = $this->update("newsex", $user->sex, $request);
        $sex = $this->update("newsex", $user->sex, $request);
        $birth = $this->update("newbirth", $user->birth, $request);
        $vaccines = $this->update("newvaccines", $user->vaccines, $request);
        $last_hesuan = $this->update("newlasthesuan", $user->last_hesuan, $request);
        //需要处理的字段
        if($request->param("newdepartment") == null){
            $department = $user->department;
        }else{
            $depart = department::get(['name' => $request->param("newdepartment")]);
            if(!$depart){
                return $this->error('系统中无此学苑，请检查输入');
            }
            $department = $depart->id;
        }
        if($request->param("newmajor") == null){
            $major = $user->major;
        }else{
            $class = major::get(['name' => $request->param("newmajor")]);
            if(!$class){
                return $this->error('系统中无此院系，请检查输入');
            }
            $major = $class->id;
        }

        $user_contact = usercontact::get($request->param("id"));
        // 实现对单个字段的更新，其他字段留空时，不会更新这些留空的字段
        $professor_phone = $this->update("newprofessorphone", $user_contact->professor_phone, $request);
        $professor = $this->update("newprofessor", $user_contact->professor, $request);
        $phone = $this->update("newphone", $user_contact->phone, $request);
        $email = $this->update("newemail", $user_contact->email, $request);
        $address = $this->update("newaddress", $user_contact->address, $request);
        $parent = $this->update("newparent", $user_contact->parent, $request);
        $parent_phone = $this->update("newparentphone", $user_contact->parent_phone, $request);
        //需要处理的字段
        if($request->param("newstatus") == null){
            $status = $user_contact->status;
        }else{
            $mood = status::get(['status_name' => $request->param("newstatus")]);
            if(!$mood){
                return $this->error('系统中无此状态，请检查输入');
            }
            $status = $mood->status_id;
        }
        if($request->param("newcontactpid") == null){
            $contactpid = $user_contact->contact_pid;
        }else{
            $contactor = userinfo::get(['no' => $request->param("newcontactpid"), 'del' => 0]);
            if(!$contactor){
                return $this->error('系统中无此用户，请正确输入学苑联络人学号');
            }
            if($contactor->department!=$department){
                return $this->error('请输入与该学生相同学苑的联络人学号');
            }
            $role = rbacuserhasroles::get(['user_id' => $contactor->id]);
            if($role->role_id != 4){
                return $this->error('该用户不是学生管理员，请正确输入学苑联络人学号');
            }

            $contactpid = $contactor->id;
        }

        // 插入info表格
        $user->data([
            'no' =>  $no,
            'name' =>  $name,
            'pinyin' =>  $pinyin,
            'sex' =>  $sex,
            'birth' =>  $birth,
            'vaccines' =>  $vaccines,
            'last_hesuan' =>  $last_hesuan,
            'major' =>  $major,
            'department' =>  $department,
        ]);
        $user->save();
        // 插入user contact 表格
        $user_contact->data([
            'professor' =>  $professor,
            'professor_phone' =>  $professor_phone,
            'phone' =>  $phone,
            'email' =>  $email,
            'address' =>  $address,
            'parent' =>  $parent,
            'parent_phone' =>  $parent_phone,
            'status' =>  $status,
            'contact_pid' => $contactpid
        ]);
        $user_contact->save();

        return $this-> success('修改学生信息成功');
    }
}