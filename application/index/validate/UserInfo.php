<?php

/**
 * @author 张娇
 * @date 2022/6/22
 * @description 后端检查插入UserInfo表中的数据是否合法
 * */

namespace app\index\validate;

use think\Validate;

Class UserInfo extends Validate
{
    protected $rule= [
        'no' => 'require|max:30',
        'name'=>'require|max:100',
        'pinyin'=>'require|max:20',
        'sex'=>['require','regex'=>['/^男&|^女&/']],
        'birth'=>'require|date',
        'major'=>'require|max:100',
        'department'=>'require|max:100',
        'vaccines'=>'require|number|length:1',
        'last_hesuan'=>'require|date',
        'del'=>'require|number|length:1',
    ];

//    protected $message =[
//        'id.require' => 'id不能为空',
//        'id.number' => 'id必须为数字',
//        'id.length' => 'id必须为11位',
//        'no.require' => '工号/学号不能为空',
//        'no.max' => '工号/学号最多30位',
//        'name.require' => '姓名不能为空',
//        'name.max' => '姓名最多30位',
//        'sex.require' => '性别不能为空',
//        'sex.regex' => '性别只能为男或女',
//        'major.require'=>'院系不能为空',
//        'major.max'=>'院系名最多为100个字节',
//        'professor.require'=>'导师不能为空',
//        'professor.max'=>'导师姓名不能超过100字节',
//        'professor_phone.require'=>'导师电话不能为空',
//        'professor_phone.number'=>'导师电话必须为数字',
//        'professor_phone.max'=>'导师电话不能超过100字节',
//        'department.require'=>'所属学苑不能为空',
//        'department.max'=>'学苑名最多为100个字节',
//        'role_id.require'=>'身份不能为空',
//        'role_id.number'=>'身份应为数字',
//        'role_id.length'=>'身份只能为一个数字',
//        'vaccines.require'=>'疫苗接种剂数不能为空',
//        'vaccines.number'=>'疫苗接种剂数应为数字',
//        'vaccines.length'=>'疫苗接种剂数只能为一个数字',
//        'birth.require'=>'出生日期不能为空',
//        'birth.date'=>'出生日期应为日期格式',
//        'phone.require'=>'联系电话不能为空',
//        'phone.number'=>'联系电话应为数字',
//        'phone.length'=>'联系电话应为11位'
//    ];

}
