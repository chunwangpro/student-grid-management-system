<?php

/**
 * @author 张娇
 * @date 2022/6/7
 * @description 后端检查插入Hesuan表中的数据是否合法
 * */

namespace app\index\validate;

use think\Validate;

Class Hesuan extends Validate
{
    protected $today;
    protected $rule= [
        'user_id' => 'require|number|length:11',
        'cov_location' => 'require|max:100',
    ];


    /**
     * 设置日期检查规则：必填、是日期、日期小于等于今天
     */
    public function setDateRule(){
        $today = $this->getToday();
        $this->rule('cov_time', 'require|date|before:'.$today);
    }

    /**
     * @return mixed
     */
    private function getToday()
    {
        $this->today = date('Y-m-d');
        return $this->today;
    }
}
