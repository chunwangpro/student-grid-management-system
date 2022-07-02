<?php

//Todo 添加文件功能说明

namespace app\index\controller;
use app\index\library\Auth;
use app\index\library\Checkform;
use app\index\library\Updatelog;
use app\index\library\Searchauth;
use think\Controller;

class Common extends Controller
{
    protected $auth; //Todo 添加注释说明
    protected $checkform;
    protected $updatelog;
    protected $searchauth;
    protected $checkLoginExclude = ['Login/index','Login/dologin','Login/logout'];//Todo 添加注释说明
    
    protected function _initialize()  //Todo 添加注释说明,_initialize方法的执行时机是什么?
    {
        $this->auth = Auth::getInstance();
        // $this->checkform = Checkform::getInstance();
        // $this->updatelog = Updatelog::getInstance();
        $this->searchauth = Searchauth::getInstance();

        $controller = $this->request->controller();
        $action = $this->request->action();
        if (in_array($controller.'/'.$action, $this->checkLoginExclude)){  //Todo 添加注释说明
            return;
        }
        
        if(!$this->auth->isLogin()){      //Todo 添加注释说明
            $this->error('请先登录系统','Login/index');
        }

        if(!$this->auth->checkAuth($controller,$action)){      //Todo 添加注释说明
            $this->error('无权访问');
        }

        $this->view->engine->layout('common/layout');
        $this->assign('layout_menu', $this->auth->menu($controller));

        $userid = $this->auth->getUserid();
        $userName = $this->auth->getUserName();
        $this->assign('layout_login_user', ['uid' => $userid,
            'name' => $userName
        ]);
    }
}