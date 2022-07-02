<?php
namespace app\index\controller\personal;
use app\index\model\Hesuan;
use app\index\model\UserVaccines;
use think\Request;
use app\index\model\UserInfo;
use app\index\controller\Common;



# 修改前端表单样式
# 删除疫苗不能重复提交的逻辑，可以在选择第二针之后，再更新自己的疫苗为第一针
# 增加核酸谎报提醒
# 有日志记录功能，记录本人更新了哪个字段
# 本地与云开发的跳转链接统一格式：使用./ 或者 ../

class Updatehesuan extends Common
{
    public function index()
    {
        $user = userinfo::get($this->auth->getUserid());
        $this->assign('no',$user->no);
        $this->assign('name',$user->name);
        $this->assign('vaccines',$user->vaccines);
        $this->assign('last_hesuan',$user->last_hesuan);

        $vaccines = uservaccines::get($this->auth->getUserid());
        $this->assign('d1', $vaccines->date_1);
        $this->assign('d2', $vaccines->date_2);
        $this->assign('d3', $vaccines->date_3);
        $this->assign('l1', $vaccines->place_1);
        $this->assign('l2', $vaccines->place_2);
        $this->assign('l3', $vaccines->place_3);

        return $this->fetch();
    }

    public function updatevaccines(){  // 修改疫苗针数
        $request = Request::instance();
        // 插入info表格
        $user = userinfo::get($this->auth->getUserid());
        $oldvaccines = $user->vaccines;
        $newvaccines = $request->param("vstatus");
        $vdate = $request->param("vdate");
        $vloc = $request->param("vloc");
        if ($newvaccines == null) {
            return $this-> error('更新失败：请填写疫苗接种剂次');
        }

        if ($newvaccines != 0){
            if ($vdate == null) {
                return $this-> error('更新失败：请填写疫苗接种日期');
            }
            if (strtotime($vdate) > strtotime(date("Y-m-d"))) {
                return $this-> error('更新失败：疫苗接种日期不能超过今天日期');
            }
            if ($vloc == null) {
                return $this-> error('更新失败：请填写疫苗接种地点');
            }
        }
        
        $user->data([
            'vaccines' =>  $newvaccines,
        ]);
        $user->save();

        // 插入vaccines表格
        $vaccines = uservaccines::get($this->auth->getUserid());
        switch ($newvaccines) {
            case 0:
                $vaccines->data([
                    'date_1' => '',
                    'place_1' => '',
                    'date_2' => '',
                    'place_2' => '',
                    'date_3' => '',
                    'place_3' => '',
                ]);
                break;
            case 1:
                $vaccines->data([
                    'date_1' => $vdate,
                    'place_1' => $vloc,
                    'date_2' => '',
                    'place_2' => '',
                    'date_3' => '',
                    'place_3' => '',
                ]);
                break;
            case 2:
                $vaccines->data([
                    'date_2' => $vdate,
                    'place_2' => $vloc,
                    'date_3' => '',
                    'place_3' => '',
                ]);
                break;
            case 3:
                $vaccines->data([
                    'date_3' => $vdate,
                    'place_3' => $vloc,
                ]);
                break;
        }
        $check = $vaccines->save();
        if ($check !== false) {
            return $this-> success('更新个人疫苗接种情况成功');
        } else {
            return $this-> error('更新失败');
        }

    }

    public function updatehesuan(){  // 增加核酸信息
        $request = Request::instance();
        // 插入info表格
        $userId = $this->auth->getUserid();
        $time = $request->param("time");
        $where = $request->param("where");
        if ($time == null) {
            return $this-> error('更新失败：请填写核酸检测时间');
        }
        if (strtotime($time) > strtotime(date("Y-m-d"))) {
            return $this-> error('更新失败：核酸检测日期不能超过今天日期');
        }
        if ($where == null) {
            return $this-> error('更新失败：请填写核酸检测地点');
        }


        $user = userinfo::get($userId);

        $last_hesuan = $user->last_hesuan;
        if ($time > $last_hesuan) {
            $user->data([
                'last_hesuan' =>  $time,
            ]);
            $user->save();
        }

        $data = hesuan::create([
            'user_id' =>  $userId,
            'cov_time' =>  $time,
            'cov_location' =>  $where,
        ]);

        $check = $data->save();

        if ($check !== false) {
            return $this-> success('更新个人核酸检测信息成功');
        } else {
            return $this-> error('更新失败');
        }

    }
}