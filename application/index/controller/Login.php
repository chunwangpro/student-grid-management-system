<?php
namespace app\index\controller;
use app\index\model\RbacUser;
use app\index\library\Recordlog;

class Login extends Common  //Todo 添加说明
{
    public function index()
    {
        // 关闭当前模板的布局功能
        // $this->view->engine->layout(false);
        return $this->fetch();
    }
    
    public function dologin(){
        if($this->request->isPost()){
            $data = [
                'username' => $this->request->post('username/s','','trim'), 
                'password' => $this->request->post('password/s','','trim')
            ];
            $validate= validate('RbacUser');;
            if(!$validate->check($data)){
                $this->error('登录失败:' . $validate->getError());
            }
            if(!$this->auth->login($data['username'],$data['password'])){
                $this->error('登录失败:'.$this->auth->getError());
            }

            $user = rbacuser::get($this->auth->getUserid());
            if($user->modify == 1){
                $record = new Recordlog();
                $record->loginrec($this->auth->getUserid());//生成登录日志
                $this->success("登录成功，您的密码为初始密码，请尽快修改！",'./index/modify/index');
            }

            $record = new Recordlog();
            $record->loginrec($this->auth->getUserid());
            $this->success("登录成功！",'main/index');//生成登录日志
            
        }else{
            $this->error('登录失败:表单数据错误');
            $this->recordlog($this->auth->getUserid());//log表设计中操作字段未区分登录成功和登录失败，同样生成登录日志
        }
    }
    
    public function logout(){
        $this->auth->logout();
        $this->redirect('Login/index');
    }
}
