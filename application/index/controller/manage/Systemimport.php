<?php
namespace app\index\controller\manage;
use app\index\controller\Common;
use app\index\model\RbacUser;
use app\index\model\UserInfo;
use app\index\model\RbacUserHasRoles;
use think\Db;
use think\Request;

# Excel 批量导入功能
# 提供一个模板可供下载
# 要求规定一些必填字段，当必填字段为空时，导入失败，并提示哪一行数据失败
# 前端要提示管理员，如果出现失败，例如第十行数据出错，那么在第二次导入时要删去前九行数据
# 日志记录功能，记录导入了多少条数据，数据的id是什么范围
# 本地与云开发的跳转链接统一格式：使用./ 或者 ../


# 要同时插入5个表
# 学生信息表 user_info
# 用户登录表 rbac_user
# 用户角色表 rbac_user_has_roles
# 用户联系信息表 user_contact
# 用户疫苗信息 user_vaccines


class Systemimport extends Common
{
    public function index()
    {
        return $this->fetch();
    }


    public function import()
    {
        //
        $msg = "文件处理失败";

        if (!empty($_FILES)) {
            // 获取表单上传文件
            $file = request()->file('file');
            if (!empty($file)) {
                // 移动到 /excel_uploads/ 目录下,该目录对外不可见(安全起见)

                $info = $file->validate(['size' => 10240000, 'ext' => 'xls,xlsx'])
                    ->rule(function(){
                        return date('Y-m-d-H-m-s-').$this->auth->getUserID().'-'.md5(microtime(true));
                    })
                    ->move(ROOT_PATH . DS . 'excel_uploads');
                if ($info) {
                    $ext = $info->getExtension();
                    $filename = ROOT_PATH . DS . 'excel_uploads' . DS . $info->getSaveName();

                    vendor("PHPExcel"); // 导入PHPExcel类库
                    $PHPExcel = new \PHPExcel(); // 创建PHPExcel对象，注意，不能少了

                    if ($ext == 'xls') { // 如果excel文件后缀名为.xls，导入Excel5类
                        vendor("PHPExcel.PHPExcel.Reader.Excel5");
                        $PHPReader = new \PHPExcel_Reader_Excel5();
                    } else if ($ext == 'xlsx') {
                        vendor("PHPExcel.PHPExcel.Reader.Excel2007");
                        $PHPReader = new \PHPExcel_Reader_Excel2007();

                    }

                    $PHPExcel = $PHPReader->load($filename);
                    $currentSheet = $PHPExcel->getSheet(0); // 获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
                    $allColumn = $currentSheet->getHighestColumn(); // 获取总列数
                    $allRow = $currentSheet->getHighestRow(); // 获取总行数

                    //先判断第一行的列标题内容是否正确,已确定是否使用了模板,如果不是,报错
                    $data = array();

                    //由于模板的列超过Z，因此需要手动处理
                    for ($k = 'A'; $k <= $allColumn; $k++) {
                        // 读取单元格
                        $data[$k] = $PHPExcel->getActiveSheet()->getCell($k . '1')->getValue();
                    }

                    if ($data['A'] != '学号/工号' || $data['B'] != '姓名'  || $data['C'] != '性别' || $data['D'] != '出生日期' ||
                        $data['E'] != '院系' || $data['F'] != '学苑' || $data['G'] != '用户身份'
                    ) {
                        $this->error("请按照模板要求导入数据");
                    }

                    $total_count = $allRow - 1; //数据总行数,如果限制Excel表格的行数,可通过此变量来控制
                    $import_success_count = 0;
                    $import_success_add_count = 0;
                    $import_success_update_count = 0;
                    $last_num = 0; //记录最后一次成功处理的数据所在的行数
                    $last_name = "";//记录最后一次成功处理的学生姓名,以便提醒用户进行检查

                    $invalid_rows = array();        //用来记录不合法的数据位置

                    //从数据的起始行开始依次读取数据,并处理
                    for ($i = 2; $i <= $allRow; $i++) {

                        $row = $i-1;
                        // 获取数据
                        try {
                            $no = trim($PHPExcel->getActiveSheet()->getCell("A" . $i)->getValue());
                            $name = trim($PHPExcel->getActiveSheet()->getCell("B" . $i)->getValue());
                            $sex = trim($PHPExcel->getActiveSheet()->getCell("C" . $i)->getValue());
                            $birth = trim($PHPExcel->getActiveSheet()->getCell("D" . $i)->getValue());
                            $major = trim($PHPExcel->getActiveSheet()->getCell("E" . $i)->getValue());
                            $department = trim($PHPExcel->getActiveSheet()->getCell("F" . $i)->getValue());
                            $role = trim($PHPExcel->getActiveSheet()->getCell("G" . $i)->getValue());
                            // 默认在校
                            $status = '在校（大兴）';
                            //默认del为0
                            $del = 0;


                            // 根据输入判断几个属性的值
                            $role_id = Db::table('rbac_role')
                                -> where('name', $role)
                                -> find();
                            $major_id = Db::table('major')
                                -> where('name', $major)
                                -> find();

                            $department_id = Db::table('department')
                                -> where('name', $department)
                                -> find();
                            $status_id = Db::table('status')
                                ->where('status_name', $status)
                                ->find();

                            //检查必要的数据项，若缺少数据，则将次行纳入非法行
                            if( $no==null || $name == null || $sex == null || $birth == null ||
                                $major_id == null || $role_id == null || $department_id == null || $status_id == null){
                                array_push($invalid_rows, $row);
                                throw new \PHPExcel_Exception("invalid rows");
                            }

                        } catch (\PHPExcel_Exception $e) {
                            continue;
                        }
                        //生成MD5盐
                        $salt = $this->genSalt();
                        // 讲数据插入相应的表中
                        $rbac_user['username'] = $name;
                        $rbac_user['password'] = $this->auth->passwordMD5($no, $salt);
                        $rbac_user['modify'] = 1;
                        $rbac_user['salt'] = $salt;

                        $rbac_user_has_role['role_id'] = $role_id['id'];

                        $user_info['no'] = $no;
                        $user_info['name'] = $name;
                        $user_info['sex'] = $sex;
                        $user_info['birth'] = $birth;
                        $user_info['major'] = $major_id['id'];
                        $user_info['department'] = $department_id['id'];
                        $user_info['del'] = $del;


                        $user_contact['status'] = $status_id['status_id'];




                        //根据学号判断该条记录是否存在，决定新增或更新
                        $res_stu_p = Db::table('user_info')
                            ->where('no', '=', $no)
                            ->find();

                        //如果查询结果存在,则需要根据实际情况考虑是否导入
                        if ($res_stu_p != null) {
                            // 若查询结果存在则更新id指向存在的那条记录
                            $id = $res_stu_p['id'];

                            $rbac_user['user_id'] = $id;
                            $rbac_user_has_role['user_id'] = $id;
                            $user_info['id'] = $id;
                            $user_contact['id'] = $id;
                            $user_vaccines['id'] = $id;

                            //此处用事务机制,以保证数据写入完整
                            Db::startTrans();
                            try {
                                Db::table('rbac_user')
                                    ->where('user_id', '=', $id)
                                    ->update($rbac_user);

                                Db::table('rbac_user_has_roles')
                                    ->where('user_id', '=', $id)
                                    ->update($rbac_user_has_role);

                                Db::table('user_info')
                                    ->where('id', '=', $id)
                                    ->update($user_info);

                                Db::table('user_contact')
                                    ->where('id', '=', $id)
                                    ->update($user_contact);

                                Db::table('user_vaccines')
                                    ->where('id', '=', $id)
                                    ->update($user_vaccines);

                                Db::commit();

                            } catch (\Exception $e) {
                                // 回滚事务
                                //echo $e;
                                Db::rollback();
                                break;
                            }
                            $import_success_count = $import_success_count + 1;
                            $import_success_update_count = $import_success_update_count + 1;

                        } else { //如果查询结果不存在,则直接添加新的记录

                            //此处用事务机制,以保证数据写入完整
                            // 启动事务
                            Db::startTrans();
                            try {

                                # 要同时插入五个表
                                # 学生信息表 user_info
                                # 学生联系人表 user_contact
                                # 用户登录表 rbac_user
                                # 用户角色表 rbac_user_has_roles
                                # 学生疫苗表 user_vaccines

                                Db::table('rbac_user')->insert($rbac_user);
                                Db::table('rbac_user_has_roles')->insert($rbac_user_has_role);
                                Db::table('user_info')->insert($user_info);
                                Db::table('user_contact')->insert($user_contact);
                                Db::table('user_vaccines')->insert($user_vaccines);

                                // 提交事务
                                Db::commit();
                            } catch (\Exception $e) {
                                // 回滚事务
                                //echo $e;
                                var_dump($e);
                                Db::rollback();
                                break;
                            }
                            $import_success_count = $import_success_count + 1;
                            $import_success_add_count = $import_success_add_count + 1;
                        }


                    }


                    if ($import_success_count < $total_count) {
                        // 用来显示错误的行数
                        $invalid = '';
                        for($i=0; $i<sizeof($invalid_rows); $i++){
                            if( $i != (sizeof($invalid_rows)-1)) {
                                $invalid = $invalid . $invalid_rows[$i] . ', ';
                            }
                            else{
                                $invalid = $invalid.$invalid_rows[$i];
                            }
                        }

                        $msg = "本次成功导入数据：" . $import_success_count . "条记录成功。其中(新增：" . $import_success_add_count . "，更新" . $import_success_update_count . ")<br> 第" . $invalid . "行存在错误或缺少数据，请修正！";
                        $this->error($msg);
                    } else {
                        $msg = "本次成功导入数据：" . $import_success_count . "条记录成功。其中(新增：" . $import_success_add_count . "，更新" . $import_success_update_count . ")";
                        $this->success($msg);
                    }
                } else {
                    // 上传失败获取错误信息
                    $msg = $file->getError();
                    $this->error($msg);
                }
            }
        }


    }
    public function genSalt() {
        /* openssl_random_pseudo_bytes(8) Fallback */
        $seed = '';
        for($i = 0; $i < 8; $i++) {
            $seed .= chr(mt_rand(0, 255));
        }
        //产生一个6-8位的随机字符串来作为盐
        $salt = substr(strtr(base64_encode($seed), '+', '.'), 0, mt_rand(6,8));
        /* Return */
        return $salt;
    }
    public function download(){

        $file_dir = ROOT_PATH . DS . 'excel_template' . DS . 'template.xlsx';

        // 检查文件是否存在
        if (! file_exists($file_dir) ) {
            $this->error('文件未找到');
        }else {
            // 打开文件
            $file1 = fopen($file_dir, "r");
            // 输入文件标签
            Header("Content-type: application/octet-stream");
            Header("Accept-Ranges: bytes");
            Header("Accept-Length:" . filesize($file_dir));
            Header("Content-Disposition: attachment;filename=" . $file_dir);
            ob_clean();     //
            flush();        // 可以清除文件中多余的路径名以及解决乱码的问题：
            //输出文件内容
            //读取文件内容并直接输出到浏览器
            echo fread($file1, filesize($file_dir));
            fclose($file1);
            exit();
        }
    }

}
