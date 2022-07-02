<?php

/**
 * @author 张娇
 * @date 2022/6/22
 * @description 后端检查插入UserInfo表中的数据是否合法
 * */

namespace app\index\validate;

use think\Validate;

Class UserContact extends Validate
{
    protected $rule= [
        'phone' => ['require','regex'=>['/^(?:(?:\+|00)86)?1(?:(?:3[\d])|(?:4[5-7|9])|(?:5[0-3|5-9])|(?:6[5-7])|(?:7[0-8])|(?:8[\d])|(?:9[1|8|9]))\d{8}$/']],
        'email'=>'require|email|max:100',
        'status'=>'require|max:100',
        'contact_pid'=>'require|max:100',
        'address'=>'require|max:100',
        'professor'=>'require|max:100',
        'professor_phone'=>['require','regex'=>['/^(?:(?:\+|00)86)?1(?:(?:3[\d])|(?:4[5-7|9])|(?:5[0-3|5-9])|(?:6[5-7])|(?:7[0-8])|(?:8[\d])|(?:9[1|8|9]))\d{8}$/']],
        'parent'=>'require|max:100',
        'parent_phone'=>['require','regex'=>['/^(?:(?:\+|00)86)?1(?:(?:3[\d])|(?:4[5-7|9])|(?:5[0-3|5-9])|(?:6[5-7])|(?:7[0-8])|(?:8[\d])|(?:9[1|8|9]))\d{8}$/']],
        'del'=>'require|number|length:1',
    ];
}