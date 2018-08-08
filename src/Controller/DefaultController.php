<?php

namespace App\Controller;

use App\Makeflow\PaoMianMakeflow\Entity\Note;
use App\Makeflow\WorkspaceContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("", name="default")
     */
    public function index(Request $request)
    {
        return $this->render('default/index.html.twig', []);
    }

    /**
     * @Route("/access-deny", name="access-deny")
     */
    public function accessDeny(Request $request)
    {
        $requestedUri = urldecode($request->query->get("requestedUri"));
        return $this->render('default/access_deny.html.twig', [
            'requestedUri' => $requestedUri
        ]);
    }
}
