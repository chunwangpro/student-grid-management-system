<?php
namespace app\index\controller\personal;

use think\Db;
use think\Request;
use app\index\model\UserInfo;
use app\index\model\UserContact;
use app\index\controller\Common;

# 学生可以更新自己的联系方式、邮箱 、紧急联系人、紧急联系人电话、居住地址
# 教师可以更新自己的联系方式、邮箱
# 页面显示个人的所有信息，实现单个字段修改，例如只修改手机号
# 如果用户提交的所有字段均和数据库中的原始数据相同，提示用户您的联系信息未发生变化
# 修改成功后，提示用户修改了哪些字段
# 有日志记录功能，记录本人更新了哪些字段的信息
# 本地与云开发的跳转链接统一格式：使用./ 或者 ../

class Updateteachercontact extends Common
{
    public function index()
    {

        $list1 = userinfo::all();
        $this->assign('data', $list1);
        $user = userinfo::get($this->auth->getUserid());
        $this->assign('no', $user->no);
        $this->assign('name', $user->name);

        $contact = usercontact::get($this->auth->getUserid());
        $this->assign('phone', $this->check($contact->phone));
        $this->assign('email', $this->check($contact->email));

        return $this->fetch();

    }

    public function check($attr){
        if ($attr==null){
            return '点击修改';
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
    
    public function updateselfinfo()
    {  // 更改个人联系信息功能，电话、邮箱居住地址
        $request = Request::instance();
        // 插入contact表格
        $user = usercontact::get($this->auth->getUserid());
        // 检查所有字段都为空时，用户没有输入任何信息
        if (!$request->param("newphone") and !$request->param("newemail")) {
            return $this->error('更新失败：您尚未填写任何新信息');
        }
        // 实现对单个字段的更新，其他字段留空时，不会更新这些留空的字段
        $phone = $this->update("newphone", $user->phone, $request);
        $email = $this->update("newemail", $user->email, $request);
        $address = $this->update("newaddress", $user->address, $request);
        
        $user->data([
            'phone' => $phone,
            'email' => $email,
        ]);
        $result = $user->save();
        if($result){
            return $this->success('修改个人联系信息成功');
        }
        return $this->error('修改个人联系信息失败');
    }

}