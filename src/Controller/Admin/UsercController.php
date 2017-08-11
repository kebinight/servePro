<?php
namespace App\Controller\Admin;



/**
 * Userc Controller
 */
class UsercController extends AppController
{
    /**
     * 登录
     */
    public function login() {
        if ($this->request->is(['POST'])) {
            $account = $this->request->data('account');
            $pwd = $this->request->data('pwd');
            //带有redirect_url参数的登录链接则需要在登录完成后跳转到该链接
            $cb = $this->request->query('cb');
            $data = [
                'account' => $account,
                'pwd' => $pwd,
                'redirect' => $cb
            ];
            $this->Common->loginHandle($data);
        }
    }
}
