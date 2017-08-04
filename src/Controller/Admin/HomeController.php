<?php
namespace App\Controller\Admin;


class HomeController extends AppController
{

    /**
    * Index method
    * @return void
    */
    public function index()
    {
        $this->Common->dealReturn(true, 'hello world');
    }
}
