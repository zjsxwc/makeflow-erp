<?php

namespace App\Controller;

use App\Entity\User;
use App\Makeflow\AbstractPlace;
use App\Makeflow\Dashboard\Entity\PlaceUser;
use App\Makeflow\Dashboard\Entity\Workspace;
use App\Makeflow\Makeflow;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


/**
 * @Route("/makeflow-user")
 */
class MakeflowUserController extends Controller
{

    /**
     * @param Workspace $workspace
     * @param $placeName
     * @param $userId
     * @return array
     */
    private function getPlace(Workspace $workspace, $placeName, $userId)
    {
        $makeflowManager = $this->get("App\Makeflow\MakeflowManager");
        $makeflows = $makeflowManager->getMakeflows();
        $makeflowName = $workspace->getMakeflowName();
        if (!isset($makeflows[$makeflowName])) {
            throw new \LogicException(sprintf("Not valid makeflowName %s", $makeflowName), 1);
        }
        $makeflow = $makeflows[$makeflowName];
        $places = $makeflow->getPlaces();
        if (!isset($places[$placeName])) {
            throw new \LogicException(sprintf("Not valid placeName %s", $placeName), 2);
        }
        $place = $places[$placeName];
        if (!$place->isUserAllowedInPlace($userId)) {
            throw new \LogicException(sprintf("User not allowed in placeName %s", $placeName), 3);
        }
        return [$makeflow, $places, $place];
    }


    /**
     * @Route("/workspace/{id}/place/{placeName}/delete-prerequisites", name="makeflow_user_delete_prerequisites_of_workspace", methods={"POST"})
     * @param Request $request
     * @param Workspace $workspace
     * @param $placeName
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deletePrerequisitesOfPlaceNameInWorkspace(Request $request, Workspace $workspace, $placeName)
    {

        $prerequisitesStr = $request->request->get("prerequisites");
        $prerequisites = explode(",", $prerequisitesStr);

        /** @var UserInterface $currentUser */
        $currentUser = $this->getUser();
        $userId = $currentUser->getId();
        try {
            list($makeflow, $places, $place) = $this->getPlace($workspace, $placeName, $userId);
            $workspaceFactory = $this->get("App\Makeflow\WorkspaceContextFactory");
            $workspaceContext = $workspaceFactory->getContext($workspace);

            $workspaceContext->fromProcessingPlaceDeletePrerequisites($place, $prerequisites);
        } catch (\Throwable $throwable) {
            return $this->json([
                "code" => $throwable->getCode(),
                "message" => $throwable->getMessage()
            ]);
        }
        return $this->json([
            "code" => -1,
            "data" => [
                "workspace" => $workspace
            ]
        ]);
    }


    /**
     * @Route("/workspace/{id}/place/{placeName}", name="makeflow_user_process_place_of_workspace", methods={"GET","POST"})
     * @param Request $request
     * @param Workspace $workspace
     * @param $placeName
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function processPlaceOfWorkspace(Request $request, Workspace $workspace, $placeName)
    {
        /** @var UserInterface $currentUser */
        $currentUser = $this->getUser();
        $userId = $currentUser->getId();

        try {
            /** @var AbstractPlace $place */
            list($makeflow, $places, $place) = $this->getPlace($workspace, $placeName, $userId);

            $directory = $workspace->getDirectory();
            if (in_array($placeName, $directory)) { //如果这步工作已经被处理了，那么就不能处理，只能等待下一级主管回退
                return $this->json([
                    "code" => 4,
                ]);
            }

            $response = $place->processAction($request, $workspace);
        } catch (\Throwable $throwable) {
            return $this->json([
                "code" => $throwable->getCode(),
                "message" => $throwable->getMessage()
            ]);
        }

        return $response;
    }


    /**
     * @Route("/makeflow/{makeflowName}/place/{placeName}/visit", name="makeflow_user_visit_place", methods={"GET","POST"})
     * @param Request $request
     * @param $makeflowName
     * @param $placeName
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function visitPlace(Request $request, $makeflowName, $placeName)
    {
        /** @var UserInterface $currentUser */
        $currentUser = $this->getUser();
        $userId = $currentUser->getId();

        try {
            $makeflowManager = $this->get("App\Makeflow\MakeflowManager");
            $makeflows = $makeflowManager->getMakeflows();
            if (!isset($makeflows[$makeflowName])) {
                throw new \LogicException(sprintf("Not valid makeflowName %s", $makeflowName), 1);
            }
            $makeflow = $makeflows[$makeflowName];
            $places = $makeflow->getPlaces();
            if (!isset($places[$placeName])) {
                throw new \LogicException(sprintf("Not valid placeName %s", $placeName), 2);
            }
            $place = $places[$placeName];
            if (!$place->isUserAllowedInPlace($userId)) {
                throw new \LogicException(sprintf("User not allowed in placeName %s", $placeName), 3);
            }
            if (!$place->canVisit) {
                throw new \LogicException(sprintf("Place %s not allowed", $placeName), 4);
            }

            $response = $place->visitAction($request);
        } catch (\Throwable $throwable) {
            return $this->json([
                "code" => $throwable->getCode(),
                "message" => $throwable->getMessage()
            ]);
        }

        return $response;
    }


    /**
     * @Route("/makeflow/{makeflowName}/create-workspace", name="makeflow_user_create_workspace", methods={"POST"})
     * @param Request $request
     * @param $makeflowName
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createWorkspace(Request $request, $makeflowName)
    {
        $title = $request->request->get("title");
        $makeflowManager = $this->get("App\Makeflow\MakeflowManager");

        $makeflows = $makeflowManager->getMakeflows();

        if (!isset($makeflows[$makeflowName])) {
            return $this->json([
                "code" => 1,
            ]);
        }
        $makeflow = $makeflows[$makeflowName];

        /** @var UserInterface $currentUser */
        $currentUser = $this->getUser();
        $userId = $currentUser->getId();

        try {
            $workspace = $makeflow->createWorkspace($userId, $title);
        } catch (\Throwable $throwable) {
            return $this->json([
                "code" => $throwable->getCode(),
                "message" => $throwable->getMessage()
            ]);
        }

        $data = [
            "workspace" => $workspace
        ];
        return $this->json([
            "code" => -1,
            "data" => $data
        ]);
    }


    /**
     * @Route("/dashboard", name="makeflow_user_dashboard")
     */
    public function dashboard()
    {
        $makeflowManager = $this->get("App\Makeflow\MakeflowManager");
        $makeflows = $makeflowManager->getMakeflows();

        /** @var UserInterface $currentUser */
        $currentUser = $this->getUser();
        $userId = $currentUser->getId();

        $em = $this->get("doctrine.orm.entity_manager");
        $placeUserRepo = $em->getRepository("MakeflowDashboard:PlaceUser");
        /** @var PlaceUser[] $placeUserList */
        $placeUserList = $placeUserRepo->findBy([
            "userId" => $userId
        ]);

        /*
         * @var array $userInvolvedPlaces
         *   [
         *       "makeflowName1"  => [
         *           "makeflowName" => "makeflowName1",
         *           "placeNames" => [ "placeName1", "placeName2" ],
         *          "workspacePackages" => [
         *                  [
         *                     "makeflowName" => "makeflowName1",
         *                     "placeName" => "placeName1"
         *                     "workspace" =>  workspaceObj1
         *                  ],
         *          ]
         *       ],
         *   ]
        */
        $userInvolvedPlaces = [];
        /** @var string[] $makeflowNameList */
        $makeflowNameList = [];
        foreach ($placeUserList as $placeUser) {
            $makeflowName = $placeUser->getMakeflowName();
            $placeName = $placeUser->getPlaceName();

            if (!isset($makeflows[$makeflowName])) {
                continue;
            }
            $makeflow = $makeflows[$makeflowName];
            $places = $makeflow->getPlaces();
            if (!isset($places[$placeName])) {
                continue;
            }

            if (isset($userInvolvedPlaces[$makeflowName])) {
                $userInvolvedPlaces[$makeflowName]["placeNames"][] = $placeName;
            } else {
                $makeflowNameList[] = $makeflowName;
                $userInvolvedPlaces[$makeflowName] = [
                    "makeflowName" => $makeflowName,
                    "placeNames" => [$placeName],
                    "workspacePackages" => []
                ];
            }
        }


        //穷举所有makeflowName下的workspace，然后判断每个workspace对应context对象的getCurrentProcessingPlaces里 包含了哪些 当前用户placeNames 里的 placeName，然后记录这个workspace与placeName
        $workspaceRepo = $em->getRepository("MakeflowDashboard:Workspace");
        /** @var Workspace[] $workspaceList */
        $workspaceList = $workspaceRepo->findProcessingWorkspacesByMakeflowNameList($makeflowNameList);
        $workspaceFactory = $this->get("App\Makeflow\WorkspaceContextFactory");
        foreach ($workspaceList as $workspace) {
            $makeflowName = $workspace->getMakeflowName();
            /** @var string[] $placeNames */
            $placeNames = $userInvolvedPlaces[$makeflowName]["placeNames"];
            $workspaceContext = $workspaceFactory->getContext($workspace);
            $workspaceProcessingPlaces = $workspaceContext->getCurrentProcessingPlaces();
            foreach ($workspaceProcessingPlaces as $workspaceProcessingPlace) {
                if (in_array($workspaceProcessingPlace->getName(), $placeNames)) {
                    $placeName = $workspaceProcessingPlace->getName();
                    $userInvolvedPlaces[$makeflowName]["workspacePackages"][] = [
                        "placeName" => $placeName,
                        "makeflowName" => $makeflowName,
                        "workspace" => $workspace
                    ];
                }
            }
        }


        $graphGenerator = $this->get("App\Makeflow\MakeflowConfigGraphGenerator");
        $makeflowGraphs = [];
        foreach ($makeflows as $makeflowName => $makeflow) {
            if (!isset($userInvolvedPlaces[$makeflowName])) {
                continue;
            }

            $points = [];
            $directedLines = [];
            $svgData = [];
            $svg = $graphGenerator->generateSvg($makeflow, $points, $directedLines, $svgData);

            $makeflowGraphs[] = [
                "makeflowLabel" => $makeflow->getLabel(),
                "makeflowName" => $makeflow->getName(),
                "points" => array_values($points),
                "directedLines" => $directedLines,
                "svg" => $svg,
                "svgData" => $svgData,
                "makeflowConfig" => $makeflow->getMakeflowConfig()
            ];
        }


        return $this->render('makeflow_user/dashboard.html.twig', [
            'userInvolvedPlaces' => array_values($userInvolvedPlaces),
            'makeflowGraphs' => $makeflowGraphs
        ]);
    }
}
