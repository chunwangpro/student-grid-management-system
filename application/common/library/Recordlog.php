<?php
//系统日志记录功能

namespace app\index\controller;

use app\index\model\Log;
use think\Session;
use app\index\model\RbacMenu;
use app\index\model\RbacUser;
use app\index\model\UserInfo;
use app\index\model\RbacPermission;
use app\index\model\RbacRoleHasPermissions;
use app\index\model\RbacUserHasRoles;
use think\Request;


# 本功能是记录系统日志功能
# 记录日志时间、操作人id、被操作人id、日志类型、操作的字段、更新的信息
# 其中，日志时间插入空值，数据库会自动记录插入的时间
# 日志类型有以下几种：
# 登录：用户登录成功后，要记录上述信息，操作人与被操作人id均为自己的id，操作字段留空，更新信息记录用户的IP地址、浏览器版本、客户端版本
# 导入：超级管理员在系统中上传文件导入，不论导入成功或者失败都要记录，此时要记录操作人id为超级管理员id，被操作人为空，操作字段为空，更新的信息为本次插入的用户id编号范围，例如（id 30 - id 40），在插入前查询一次数据库中用户的最后一个id，插入完毕后查询数据库中用户的最后一个id，分别记录下来写到new_info 里
# 查询：所有用户查询自己的个人信息时要记录，操作人与被操作人都是自己，点开查询个人详细信息时、点开更新个人联系方式时要记录、点开更新个人状态时要记录，点开更新个人核酸疫苗信息时要记录，点开修改密码时要记录，总之全都要记下来
# 修改：学生修改自己的联系方式、当前状态、核酸疫苗信息，并且修改成功后，记录修改的字段，记录修改后的值。管理员修改别人的信息的时候，也要记录，记录修改人和被修改人的id，修改的字段有多个时，每个字段记录一条日志，例如我修改了自己的手机号和邮箱，修改成功后，日志中会新增两条记录。
# 删除
class Recordlog
{
    public function index()
    {
        return $this -> fetch();
    }
    public function loginRec($id){
        // 因为操作人与被操作人id均为自己的id，因此只用传入一个id参数即可
        // $id : 为登录用户的id

        // 用来存储log数据

        // 通过request请求ip地址
        $IP = request()->ip();
        // 通过http头中的user-agent字段来记录浏览器版本与客户端版本
        $user_agent = request()->header('user-agent');
        // 这里使用了header里面的信息来填充浏览器信息和客户端版本，但由于数据表较长，
        // new_info 字段长度需要重新调整

        //连接ip地址、浏览器信息以及客户端版本字段
        $info = $IP.', '.$user_agent;

        // 将数据保存到相应的表中
        $log = new Log;
        $log ->data([
            'log_user_id0' => $id,
            'log_user_id1' => $id,
            'log_type' => '登录',
            'attribute_name' => '',
            'new_info' => $info,
        ]);
        $log->save();
    }

    public function queryRec($id, $table){
        // 因为操作人与被操作人id均为自己的id，因此只用传入一个id参数即可
        // $id : 为发起查询人的id
        // $table: 查询表的名称

        // 将数据保存到相应的表中
        $log = new Log;
        $log ->data([
            'log_user_id0' => $id,
            'log_user_id1' => $id,
            'log_type' => '查询',
            'attribute_name' => $table,
            'new_info' => '',
        ]);
        $log -> save();
    }
    public function reviseRec($user_0, $user_1, $attribute_names, $attribute_contents){
        // 传入了四个参数，前两个是操作者与被操作者的id，第三个为保存了更改字段名称的数组，第四个为相应更改字段的内容
        // user_0: 操作人的id
        // user_1: 被操作人的id
        // attribute_names: 被修改的属性数组
        // attribute_contents: 相应的被修改的内容

        //使用for循环遍历”被修改的属性数组“与”相应的被修改的内容“
        for ($i=0; $i < sizeof($attribute_names); $i++){

            // 将数据保存到相应的表中
            $log = new Log;
            $log -> data([
                'log_user_id0' => $user_0,
                'log_user_id1' => $user_1,
                'log_type' => '修改',
                'attribute_name' => $attribute_names[$i],
                'new_info' => $attribute_contents[$i],
            ]);
            $log -> save();
        }
    }

    public function removeRec($user_0, $user_1){
        // 删除操作只需要记录操作人与被操作人的id
        // user_0: 操作人的id
        // user_1: 被操作人的id

        // 将数据保存到相应的表中
        $log = new Log;
        $log -> data([
            'log_user_id0' => $user_0,
            'log_user_id1' => $user_1,
            'log_type' => '删除',
            'attribute_name' => '',
            'new_info' => '',
        ]);
        $log -> save();
    }
    public function importRec($superAdminId, $successNum, $start_id, $end_id){
        // 导入操作需要将超级管理员的id传入，作为操作人与被操作人；
        // attribute_name 设置为共导入了多少条数据，new_info 更新的信息为本次插入的用户id编号范围

        // 将数据保存到相应的表中
        $log = new Log;
        $log -> data([
            'log_user_id0' => $superAdminId,
            'log_user_id1' => $superAdminId,
            'log_type' => '导入',
            'attribute_name' => $successNum,
            'new_info' => 'id ' . $start_id . ' - id ' . $end_id,
        ]);
        $log -> save();
    }
    public function exportRec($superAdminId, $page, $info){
        // 导出操作需要将超级管理员id传入，作为操作人与被操作人
        // page为导出表的名字，info为额外的新的信息

        // 将数据保存到相应的表中
        $log = new Log;
        $log -> data([
            'log_user_id0' => $superAdminId,
            'log_user_id1' => $superAdminId,
            'log_type' => '导出',
            'attribute_name' => $page,
            'new_info' => $info,
        ]);
        $log -> save();
    }


    public function searchRec($id, $page){
        // 查询炒作需要将操作人的id传入，作为操作人与被操作人
        // page为所查询的表的名字

        $log = new Log;
        $log -> data([
            'log_user_id0' => $id,
            'log_user_id1' => $id,
            'log_type' => '查询',
            'attribute_name' => $page,
            'new_info' => '',
        ]);
        $log -> save();
    }
}