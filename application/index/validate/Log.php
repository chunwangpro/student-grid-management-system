<?php

/**
 * @author 张娇
 * @date 2022/6/22
 * @description 后端检查插入Log表中的数据是否合法
 * */

namespace app\index\validate;

use think\Validate;

Class Log extends Validate
{
    protected $rule= [
        'log_user_id0' => 'require|number|length:11',
        'log_user_id1' => 'require|number|length:11',
        'log_type' => 'require|max:100',
        'attribute_name' => 'require|max:100',
        'new_info' => 'require|max:300',
    ];
}
