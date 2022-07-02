<?php
namespace app\index\controller;
use think\Request;
use think\Db;
use app\index\model\RbacUser;
use app\index\model\UserInfo;
use app\index\controller\Common;

class Modify extends Common
{
    public function index()
    {
        return $this->fetch();
    }
    
    public function modify_check(){  // 提交新密码
        
        // 用户名不允许修改
        //
        // 检查用户名重复，用户可以用自己原本的用户名，但不能和别人重复
        //$user = Db::query ('select * from rbac_user where username=? and del="0"', [$request-> param ("newusername")]);
        //if($user and $user[0]['user_id'] != $this->auth->getUserid()) {
        //    $this-> error('该用户名已存在，请输入新的用户名');
        //}
        $request = Request::instance();
        $user = RbacUser::get($this->auth->getUserid());

        if($request->param("oldpassword") == null){
            $this->error('修改失败：您还未输入当前密码！');
        }

        if($request->param("newpassword") == null){
            $this->error('修改失败：请输入新密码！');
        }

        if($request->param("checknewpassword") == null){
            $this->error('修改失败：请再次输入新密码！');
        }

        if($this->auth->passwordMD5($request->param("oldpassword"), $user->salt) != $user->password){
            $this->error('修改失败：当前密码不正确，请重新输入！');
        }

        if($request->param("oldpassword") == $request->param("newpassword")){
            $this->error('修改失败：新密码不能与当前密码相同，请重新输入！');
        }

        $user_info = UserInfo::get($this->auth->getUserid());
        if($request->param("newpassword") == $user_info->birth){
            $this->error('修改失败：新密码不能为出生日期，请重新输入！');
        }

        if($request->param("newpassword") != $request->param("checknewpassword")){
            $this->error('修改失败：两次输入的新密码不一致，请重新输入！');
        }

        // 验证密码是否符合登录要求
        $data = [
            'username' => $user_info->no,
            'password' => $request->param("newpassword")
        ];
        $validate= validate('RbacUser');;
        if(!$validate->check($data)){
            $this->error('修改失败:' . $validate->getError());
        }
        
        $user->data([
            'password' => $this->auth->passwordMD5($request->param("newpassword"), $user->salt),
            'modify' => 0,
        ]);
        $result = $user->save();
        if($result){
            $this->success('密码修改成功，请重新登录！', './index/login/logout');
        }else{
            return $this->error('删除密码失败');
        }
        
    }
}