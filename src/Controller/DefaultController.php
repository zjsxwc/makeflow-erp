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
     * @Route("", name="workflow_admin_dashboard")
     */
    public function index(Request $request)
    {
        $em = $this->get("doctrine.orm.entity_manager");
        $cdnUrl = $this->getParameter("cdn_url");
//        dump($em, $cdnUrl);die;

        $mm = $this->get("App\Makeflow\MakeflowManager");

        $paoMianMakeflow = $mm->getMakeflow("PaoMian");

        $mayWorkspaceList = $paoMianMakeflow->getWorkspaces();
        if (isset($mayWorkspaceList[0])) {
            $workspace = $mayWorkspaceList[0];
        }else {
            $workspace = $paoMianMakeflow->createWorkspace(1, strval(time()));
        }

//        dump($workspace->getBuildDate()->getTimezone(), $workspace->getBuildDate()->getTimestamp(), $workspace->getBuildDate()->format('Y-m-d H:i:s'), date_default_timezone_get());die;


//        $context = $this->get("App\Makeflow\WorkspaceContextFactory")->getContext($workspace);
//
//        $cps = $context->getCurrentProcessingPlaces();
//
//        $onePlace = $cps[0];
//
//        $onePlace->bindUsersToPlace([3,4,2,1]);
//
//        dump($cps, $workspace,$onePlace->getUsers());die;

        $paoMianPlaces = $paoMianMakeflow->getPlaces();

        $buyPaoMianPlace = $paoMianPlaces["BuyPaoMian"];
        return $buyPaoMianPlace->processAction($request, $workspace);

//        dump($paoMianMakeflow, $paoMianMakeflow->getMakeflowConfig(), $paoMianMakeflow->getPlaces());die;
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }
}
