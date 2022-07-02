<?php
//Todo 添加文件功能说明

namespace app\index\library;

use think\Session;
use think\Db;
use app\index\model\RbacMenu;
use app\index\model\RbacUser;
use app\index\model\UserInfo;
use app\index\model\RbacPermission;
use app\index\model\RbacRoleHasPermissions;
use app\index\model\RbacUserHasRoles;

class Auth
{
    protected $sessionName = 'user';
    
    public function login($username, $password)
    {
        $user_info = UserInfo::get(['no' => $username, 'del' => 0]); //Todo 添加语法说明

        // $user = Db::table('user_info u')
        // ->field('u.id, u.pinyin as pinyin, u.name as name, 
        //         u.no as no, u.del as del, u.major as u_major,
        //         u.department as u_department, 
        //         r.password as password, r.salt as salt')
        //     ->join('rbac_user r','r.user_id=u.id','LEFT')
        //     ->where('u.del = 0')
        //     ->where('u.no = ?', [$username])
        //     ->select();

        if(!$user_info){
            $this->setError('用户不存在！');
            return false;
        }
        // echo $this->passwordMD5($password,$user->salt); // 忘记密码时，管理员直接输出密码
        $user = RbacUser::get(['user_id' => $user_info->id]);

        if($user->password != $this->passwordMD5($password, $user->salt)){
            $this->setError('用户名或密码不正确！');
            return false;
        }
        
        // $user_db = new UserInfo();
        // $user_info = $user_db->where('id','=',$user->user_id)->find();

        // Session::set($this->sessionName,[
        //     'id' => $user->user_id,
        //     'name' => $user_info->name
        // ]);
        Session::set($this->sessionName,[
            'id' => $user_info->id,
            'name' => $user_info->name
        ]);
        return true;
    }
    
    public function isLogin()
    {
        return Session::has($this->sessionName . '.id');//Todo 添加语法说明及注释
    }

    public function getUserid()
    {
        return Session::get($this->sessionName . '.id');//Todo 添加语法说明及注释
    }
    public function getUserName()
    {
        return Session::get($this->sessionName . '.name');//Todo 添加语法说明及注释
    }
    
    public function logout(){
        Session::delete($this->sessionName);  //Todo 添加语法说明及注释
        return true;
    }

    protected static $instance; //Todo 添加语法及注释说明

    public static function getInstance()  //Todo 添加语法及注释说明
    {
        if(is_null(self::$instance)){
            self::$instance = new static();  //Todo 添加语法及注释说明
        }
        return self::$instance;
    }


    protected $error;

    public function getError()
    {
        return $this->error;
    }

    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }

    public function passwordMD5($password,$salt){
        return md5(md5($password).$salt);  //Todo 添加注释说明
    }
    
    public function menu($curr){
        
        $userPermissions = $this->getLoginUserPermissions();
        $menu = RbacMenu::tree();
        $data = $menu->getData();
        $results = [];
        foreach ($userPermissions as $v){
            if($v['controller'] === '*'){
                $results = $data;
                break;
            }

            foreach ($data as $vv){
                if(strtolower($v['controller']) === strtolower($vv['controller'])){
                    $results[] = $vv;
                    break;
                }
            }
        }
        return $menu->data($results)->getTree(strtolower($curr));
    }

    public function checkAuth($controller,$action){
        $userPermissions = $this->getLoginUserPermissions();
        
        foreach($userPermissions as $v){
            if($v['controller'] === '*'){
                return true;               
            }
            if(strtolower($v['controller']) === strtolower($controller)){
                if($v['action'] === '*'){
                    return true;
                }
                if($action === $v['action']){
                    return true;
                }
            }
        }
        return false;
    }
    public function getLoginUserPermissions(){
        $user_id = Session::get($this->sessionName . '.id');

        $role_user = new RbacUserHasRoles();
        $role_ids = $role_user->where('user_id',$user_id)->select();

        $role_result = [];
        foreach ($role_ids as $v){
            array_push($role_result,$v['role_id']);
        }
        
        $role_permission = new RbacRoleHasPermissions();
        $permission_ids = $role_permission->where('role_id','in',$role_result)->select();

        $permission_result = [];
        foreach ($permission_ids as $v){
            array_push($permission_result,$v['permission_id']);
        }

        $permission = new RbacPermission();
        $permissions = $permission->where('id','in',$permission_result)->select();


//        foreach ($permissions as $v){
//            echo $v;
//        }
        return $permissions;
    }
}