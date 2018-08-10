<?php

namespace App\Security;



use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;

class UserAuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler
{

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
//        dump("登录成功，返回登录成功消息");die;
        return parent::onAuthenticationSuccess($request, $token);
    }

}