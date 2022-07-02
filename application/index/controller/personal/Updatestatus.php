<?php
namespace app\index\controller\personal;
use app\index\model\Log;
use app\index\model\RbacUserHasRoles;
use app\index\model\UserContact;
use think\Db;
use think\Request;
use app\index\model\UserInfo;
use app\index\controller\Common;
use app\index\model\Status;

# 有日志记录功能，记录本人更新了个人状态
# 本地与云开发的跳转链接统一格式：使用./ 或者 ../

class Updatestatus extends Common
{
    public function index()
    {
        //由于状态项被放入user_contact表中，所以这里修改了对应代码
        $contact = UserContact::get($this->auth->getUserid());
        $userinfo = userinfo::get($this->auth->getUserid());
        //通过查询RbacUserHasRoles表，判断用户身份
        $userrole = RbacUserHasRoles::get($this->auth->getUserid());
        $this->assign('role',$userrole->role_id);
        $this->assign('no',$userinfo->no);
        $this->assign('name',$userinfo->name);

        //使用这种写法，即使修改了数据库信息，也能正确显示数据，避免重复修改代码。
        $status = Status::get($contact->status);
        $this->assign('status',$status->status_name);

        //传递res对象数组到前端，后续对数组的处理由js完成，详情可见view对应的html文件。
        $res = Status::all();
        $this->assign("res",$res);
//        $i = 0;
//        foreach ($res as $key => $value){
//            $id = 'id'.$i;
//            $name = 'name'.$i;
//            //dump($value->toArray()["status_name"]);
//            $this->assign($id,$value->toArray()["status_id"]);
//            $this->assign($name,$value->toArray()["status_name"]);
//            $i++;
//        }
//        $this->assign("total",$i);

        return $this->fetch();
    }
    
    public function updatestatus(){
        // 更改个人状态
        // 状态项被放入user_contact表中，这里是调整后的代码
        $contact = UserContact::get($this->auth->getUserid());
        $request = Request::instance();
        //这里新增日志记录代码，当点击更新按钮时，将视为修改个人状态操作
        //判断是否更新了状态，如果选择的状态与原先一致，则不记录操作
        //$status_name0表示原先状态名，status_name1表示修改后的状态名
        $status_name0 = Status::get($contact->status);
        $status_name1 = Status::get($request->param("newstatus"));
        if($status_name1 == null){
            return $this-> error('更新失败：您尚未选择状态');
        }
        if($contact->status!=$request->param("newstatus")){
            $log = new Log;
            $log->data([
                'log_user_id0' => $this->auth->getUserid(),
                'log_type' => '修改',
                'log_user_id1' => $this->auth->getUserid(),
                'attribute_name' => '当前个人状态',
                'new_info' => '从'.$status_name0->status_name.'修改为'.$status_name1->status_name
            ]);
            $log->save();
        }
        $contact->data([
            'status' =>  $request->param("newstatus"),
        ]);
        $contact->save();
        return $this->success('更新个人状态成功');

    }
}