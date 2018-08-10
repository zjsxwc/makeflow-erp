<?php
/**
 * Created by PhpStorm.
 * User: wangchao
 * Date: 08/08/2018
 * Time: 10:28 AM
 */

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
//        dump("已登录用户权限不够，开始跳转登录页去换账户登录");die;
        $requestedUri = urlencode($request->getUri());
        return new RedirectResponse("/access-deny?requestedUri=".$requestedUri);
    }
}