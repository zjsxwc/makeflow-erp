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
        $requestedUri = base64_encode($request->getUri());
        return new RedirectResponse("/access-deny?requestedUri=".$requestedUri);
    }
}