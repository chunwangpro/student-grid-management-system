<?php
namespace app\index\controller\manage;
use think\Db;
use think\Request;
use app\index\controller\Common;
use app\index\model\UserInfo;

class Checkstudentinfo extends Common
{
    // 教师端代码逻辑与管理员端类似，只是查询时身份限制为学生，详细注释在Updatestudentinfo文件
    public function index()
    {
        $student = Db::name('user_info')->where('role_id=3 and del=0')->order('no asce')->paginate(2);
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
	    // 渲染模板输出
	   // return view('',['student'=>$student]);
        // $student = Db::query ('select * from user_info where role_id=3 and del=0');
        // $this->assign('student',$student);
        // return $this->fetch();
    }
	
    public function search(){  // search.html 以学号搜索结果页面
        $request = Request::instance();
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
                     m.name as r_major')
            ->join('department d','d.id=u.department')
            ->join('status s','s.status_id=u.status')
            ->join('major m','m.id=u.major')
            ->where([
                'pinyin'  =>  ['like','%'.$request->param("pinyin").'%'],
                'user_info.name'  =>  ['like','%'.$request->param("name").'%'],
                'no' =>  ['like', $request->param("num").'%'],
                'professor' => ['like','%'.$request->param("tutor").'%'],
                'm.name' => ['like', '%'.$request->param("major").'%'],
                'd.name' => ['like', '%'.$request->param("department").'%'],
                's.status_name' => ['like', '%'.$request->param("status").'%'],
                'role_id' => 3,
                'del' => 0
            ])
            ->order('no asce')
            ->select();
            // ->paginate(10);
            
            if (count($student)){
                $this->assign('student',$student);
                return $this->fetch();} 
            else{return $this-> error('系统中无此用户信息', '/index/manage.Checkstudentinfo/index');}    
        }
        
        // 高级搜索
        if ($request->param("mode") == "mode2") {
            
            $major_data = $request->param("major2/a");  
            $department_data = $request->param("department2/a");  
            $status_data = $request->param("status2/a");  
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
            for ($i=1; $i<count($major_data); $i++){
                $major_con = $major_con.' or '.'u.major = '.$major_data[$i];
            }
            for ($i=1; $i<count($department_data); $i++){
                $department_con = $department_con.' or '.'u.department = '.$department_data[$i];
            }
            for ($i=1; $i<count($status_data); $i++){
                $status_con = $status_con.' or '.'u.status = '.$status_data[$i];
            }
            
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
                'role_id' => 3,
                'del' => 0
            ])
            ->where($status_con)
            ->where($major_con)
            ->where($department_con)
            ->order('no asce')
            ->select();
            // ->paginate(10);                
            
            if (count($student)){
                $this->assign('student',$student);
                // return view('advanced',['student'=>$student]);
                return $this->fetch();
            } 
            if(count($student) == 0){return $this-> error('系统中无此用户信息', '/index/manage.Checkstudentinfo/index');}  
            }
            
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
                    'role_id' => 3,
                    'del' => 0
                ])
                ->where($status_con)
                ->where($major_con)
                ->where($department_con)
                ->whereTime('cov_time', 'between', [$request->param("cov_time_2"), $request->param("cov_time_1")])
                ->order('no asce, cov_time asce') //按照学号+核酸时间排序
                ->select();
                // ->paginate(10);
            
                if (count($student)){
                    // $this->assign('student',$student);
                    return view('advanced',['student'=>$student]);
                    // return $this->fetch();
                } 
                else{return $this-> error('系统中无此用户信息', '/index/manage.Checkstudentinfo/index');}              
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
}