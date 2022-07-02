<?php
namespace app\index\controller;

class Index extends Common
{
    public function index()
    {
        #return 'iCampus!';
        $this->redirect('Login/index');
    }
}
