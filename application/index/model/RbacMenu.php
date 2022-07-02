<?php
//Todo 添加文件功能说明

namespace app\index\model;

use app\index\library\Menu;
use think\Model;

class RbacMenu extends Model
{
    public static function tree()
    {
        $data = self::order('sort' , 'asc')->select();
        
        return new Menu($data);
    }
}