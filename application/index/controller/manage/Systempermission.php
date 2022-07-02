<?php
namespace app\index\controller\manage;
use think\Db;
use think\Request;
use app\index\controller\Common;
use app\index\model\UserInfo;
use app\index\model\Department;
use app\index\model\RbacUserHasRoles;
use app\index\model\UserManageDepartment;


# 超级管理员可以在这个页面修改教师管理员为某一个学苑的指导教师
# 对应的数据库为 user_manage_department
# 从而实现一个教师可以管理多个学苑的功能
# 在这个页面有一个搜索组件，和表单，提供模糊搜索，在全体教师（role_id=2且del=0）中搜索教师
# 表单中显示教师的工号、姓名、管理的学苑名称
# 超级管理员可以选择某个教师，并在下拉菜单中选择某一个学苑，不要写死，在数据库中查询所有学苑信息
# 可以新增某教师为某学苑管理员，可以新增多条，也可以删除（del修改为1）

# 不允许搜索学生，不允许设置学生管理某个苑，相应的添加学生为管理员的功能为：教师可以在supervise/checkstudent中将学生的身份修改为学生管理员
# 也许可以搜索学生，超级管理员应该可以看到所有的学苑管理员信息，包括教师管理员和学生管理员


class Systempermission extends Common
{
    public function index() //展示所有学苑管理权限
    {
        $rows = db('user_info')
        ->join('rbac_user_has_roles','user_info.id=rbac_user_has_roles.user_id','left')
        ->join('rbac_role','rbac_role.id=rbac_user_has_roles.role_id','left')
        ->join('user_manage_department','user_info.id=user_manage_department.user_id','left')
        ->join('major','user_info.major=major.id','left')
        ->join('department','user_manage_department.department=department.id','left')
        //->where('rbac_user_has_roles.role_id',2)
        ->where('user_info.del',0)
        ->where('user_manage_department.del',0)
        ->field(['user_manage_department.id as manage_id', 'rbac_role.name as role', 'user_info.id', 'user_info.no','user_info.name','major.name as major','department.name as department', 'department.id as department_id'])//显示的字段
        ->order('no asce')  // 降序 desc
        ->order('department_id asce')
        ->paginate(10);
        //->select();

        return view('',['rows'=>$rows]);
    }
    
    // public function info(){  // 增加学苑权限页面
    //     //版本3 点击放大镜后在数据库中插入一行新的记录
    //     //前几行内容与该行一致，最后一行为一个下拉框
    //     $request = Request::instance();
	//     //在user_manage_department表中新增一行
	//     $info = UserManageDepartment::get($request->param("id"));
	//    // $this->assign('user_id',$info->user_id);
    // //     $this->assign('department',$info->department);
	//     $department = new UserManageDepartment;
	//     $department->data([  // 初始化，用户名：学号，密码：出生日期
    //         'user_id' =>  $info->user_id,
    //         // 'department' => $info->department, 
    //         'del' => 0,
    //     ]);
    //     $department->save();
    //     return $this-> success('增加一条新的记录', '../index/manage.systempermission/index');
        //版本2（不可行）
        // $UserManageDepartment = UserManageDepartment::get($this->auth->getUserid());
        // $userinfo = userinfo::get($this->auth->getUserid());
        // $this->assign('no',$userinfo->no);
        // $this->assign('name',$userinfo->name);
        
        
        
        // ->order('id desc')//根据id降序排列
        // ->field(['name'])//显示的字段
        // ->select();
        
        

        // $this->assign("name",$departments[0]["name"]);


        // return view('',['departments'=>$departments]);
    //}
    
    // public function updateindex(){
    //     $UserManageDepartment = UserManageDepartment::get($this->auth->getUserid());
    //     $userinfo = userinfo::get($this->auth->getUserid());
    //     $this->assign('no',$userinfo->no);
    //     $this->assign('name',$userinfo->name);
        
    //     $department = UserManageDepartment::get($UserManageDepartment->department);
    //     $this->assign('department',$department->department);
    // }
    
    // public function updatedepartmentinfo(){  // 管理员提交修改信息功能
    //     $request = Request::instance();
    //     $newdepartment = $request->param("alldepartments");
    //     echo($newdepartment);

    //     $update = db('user_manage_department')
    //     ->where('department',null)//条件:状态为1
    //     ->where('user_manage_department.del=0')
        
    //     ->select();

    //     $update->data([
    //         'department' =>   $newdepartment,
            
    //     ]);
    //     $update->save();
    //     return $this-> success('添加学苑权限成功','Index/manage.Systempermission/index');
       
        
    //     // $log = new Log;
    //     // $log->data([
    //     //         'log_user_id0' => $this->auth->getUserid(),
    //     //         'log_type' => '添加',
    //     //         'log_user_id1' => $this->auth->getUserid(),
    //     //         'attribute_name' => '管理学苑权限',
    //     //         'new_info' => '添加管理学苑权限'.$update1->department
    //     //     ]);
    //     //     $log->save();
       
    // }

    //首页搜索管理员权限
    public function searchadmin()
    {
        $request = Request::instance();
        $rows = db('user_info u')
        ->join('rbac_user_has_roles','u.id=rbac_user_has_roles.user_id','left')
        ->join('rbac_role','rbac_role.id=rbac_user_has_roles.role_id','left')
        ->join('user_manage_department','u.id=user_manage_department.user_id','left')
        ->join('major','u.major=major.id','left')
        ->join('department','user_manage_department.department=department.id','left')
        //->where('rbac_user_has_roles.role_id',2)
        ->where('u.del',0)
        ->where('user_manage_department.del',0)
        ->where(function ($query) use ($request) {
            $query->where(['u.name'  =>  ['like','%'.$request->param("searchinput").'%']])
            ->whereor(['no' =>  ['like', '%'.$request->param("searchinput").'%']]);
        })
        ->field(['user_manage_department.id as manage_id', 'rbac_role.name as role', 'u.id', 'u.no','u.name', 'u.del', 'major.name as major','department.name as department', 'department.id as department_id'])//显示的字段
        ->order('no asce')  // 降序 desc
        ->order('department_id asce')
        ->paginate(10);
        //->select();

        $this->assign('rows',$rows);
        return $this->fetch(); 
    }
    

    public function search()//教师管理员名单页面
    {
        $rows = Db::table('user_info u')
        ->join('department d','d.id=u.department')
        ->join('major m','m.id=u.major')
        ->join('rbac_user_has_roles','user_info.id=rbac_user_has_roles.user_id','left')
        ->where('rbac_user_has_roles.role_id',2)  //条件:教师管理员
        ->where('u.del=0')
        ->order('no asce')
        ->field(['u.id', 'u.no','u.name','m.name as major','d.name as department'])
        //->paginate(10);
        ->select();
        
        // 获取教师管理的学苑
        for ($i=0; $i<count($rows); $i++){
            $department_id = Db::table('user_manage_department g')
            ->join('department d','d.id=g.department','LEFT')
            ->field('d.name as department_name')
            ->where('g.user_id = ?',[$rows[$i]['id']])
            ->where('g.del = 0')
            ->select();
           
            $department_manage = '';
            if (count($department_id) != 0){
                $department_manage = $department_manage.$department_id[0]["department_name"];
                for ($j=1; $j<count($department_id); $j++){
                    $department_manage = $department_manage.'、'.$department_id[$j]["department_name"];
                }
            }
            $rows[$i]['department'] = $department_manage;
        }
        
        $this->assign('rows',$rows);
        return $this->fetch(); 
    }






    public function submit() // 查询结果页面
    {
        $request = Request::instance();
                
        $rows = Db::table('user_info u')
        ->join('department d','d.id=u.department')
        ->join('major m','m.id=u.major')
        ->join('rbac_user_has_roles','user_info.id=rbac_user_has_roles.user_id','left')
        ->field('u.id, u.name as name, u.no as no, d.name as department, m.name as major')
        ->where([
            'user_info.del' => 0,
            'rbac_user_has_roles.role_id'=> 2
            ])
        ->where(function ($query) use ($request) {
                $query->where(['u.name'  =>  ['like','%'.$request->param("searchinput").'%']])
                ->whereor(['no' =>  ['like', '%'.$request->param("searchinput").'%']]);
            })
        ->order('no asce')
        ->field(['user_info.id', 'user_info.no','user_info.name','m.name as major','d.name as department'])
        ->select();

        if (count($rows)){
            // 获取教师管理的学苑
            for ($i=0; $i<count($rows); $i++){
                $department_id = Db::table('user_manage_department g')
                ->join('department d','d.id=g.department','LEFT')
                ->field('d.name as department_name')
                ->where('g.user_id = ?',[$rows[$i]['id']])
                ->where('g.del = 0')
                ->select();
           
                $department_manage = '';
                if (count($department_id) != 0){
                    $department_manage = $department_manage.$department_id[0]["department_name"];
                    for ($j=1; $j<count($department_id); $j++){
                        $department_manage = $department_manage.'、'.$department_id[$j]["department_name"];
                    }
                }
                $rows[$i]['department'] = $department_manage;
            }
            //$this->assign('rows',$rows);
            //return $this->fetch();
            return view('',['rows'=>$rows]);
        } else{
            return $this-> error('系统中无此教师信息');
        }
    }




    //新增学苑管理权限页面，需要输入学工号、姓名、学苑
    public function add(){
        //传递res对象数组到前端，后续对数组的处理由js完成，详情可见view对应的html文件。
        $res = Department::all();
        $this->assign("res",$res);
        return $this->fetch();
    }
	



    //新增学苑管理权限，表单提交后插入数据库
    public function add_into_table(){
        $request = Request::instance();
        $no = $request->param('no');
        $name = $request->param('name');
        $department_id = $request->param('department');

        // 学苑不能为空
        if(!$department_id){
            return $this->error('学苑名称不能为空，请检查输入');
        }
        // 找到管理学苑的id
        $department = Department::get(['id' => $department_id]);
        if(!$department){
            return $this->error('系统中无此学苑，请检查输入');
        }

        // 找到 user id
        $user = UserInfo::get(['no' => $no, 'name' => $name, 'del' => 0]);
        if(!$user){
            return $this->error('系统中无此用户，请检查学工号和姓名输入是否准确/匹配');
        }

        // 检查管理员权限
        $check_role = RbacUserHasRoles::get($user->id);
        // 如果用户是学生，则将其改为学生管理员
        if($check_role->role_id == 3){
            $check_role->data([
                'role_id' =>  4,
            ]);
            $check_role->save();
        }
        // 检查学生管理员管理的学苑是否是自己的学苑
        $check_role = RbacUserHasRoles::get($user->id);
        if($check_role->role_id == 4 and $user->department != $department_id){
            return $this->error('添加权限失败，学生管理员只能管理自己所在的学苑！');
        }

        // 检查是否已经存在该学苑权限
        $permission = UserManageDepartment::get(['user_id' => $user->id,'department' => $department_id, 'del' => 0]);
        if ($permission){
            return $this->error('该用户已经是该学苑管理员，无法重复添加');
        }

        // 插入UserManageDepartment表格
        $new_permission = new UserManageDepartment;
        $new_permission->data([
            'user_id' =>  $user->id,
            'department' =>  $department_id,
            'del' => 0,
        ]);
        $result = $new_permission->save();
        
        if ($result){
            return $this->success('学苑权限添加成功', './index/manage.systempermission/index');
        }else{
            return $this->error('学苑权限添加失败');
        }
        
    }







    // 为老管理员增加新的学苑管理权限
    public function addold(){
        $request = Request::instance();
        $user = UserInfo::get($request->param('id'));
        $this->assign('no', $user->no);
        $this->assign('name', $user->name);
        
        //传递res对象数组到前端，后续对数组的处理由js完成，详情可见view对应的html文件。
        $res = Department::all();
        $this->assign("res",$res);
        return $this->fetch();
    }





    // 修改老管理员的学苑管理权限
    public function modifyold(){
        $request = Request::instance();
        $permission = UserManageDepartment::get($request->param('id'));

        $user = UserInfo::get($permission->user_id);

        $this->assign('no', $user->no);
        $this->assign('name', $user->name);
        $this->assign('permission_id', $permission->id);  // 修改这一条permission，传到前端，隐藏起来

        //传递res对象数组到前端，后续对数组的处理由js完成，详情可见view对应的html文件。
        $res = Department::all();
        $this->assign("res",$res);
        return $this->fetch();
    }






    //修改学苑管理权限，表单提交后插入数据库
    public function modifyoldpermission(){
        $request = Request::instance();
        $no = $request->param('no');
        $name = $request->param('name');
        $permission_id = $request->param('permission_id');
        $department_id = $request->param('department');

        // 学苑不能为空
        if(!$department_id){
            return $this->error('学苑名称不能为空，请检查输入');
        }
        // 找到管理学苑的id
        $department = Department::get(['id' => $department_id]);
        if(!$department){
            return $this->error('系统中无此学苑，请检查输入');
        }

        // 找到 user id
        $user = UserInfo::get(['no' => $no, 'name' => $name, 'del' => 0]);
        if(!$user){
            return $this->error('系统中无此用户，请检查学工号和姓名输入是否准确/匹配');
        }

        // 检查管理员权限
        $check_role = RbacUserHasRoles::get($user->id);
        // 如果用户是学生，则将其改为学生管理员
        if($check_role->role_id == 3){
            $check_role->data([
                'role_id' =>  4,
            ]);
            $check_role->save();
        }
        //检查学生管理员管理的学苑是否是自己的学苑
        $check_role = RbacUserHasRoles::get($user->id);
        if($check_role->role_id == 4 and $user->department != $department_id){
            return $this->error('添加权限失败，学生管理员只能管理自己所在的学苑！');
        }
        
        // 检查是否已经存在该学苑权限，同时可以检查新增权限是否与原先权限相同
        $exist_permission = UserManageDepartment::get(['user_id' => $user->id, 'department' => $department_id, 'del' => 0]);

        if ($exist_permission){
            return $this->error('该用户已经是该学苑管理员，无法重复添加');
        }

        // // 检查新增权限是否与原先权限相同
        // if ($permission->department == $department_id){
        //     return $this->error('该用户已经是该学苑管理员，无需修改');
        // }
        
        // 找到该条权限
        $permission = UserManageDepartment::get($permission_id);
        $permission->data([
            'department' =>  $department_id,
        ]);
        $result = $permission->save();
        
        if ($result){
            return $this->success('学苑权限修改成功', './index/manage.systempermission/index');
        }else{
            return $this->error('学苑权限修改失败');
        }
        
    }






    //管理员删除信息的功能
	public function deletepermission(){
	    $request = Request::instance();

	    //从user_manage_department表中删除
	    $info = UserManageDepartment::get($request->param("id"));

        // 检查管理员身份
        $check_role = RbacUserHasRoles::get($info->user_id);
        // 如果用户是学生管理员，删除权限后将其身份改为学生
        if($check_role->role_id == 4){
            $check_role->data([
                'role_id' =>  3,
            ]);
            $check_role->save();
        }

        // 1代表从user_manage_department表中删除
        $info->data([
            'del' =>  1,
        ]);

        $result = $info->save();
        if($result){
            return $this-> success('删除学苑权限成功', '../index/manage.systempermission/index');
        }else{
            return $this-> success('删除学苑权限失败');
        }
	}
}