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
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }
}
