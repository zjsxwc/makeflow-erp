<?php

namespace App\Twig;


use Symfony\Component\HttpFoundation\RequestStack;

class CommonExtension extends \Twig_Extension
{
    /** @var  string */
    protected $cdnAssetsBaseUrl;
    /** @var RequestStack  */
    protected $requestStack;
    /** @var  string */
    protected $version;

    public function __construct(RequestStack $requestStack, $cdnAssetsBaseUrl, $version)
    {
        $this->requestStack = $requestStack;
        $this->cdnAssetsBaseUrl = $cdnAssetsBaseUrl;
        $this->version = $version;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('asset_host', array($this, 'getAssetHost')),
            new \Twig_SimpleFunction('version', array($this, 'getVersion'))
        );
    }

    public function getAssetHost()
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($this->cdnAssetsBaseUrl) {
            return $this->cdnAssetsBaseUrl;
        } else {
            return $request->getSchemeAndHttpHost();
        }
    }

    public function getVersion()
    {
        return $this->version;
    }
}