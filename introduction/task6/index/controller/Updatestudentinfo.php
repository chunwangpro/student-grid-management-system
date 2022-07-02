<?php
namespace app\index\controller\manage;
use think\Db;
use think\Request;
use app\index\controller\Common;
use app\index\model\RbacUser;
use app\index\model\UserInfo;
use app\index\model\RbacUserHasRoles;

class Updatestudentinfo extends Common
{  
    // 控制初始界面
    public function index()
    {
        // 以下查询语句是希望在搜索首页分页显示所有的学生或老师信息，但出于美观性考虑，界面未显示学生信息的表格
        $student = Db::name('user_info')->where('role_id=3 or role_id=2 and del=0')->order('no asce')->paginate(2);
        $major = Db::query ('select * from major');
        $department = Db::query ('select * from department');
        $status = Db::query ('select * from status');
        $this->assign('student',$student);
        $this->assign('major',$major);
        $this->assign('department',$department);
        $this->assign('status',$status);
        return $this->fetch();
        // 选取所有学生，按照学号排序，每页显示两名学生
        // $student = Db::name('user_info')->where('role_id=3 and del=0')->order('no asce')->paginate(2);
	    // 渲染模板输出，未使用
	    // return view('',['student'=>$student]);
    }
	
    public function search(){  // search.html 以学号搜索结果页面
        // 获取POST表单
        $request = Request::instance();
        // 以下是查询所有数据库信息，方便导出功能的同学使用
        // $student = Db::query ('select * from user_info where role_id=3 and del=0');
        $department = Db::query ('select * from department');
        $major = Db::query ('select * from major');
        $status = Db::query ('select * from status');
        $this->assign('major',$major);
        $this->assign('department',$department);
        $this->assign('status',$status);
        // $this->assign('student',$student);
        
        // 模糊查询
        if ($request->param("mode") == "mode1") {
                
            $student = Db::table('user_info u')
            ->field('u.id, u.pinyin as pinyin, u.name as name, 
                     u.no as no, u.professor as professor, u.role_id as role_id,
                     u.del as del, u.major as u_major, u.department as u_department, 
                     u.status as u_status, d.id as d_id, d.name as r_department,
                     s.status_id, s.status_name as r_status, m.id as m_id,
                     m.name as r_major') // 选择查询字段范围
            ->join('department d','d.id=u.department') // 联表查询学苑表
            ->join('status s','s.status_id=u.status') // 联表查询状态表
            ->join('major m','m.id=u.major') // 联表查询部门、专业表
            ->where([
                'pinyin'  =>  ['like','%'.$request->param("pinyin").'%'], // 姓名拼音
                'user_info.name'  =>  ['like','%'.$request->param("name").'%'], // 姓名
                'no' =>  ['like', $request->param("num").'%'], // 学号、工号
                'professor' => ['like','%'.$request->param("tutor").'%'], // 导师姓名
                'm.name' => ['like', '%'.$request->param("major").'%'], // 部门、专业
                'd.name' => ['like', '%'.$request->param("department").'%'], // 学苑
                's.status_name' => ['like', '%'.$request->param("status").'%'], // 状态
                'del' => 0 // 未删除数据
            ])
            ->where('role_id',['=',3],['=',2],'or') // 身份为教师或学生
            ->order('no asce') // 按照学号、工号顺序排列
            ->select();
            // ->paginate(10); // 分页，暂时不使用
            
            if (count($student)){ // 如果查询到信息
                $this->assign('student',$student);
                return $this->fetch();} 
            else{return $this-> error('系统中无此用户信息', '/index/manage.Updatestudentinfo/index');} // 未找到信息，则返回查询界面
        }
        
        // 高级搜索
        if ($request->param("mode") == "mode2") {
            
            // 由于是多选操作，提前生成查询学苑、部门、状态和身份的SQL语句
            $major_data = $request->param("major2/a");  
            $department_data = $request->param("department2/a");  
            $status_data = $request->param("status2/a");  
            $role_data = $request->param("role/a");  
            $role_con = '';
            $major_con = '';
            $department_con = '';
            $status_con = '';
            if ($major_data[0] != null){
                $major_con = 'u.major = '.$major_data[0];
            }
            if ($status_data[0] != null){
                $status_con = 'u.status = '.$status_data[0];
            }
            if ($department_data[0] != null){
                $department_con = 'u.department = '.$department_data[0];
            }
            if ($role_data[0] != null){
                $role_con = 'u.role_id = '.$role_data[0];
            }
            for ($i=1; $i<count($major_data); $i++){
                $major_con = $major_con.' or '.'u.major = '.$major_data[$i];
            }
            for ($i=1; $i<count($department_data); $i++){
                $department_con = $department_con.' or '.'u.department = '.$department_data[$i];
            }
            for ($i=1; $i<count($status_data); $i++){
                $status_con = $status_con.' or '.'u.status = '.$status_data[$i];
            }
            for ($i=1; $i<count($role_data); $i++){
                $role_con = $role_con.' or '.'u.role_id = '.$role_data[$i];
            }
            
            // 如果不查询核酸信息，即时间为空
            if ($request->param("cov_time_2") == null){
                
            $student = Db::table('user_info u')
            ->field('u.id, u.pinyin as pinyin, u.name as name, 
                     u.no as no, u.professor as professor, u.role_id as role_id,
                     u.del as del, u.major as u_major, u.department as u_department, 
                     u.status as u_status, d.id as d_id, d.name as r_department,
                     s.status_id, s.status_name as r_status, m.id as m_id,
                     m.name as r_major')
            ->join('department d','d.id=u.department')
            ->join('status s','s.status_id=u.status')
            ->join('major m','m.id=u.major')
            ->where([
                'pinyin'  =>  ['like','%'.$request->param("pinyin").'%'],
                'user_info.name'  =>  ['like','%'.$request->param("name").'%'],
                'no' =>  ['like', $request->param("num").'%'],
                'professor' => ['like','%'.$request->param("tutor").'%'],
                'del' => 0
            ])
            ->where($status_con)
            ->where($major_con)
            ->where($department_con)
            ->where($role_con)
            ->order('no asce')
            ->select();
            // ->paginate(10);                
            
            if (count($student)){
                $this->assign('student',$student);
                // return view('advanced',['student'=>$student]);
                return $this->fetch();
            } 
            if(count($student) == 0){return $this-> error('系统中无此用户信息', '/index/manage.Updatestudentinfo/index');}  
            }
            
            // 如果核酸时间不为空，则以核酸表的信息为主
            if ($request->param("cov_time_2") != null){
                
                $student = Db::table('hesuan h')
                ->field('u.id, u.pinyin as pinyin, u.name as name, 
                     u.no as no, u.professor as professor, u.role_id as role_id,
                     u.del as del, u.major as u_major, u.department as u_department, 
                     u.status as u_status, d.id as d_id, d.name as r_department,
                     s.status_id, s.status_name as r_status, m.id as m_id,
                     m.name as r_major, h.id as h_id, h.user_id as h_user_id, 
                     h.cov_time, h.cov_location')
                ->join('user_info u','u.id = h.user_id')
                ->join('department d','d.id=u.department')
                ->join('status s','s.status_id=u.status')
                ->join('major m','m.id=u.major')
                ->where([
                    'pinyin'  =>  ['like','%'.$request->param("pinyin").'%'],
                    'u.name'  =>  ['like','%'.$request->param("name").'%'],
                    'no' =>  ['like', $request->param("num").'%'],
                    'professor' => ['like','%'.$request->param("tutor").'%'],
                    //'role_id' => 3, // 管理员端应该显示教师和学生，不应该只显示学生
                    'del' => 0
                ])
                ->where($status_con)
                ->where($major_con)
                ->where($department_con)
                ->where($role_con) // 管理员可以选择身份进行查询
                ->whereTime('cov_time', 'between', [$request->param("cov_time_2"), $request->param("cov_time_1")]) // 时间查询
                ->order('no asce, cov_time asce') // 按照学号+核酸时间排序
                ->select();
                // ->paginate(10);
            
                if (count($student)){
                    // $this->assign('student',$student);
                    return view('advanced',['student'=>$student]);
                    // return $this->fetch();
                } 
                else{return $this-> error('系统中无此用户信息', '/index/manage.Updatestudentinfo/index');}              
            }
        }
        return $this->fetch();
    }

    public function info(){  // 查看学生详细信息页面
        $request = Request::instance();
        $user = userinfo::get($request->param("id"));

        $this->assign('no',$user->no);
        $this->assign('name',$user->name);
        $this->assign('sex',$user->sex);
        $this->assign('major',$user->major);
        $this->assign('professor',$user->professor);
        $this->assign('professor_phone',$user->professor_phone);
        $this->assign('department',$user->department);
        $this->assign('birth',$user->birth);
        $this->assign('phone',$user->phone);
        $this->assign('email',$user->email);
        $this->assign('address',$user->address);
        $this->assign('status',$user->status);
        $this->assign('parent',$user->parent);
        $this->assign('parent_phone',$user->parent_phone);
        
        return $this->fetch();
    }    

    // public function index()
    // {
    //     $student = Db::query ('select * from user_info where role_id=3 and del=0');
    //     $this->assign('student',$student);
    //     return $this->fetch();
    // }
    
    // public function search(){  // search.html 模糊搜索结果页面
    //     $request = Request::instance();
    //     $student = Db::query ('select * from user_info where (no like ? or name like ?) and role_id=3 and del=0', ['%'.$request->param("searchinput").'%', '%'.$request->param("searchinput").'%']);
    //     if (count($student)){
    //         $this->assign('student',$student[0]);
    //         return $this->fetch();
    //     } else{
    //         return $this-> error('系统中无此用户信息', '/public/index/student.all/');
    //     }
    // }
    
    public function changeinfo(){  // 管理员修改学生信息页面
        $request = Request::instance();
        $user = userinfo::get($request->param("id"));

        $this->assign('no',$user->no);
        $this->assign('name',$user->name);
        $this->assign('sex',$user->sex);
        $this->assign('major',$user->major);
        $this->assign('professor',$user->professor);
        $this->assign('professor_phone',$user->professor_phone);
        $this->assign('department',$user->department);
        $this->assign('birth',$user->birth);
        $this->assign('phone',$user->phone);
        $this->assign('email',$user->email);
        $this->assign('address',$user->address);
        $this->assign('status',$user->status);
        $this->assign('parent',$user->parent);
        $this->assign('parent_phone',$user->parent_phone);
        
        return $this->fetch();
    }
    
    public function updatestudentinfo(){  // 管理员提交修改学生信息功能
        $request = Request::instance();
        
        if($request->param("role")=="超级管理员"){
            $role_id = 1;
        }
        elseif($request->param("role")=="教师"){
            $role_id = 2;
        }else{
            $role_id = 3;
        }
        // 插入info表格
        $user = userinfo::get($request->param("id"));
        $user->data([
            'no' =>  $request->param("no"),
            'name' =>  $request->param("name"),
            'sex' =>  $request->param("sex"),
            'major' =>  $request->param("major"),
            'professor' =>  $request->param("professor"),
            'professor_phone' =>  $request->param("professor_phone"),
            'role_id' =>  $role_id,
            'birth' =>  $request->param("birth"),
            'phone' =>  $request->param("phone"),
            'address' =>  $request->param("address"),
            'status' =>  $request->param("status"),
            'parent' =>  $request->param("parent"),
            'parent_phone' =>  $request->param("parent_phone"),
        ]);
        $user->save();
        return $this-> success('修改学生信息成功','Index/manage.updatestudentinfo/index');
    }
    
    public function deleteinfo(){  // 管理员删除学生信息功能
        $request = Request::instance();
        // 从user表删除
        $user = rbacuser::get($request->param("id"));
        $user->data([
            'del' =>  1,  // 1代表将用户从user数据库删除
        ]);
        $user->save();
        // 从info表删除
        $user = userinfo::get($request->param("id"));
        $user->data([
            'del' =>  1,  // 1代表将用户从info数据库删除
        ]);
        $user->save();
        
        return $this-> success('删除学生信息成功', 'Index/manage.updatestudentinfo/index');
    }
    
    
    
}