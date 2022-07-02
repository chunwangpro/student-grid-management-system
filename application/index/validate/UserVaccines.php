<?php

/**
 * @author 张娇
 * @date 2022/6/22
 * @description 后端检查插入Hesuan表中的数据是否合法
 * */

namespace app\index\validate;

use think\Validate;

Class UserVaccines extends Validate
{
    protected $rule = [
        'date_1' => 'require|date',
        'place_1' => 'require|max:100',
        'date_2' => 'require|date',
        'place_2' => 'require|max:100',
        'date_3' => 'require|date',
        'place_3' => 'require|max:100',
        'del'=>'require|number|length:1',
    ];
}