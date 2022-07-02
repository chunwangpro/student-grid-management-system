<?php

namespace app\index\library;

use think\Session;
use app\index\model\RbacMenu;
use app\index\model\RbacUser;
use app\index\model\UserInfo;
use app\index\model\RbacPermission;
use app\index\model\RbacRoleHasPermissions;
use app\index\model\RbacUserHasRoles;
use think\Db;

class Searchauth
{
    protected $sessionName = 'search';


    public function setSearchSession($caller, $pinyin, $name, $num, $tutor, $major, $department, $status,
    $major_data, $department_data, $status_data, $role_data, $cov_time_1, $cov_time_2,$quick, $manager, $searchmode
    ){
        //$caller请传入
        Session::set($this->sessionName,[
          'caller' => $caller,
          'timestamp' => time(),
          'pinyin' => $pinyin,
          'name' => $name,
          'num' => $num,
          'tutor' => $tutor,
          'major' => $major,
          'department' => $department,
          'status' => $status,
        
          'major_data' => $major_data,
          'department_data' => $department_data,
          'status_data' => $status_data,
          'role_data' => $role_data,
          'cov_time_1' => $cov_time_1,
          'cov_time_2' => $cov_time_2,

          'quick' => $quick,
          'manager' => $manager,
          'searchmode' => $searchmode

        ]);
      }


      public function getCaller()
      {
          return Session::get($this->sessionName . '.caller');
      }
      public function getTimestamp()
      {
          return Session::get($this->sessionName . '.timestamp');
      }
      public function getPinyin()
      {
          return Session::get($this->sessionName . '.pinyin');
      }
      public function getName()
      {
          return Session::get($this->sessionName . '.name');
      }
      public function getNum()
      {
          return Session::get($this->sessionName . '.num');
      }
      public function getTutor()
      {
          return Session::get($this->sessionName . '.tutor');
      }
      public function getMajor()
      {
          return Session::get($this->sessionName . '.major');
      }
      public function getDepartment()
      {
          return Session::get($this->sessionName . '.department');
      }
      public function getStatus()
      {
          return Session::get($this->sessionName . '.status');
      }

      public function getMajorData()
      {
          return Session::get($this->sessionName . '.major_data');
      }
      public function getDepartmentData()
      {
          return Session::get($this->sessionName . '.department_data');
      }
      public function getStatusData()
      {
          return Session::get($this->sessionName . '.status_data');
      }
      public function getRoleData()
      {
          return Session::get($this->sessionName . '.role_data');
      }
      public function getCovTime1()
      {
          return Session::get($this->sessionName . '.cov_time_1');
      }
      public function getCovTime2()
      {
          return Session::get($this->sessionName . '.cov_time_2');
      }
      public function getQuick()
      {
          return Session::get($this->sessionName . '.quick');
      }

      public function getManager()
      {
          return Session::get($this->sessionName . '.manager');
      }

      public function getSearchMode()
      {
          return Session::get($this->sessionName . '.searchmode');
      }






    

    protected static $instance; //Todo 添加语法及注释说明

    public static function getInstance()  //Todo 添加语法及注释说明
    {
        if(is_null(self::$instance)){
            self::$instance = new static();  //Todo 添加语法及注释说明
        }
        return self::$instance;
    }


    protected $error;

    public function getError()
    {
        return $this->error;
    }

    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }



}