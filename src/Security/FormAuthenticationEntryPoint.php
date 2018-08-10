<?php
/**
 * Created by PhpStorm.
 * User: wangchao
 * Date: 10/08/2018
 * Time: 10:22 AM
 */

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 *
 * Modified from vendor/symfony/symfony/src/Symfony/Component/Security/Http/EntryPoint/FormAuthenticationEntryPoint.php
 *
 * FormAuthenticationEntryPoint starts an authentication via a login form.
 *
 */
class FormAuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    private $loginPath;
    private $useForward;
    private $httpKernel;
    private $httpUtils;
    private $translator;

    /**
     * Constructor.
     *
     * @param TranslatorInterface $translator
     * @param HttpKernelInterface $kernel
     * @param HttpUtils $httpUtils An HttpUtils instance
     * @param string $loginPath The path to the login form
     * @param bool $useForward Whether to forward or redirect to the login form
     */
    public function __construct(TranslatorInterface $translator, HttpKernelInterface $kernel, HttpUtils $httpUtils, $loginPath, $useForward = false)
    {
        $this->translator = $translator;
        $this->httpKernel = $kernel;
        $this->httpUtils = $httpUtils;
        $this->loginPath = $loginPath;
        $this->useForward = (bool)$useForward;
    }

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
//        dump("未登录导致的权限不够，开始跳转登录页去登录");die;
        if ($this->useForward) {
            $subRequest = $this->httpUtils->createRequest($request, $this->loginPath);

            $response = $this->httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
            if (200 === $response->getStatusCode()) {
                $response->headers->set('X-Status-Code', 401);
            }

            return $response;
        }

        return $this->httpUtils->createRedirectResponse($request, $this->loginPath);
    }
}