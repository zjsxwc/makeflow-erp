<?php

namespace App\Security;

use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Request;


class UserAuthenticationFailureHandler extends DefaultAuthenticationFailureHandler
{

    /** @var null|TranslatorInterface  */
    protected $translator = null;

    /** @var \Doctrine\Common\Cache\Cache|null  */
    protected $cache = null;

    /**
     * UserAuthenticationFailureHandler constructor.
     * @param TranslatorInterface $translator
     * @param HttpKernelInterface $httpKernel
     * @param HttpUtils $httpUtils
     * @param array $options
     * @param LoggerInterface|null $logger
     */
    public function __construct(TranslatorInterface $translator, HttpKernelInterface $httpKernel, HttpUtils $httpUtils, array $options = array(), LoggerInterface $logger = null)
    {
        $this->translator = $translator;

        parent::__construct($httpKernel,$httpUtils,$options,$logger);
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
//        dump("登录失败，返回登录失败消息");die;
        return parent::onAuthenticationFailure($request, $exception);
    }

}