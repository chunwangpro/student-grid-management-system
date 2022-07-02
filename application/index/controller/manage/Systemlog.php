<?php
namespace app\index\controller\manage;
use think\Db;
use think\Request;
use app\index\controller\Common;
use app\index\model\RbacUser;
use app\index\model\RbacRole;
use app\index\model\UserInfo;
use app\index\model\RbacUserHasRoles;
use app\index\model\Log;


# 查询系统日志功能
# 前端只有一个查询组件，参考 manage/Systemuser/index.html
# 有模糊搜索功能、高级搜索功能，参考 manage/Systemuser.php，需要针对日志表进行调整
# 可以搜索某个学生的操作，可以搜索他的学号、姓名、拼音等，这些都可以搜
# 日志表中存储了每次操作人和被操作人的id，如果用户更新自己的信息，则这两个id都是自己
# 如果超级管理员或者管理员修改了别的用户的信息，则这两个id分别是管理员和操作对象
# 有放大镜功能，可以在查询结果的表单中显示几个关键字段，其余信息放到放大镜中，点击放大镜跳转该条日志的详细信息
# 比如在查询表单中显示编号、操作人姓名、被操作人姓名、操作时间、操作类型，把操作的字段和更新的信息放到放大镜中
# 无名单导出功能
# 有日志记录功能，记录了用户在何时查询了什么信息的日志，查看了哪条日志的详细信息
# 本地与云开发的跳转链接统一格式：使用./ 或者 ../

class Systemlog extends Common
{
    public function index()
    {
        
        $major = Db::query ('select * from major');
        $department = Db::query ('select * from department');
        $role = Db::query('select * from rbac_role');
        
        $this->assign('major',$major);
        $this->assign('department',$department);
        $this->assign('role',$role);
        
        
        return $this->fetch();
    }
	
	public function search()
	{
	    $request = Request::instance();
	    $major = Db::query ('select id from major');
        $department = Db::query ('select id from department');
        $role = Db::query('select id from rbac_role');
	    
	    $type = $request->param("type/a") ? $request->param("type/a") : array('登录', '查询', '修改', '搜索', '删除', '导入');
	    
	    
	    
	    //模糊查询
	    if($request->param('mode') == "mode1")
	    {
	        $log = Db::table('log l')
	        //操作人信息
	        ->join('user_info u0', 'u0.id=l.log_user_id0')
	        ->join('user_contact c0','u0.id=c0.id')
            ->join('department d0','d0.id=u0.department')
            ->join('major m0','m0.id=u0.major')
            ->join('rbac_user_has_roles ur0', 'ur0.user_id = u0.id')
            ->join('rbac_role r0', 'r0.id = ur0.role_id')
            //被操作对象信息
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
	            u0.pinyin as pinyin0,
	            u0.no as no0,
	            u0.name as name0,
	            u0.major as major0,
	            m0.name as major0_name,
	            u0.department as department0,
	            d0.name as department0_name,
	            c0.id as contact0,
	            c0.phone as contact0_phone,
	            r0.id as role0,
	            r0.name as role0_name,
	            u0.del as del0,
	            
	            u1.id as id1,
	            u1.pinyin as pinyin1,
	            u1.no as no1,
	            u1.name as name1,
	            u1.major as major1,
	            m1.name as major1_name,
	            u1.department as department1,
	            d1.name as department1_name,
	            c1.id as contact1,
	            c1.phone as contact1_phone,
	            r1.id as role1,
	            r1.name as role1_name,
	            u1.del as del1
	            
	        ')
	        
            ->where
            ([
                'u0.pinyin'  =>  ['like','%'.$request->param("pinyin0").'%'],
                'u0.name'  =>  ['like','%'.$request->param("name0").'%'],
                'u0.no'  =>  ['like','%'.$request->param("num0").'%'],
                
                
                'u1.pinyin'  =>  ['like','%'.$request->param("pinyin1").'%'],
                'u1.name'  =>  ['like','%'.$request->param("name1").'%'],
                'u1.no'  =>  ['like','%'.$request->param("num1").'%'],
                
                
                'l.log_type' => array('in', $type),
                'l.attribute_name' => ['like','%'.$request->param("attribute_name").'%']

            ])
            
            ->order('l.log_id asce')
            ->select();
            //->paginate(20);
            
            
            if( count($log) )
            { 
                $this->assign('log', $log);
                return $this->fetch();
            }
            else 
            {
                return $this->error('系统中无相关日志信息');    
            }
	    }
	    
	    //高级搜索
	    if($request->param("mode") == "mode2")
	    {
	        $time = $request->param("ope_time") ? $request->param("ope_time") : "1970-1-1";
	        $timestamp0 = strtotime($time);
	        $timestamp1 = $time=="1970-1-1" ? time() : $timestamp0 + 1*24*60*60;
	        $day = date('Y-m-d H:i:s', $timestamp0);
	        $night = date('Y-m-d H:i:s', $timestamp1);
	        
	        
	        $role0 = $request->param("role0/a") ? $request->param("role0/a") : range(1, count($role), 1);
    	    $major0 = $request->param("major0/a") ? $request->param("rmajor0/a") : range(1, count($major), 1);
    	    $department0 = $request->param("department0/a") ? $request->param("department0/a") : range(1, count($department), 1);
    	    
    	    $role1 = $request->param("role1/a") ? $request->param("role1/a") : range(1, count($role), 1);
    	    
    	    $major1 = $request->param("major1/a") ? $request->param("major1/a") : range(1, count($major), 1);
    	    $department1 = $request->param("department1/a") ? $request->param("department1/a") : range(1, count($department), 1);
	        
	        
	        $log = Db::table('log l')
	        //操作人信息
	        ->join('user_info u0', 'u0.id=l.log_user_id0')
	        ->join('user_contact c0','u0.id=c0.id')
            ->join('department d0','d0.id=u0.department')
            ->join('major m0','m0.id=u0.major')
            ->join('rbac_user_has_roles ur0', 'ur0.user_id = u0.id')
            ->join('rbac_role r0', 'r0.id = ur0.role_id')
            //被操作对象信息
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
	            u0.pinyin as pinyin0,
	            u0.no as no0,
	            u0.name as name0,
	            u0.major as major0,
	            m0.name as major0_name,
	            u0.department as department0,
	            d0.name as department0_name,
	            c0.id as contact0,
	            c0.phone as contact0_phone,
	            r0.id as role0,
	            r0.name as role0_name,
	            u0.del as del0,
	            
	            u1.id as id1,
	            u1.pinyin as pinyin1,
	            u1.no as no1,
	            u1.name as name1,
	            u1.major as major1,
	            m1.name as major1_name,
	            u1.department as department1,
	            d1.name as department1_name,
	            c1.id as contact1,
	            c1.phone as contact1_phone,
	            r1.id as role1,
	            r1.name as role1_name,
	            u1.del as del1
	            
	        ')
	        
            
            ->where
            ([
                'u0.pinyin'  =>  ['like','%'.$request->param("pinyin0").'%'],
                'u0.name'  =>  ['like','%'.$request->param("name0").'%'],
                'u0.no'  =>  ['like','%'.$request->param("num0").'%'],
                'r0.id' => array('in', $role0),
                'm0.id' => array('in', $major0),
                'd0.id' => array('in', $department0),
                
                
                'u1.pinyin'  =>  ['like','%'.$request->param("pinyin1").'%'],
                'u1.name'  =>  ['like','%'.$request->param("name1").'%'],
                'u1.no'  =>  ['like','%'.$request->param("num1").'%'],
                'r1.id' => array('in', $role1),
                'm1.id' => array('in', $major1),
                'd1.id' => array('in', $department1),
                
                
                'l.log_type' => ['in', $type],
                'l.attribute_name' => ['like','%'.$request->param("attribute_name").'%'],
                'l.time' => array( 'between', "$day, $night" )
                
            ])
            
            ->order('l.log_id dsce')
            ->select();
            //->paginate(20);
            
           
            if(count($log))
            {
                $this->assign('log', $log);
                return $this->fetch();
            }
            else 
            {
                return $this->error('系统中无相关日志信息');    
            }
	    }
	    
	    
	}
	
	public function info()
	{
	    $request = Request::instance();
	    $log = Db::table('log l')
	        //操作人信息
	        ->join('user_info u0', 'u0.id=l.log_user_id0')
	        ->join('user_contact c0','u0.id=c0.id')
            ->join('department d0','d0.id=u0.department')
            ->join('major m0','m0.id=u0.major')
            ->join('rbac_user_has_roles ur0', 'ur0.user_id = u0.id')
            ->join('rbac_role r0', 'r0.id = ur0.role_id')
            //被操作对象信息
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
	            
	            
	            u0.no as no0,
	            u0.name as name0,
	            m0.name as major0_name,
	            d0.name as department0_name,
	            c0.phone as contact0_phone,
	            r0.name as role0_name,
	            u0.del as del0,
	            
	            u1.no as no1,
	            u1.name as name1,
	            m1.name as major1_name,
	            d1.name as department1_name,
	            c1.phone as contact1_phone,
	            r1.name as role1_name,
	            u1.del as del1
	            
	        ')
	        
            
            ->where(['l.log_id' => $request->param("id")])
            ->select();
            
	    $this->assign('log', $log);
	    return $this->fetch();
	}
    
}