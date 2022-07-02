# 李昭辉-学生网格-修改密码功能

## 用户故事

用户日常修改密码、系统检测到用户密码为初始密码后，也会跳转到修改密码

## 界面截图

1. 入口 - 右上角

   <img src="https://tva1.sinaimg.cn/large/e6c9d24ely1h3lqfjxuekj20ue0fijrw.jpg" alt="截屏2022-06-06 21.39.42" style="zoom:50%;" />

2. 修改密码界面

   在修改密码页面的表单中含有当前密码、新密码、确认新密码三个输入框，用户需要依次输入相应内容并提交

<img src="https://tva1.sinaimg.cn/large/e6c9d24ely1h2yw2ijrffj21ho0rzgnm.jpg" alt="截屏2022-06-06 21.37.21" style="zoom: 50%;" />

## 系统中URL地址

http://group2.edu365.pub/index/modify/index.html

## 使用的账号权限

所有用户均有修改密码的权限

## 涉及的数据库表及字段

涉及到 rbac_user 表

![截屏2022-06-26 16.39.56](https://tva1.sinaimg.cn/large/e6c9d24ely1h3lqjdgurhj21a00hqaf8.jpg)

其中前四个为测试账号、salt与密码均相同，其余用户的salt为随机字符串

每个用户的初始密码默认为出生日期，modify默认为1

如果用户更改了密码，将会修改密码，modify将会修改为0，但不会修改salt。

## 前后端代码及说明

### 功能说明

1. 验证前端当前密码输入框中是否为空，并给出详细的提示信息“当前密码为空，请先输入！”
2. 验证前端新密码输入框中是否为空，并给出详细的提示信息“新密码为空，请先输入！”
3. 验证前端再次确认密码输入框中是否为空，并给出详细的提示信息“请再次输入新密码！”
4. 检查用户提交的新密码不能为空
5. 检查用户提交的新密码不能少于6位
6. 系统会检查用户两次输入的新密码是否相同
7. 验证用户输入的当前密码是否正确
8. 系统会验证新密码与当前密码是否相同，如果相同会提示“修改失败，新密码与当前密码相同”
9. 系统会验证新密码与初始密码是否相同，如果相同会提示“修改失败，新密码与初始密码相同”

### 代码文件

index/controller/Modify.php

````php
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
````

### 前端代码

View/modify/index.html

````html
<div class="widget-box">
    <div class="widget-box widget-color-dark" id="widget-box-7">
        <div class="widget-header widget-header-small">
            <h5 class="widget-title">修改密码</h5>
        </div>
        
        <div class="widget-body">
            <div class="widget-main">
                <div class="alert alert-info">
                    <form action="modify_check" method="post" class="form-horizontal" role="form">
                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 当前密码 </label>

                            <div class="col-sm-9">
                                <input type="text" name="oldpassword" id="form-field-1" placeholder="请填写当前密码"
                                    class="col-xs-10 col-sm-5">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 新密码 </label>

                            <div class="col-sm-9">
                                <input type="text" name="newpassword" id="form-field-1" placeholder="请填写您的新密码"
                                    class="col-xs-10 col-sm-5">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 确认密码 </label>

                            <div class="col-sm-9">
                                <input type="text" name="checknewpassword" id="form-field-1" placeholder="请再次填写新密码"
                                    class="col-xs-10 col-sm-5">
                            </div>
                        </div>


                        <div style="text-align:center;vertical-align:middle;">
                            <input type="submit" value="确认修改" />
                        </div>


                    </form>



                </div><!-- /.widget-main -->
            </div><!-- /.widget-body -->
        </div>
    </div><!-- /.widget-body -->
</div>
````

## 前端页面截图

![截屏2022-06-26 17.16.46](https://tva1.sinaimg.cn/large/e6c9d24ely1h3lrnvktygj21c00u0gng.jpg)

![截屏2022-06-26 17.16.53](https://tva1.sinaimg.cn/large/e6c9d24ely1h3lrnzg451j21c00u0myx.jpg)

![截屏2022-06-26 17.16.59](https://tva1.sinaimg.cn/large/e6c9d24ely1h3lro2od5kj21c00u0myz.jpg)

![截屏2022-06-06 21.37.48](https://tva1.sinaimg.cn/large/e6c9d24ely1h2yw38lvuyj22m40hwq4z.jpg)

![截屏2022-06-06 22.30.11](https://tva1.sinaimg.cn/large/e6c9d24ely1h2yw9528jtj21b60omjs0.jpg)

![截屏2022-06-06 22.30.34](https://tva1.sinaimg.cn/large/e6c9d24ely1h2yw9gdynkj21b40os3z5.jpg)

![截屏2022-06-06 22.30.52](https://tva1.sinaimg.cn/large/e6c9d24ely1h2yw9qoscej21as0oe74u.jpg)

![截屏2022-06-06 22.31.07](https://tva1.sinaimg.cn/large/e6c9d24ely1h2ywa0grakj21ao0oa0t8.jpg)

![截屏2022-06-26 17.21.09](https://tva1.sinaimg.cn/large/e6c9d24ely1h3lrpy6u8vj21c00u0wgi.jpg)

![截屏2022-06-26 17.21.28](https://tva1.sinaimg.cn/large/e6c9d24ely1h3lrq0ue2cj21c00u0tao.jpg)

![截屏2022-06-06 22.31.23](https://tva1.sinaimg.cn/large/e6c9d24ely1h2ywaathnij21au0ocwez.jpg)



