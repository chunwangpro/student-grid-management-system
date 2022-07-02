<?php

/**
 * @author 张娇
 * @date 2022/6/7
 * @description 后端检查插入RbacUser表中的数据是否合法
 * */

/**
使用方法：要验证插入数据表RbacUser的数据是否合法，则使用以下代码
$validate = Loader::validate('RbacUser');
if(!$validate->check($data)){
    dump($validate->getError());
}
其中，$data应为数组，数组内的变量名和$rule对应，
如RbacUser验证的数据$data=['username'=>'anyvalue','password'=>'anyvalue']
*/


namespace app\index\validate;

use think\Validate;

Class RbacUser extends Validate
{
    protected $rule= [
        'username' => 'require|max:100',
        'password' => 'require|min:6',
    ];

    protected $message =[
        'username.require' => '用户名不能为空',
        'username.max' => '用户名最多为32个字符',
        'password.require' => '密码不能为空',
        'password.min' => '密码不能少于6位'
    ];
}
