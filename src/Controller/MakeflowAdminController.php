<?php

namespace App\Controller;

use App\Entity\User;
use App\Makeflow\Place;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * @Route("/makeflow-admin")
 */
class MakeflowAdminController extends Controller
{
    /**
     * @Route("/dashboard", name="makeflow_admin_dashboard")
     */
    public function dashboard()
    {
        $makeflowManager = $this->get("App\Makeflow\MakeflowManager");

        $makeflows = $makeflowManager->getMakeflows();

        $graphGenerator = $this->get("App\Makeflow\MakeflowConfigGraphGenerator");
        $makeflowGraphs = [];
        foreach ($makeflows as $makeflowName => $makeflow) {
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

        $userList = $this->getAllUsers();

        $allUsers = [];
        foreach ($userList as $user) {
            $allUsers[] = [
                "id" => $user->getId(),
                "username" => $user->getUsername()
            ];
        }
        return $this->render('makeflow_admin/dashboard.html.twig', [
            'makeflowGraphs' => $makeflowGraphs,
            'allUsers' => $allUsers
        ]);
    }

    /**
     * @return User[]
     */
    protected function getAllUsers()
    {
        $entityManager = $this->get("doctrine.orm.entity_manager");

        /** @var UserRepository $userRepo */
        $userRepo = $entityManager->getRepository("App:User");
        /** @var User[] $userList */
        $userList = $userRepo->findAll();
        return $userList;
    }

    /**
     * @Route("/all-users", name="makeflow_admin_all_users", methods={"POST"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getAllUsersAction(Request $request)
    {
        $userList = $this->getAllUsers();

        return $this->json([
            "code" => -1,
            "data" => [
                "users" => $this->getUsersRenderData($userList)
            ]
        ]);
    }

    /**
     * @Route("/place-users", name="makeflow_admin_place_users", methods={"POST"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getUsersOfPlace(Request $request)
    {
        $makeflowName = $request->request->get("makeflowName");
        $placeName = $request->request->get("placeName");

        $makeflowManager = $this->get("App\Makeflow\MakeflowManager");
        try {
            /** @var Place $place */
            $place = $makeflowManager->getMakeflows()[$makeflowName]->getPlaces()[$placeName];
        } catch (\Throwable $throwable) {
            return $this->json([
                "code" => $throwable->getCode(),
                "message" => $throwable->getMessage()
            ]);
        }

        $userList = $place->getUsers();

        return $this->json([
            "code" => -1,
            "data" => [
                "users" => $this->getUsersRenderData($userList)
            ]
        ]);
    }


    /**
     * @Route("/place-bind-users", name="makeflow_admin_place_bind_users", methods={"POST"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function placeBindUsers(Request $request)
    {
        /** @var string $makeflowName */
        /** @var string $placeName */
        /** @var int[] $userIdList */
        list($makeflowName, $placeName, $userIdList) = $this->getMakeflowNameAndPlaceNameAndUserIdListFromRequest($request);

        $makeflowManager = $this->get("App\Makeflow\MakeflowManager");
        try {
            /** @var Place $place */
            $place = $makeflowManager->getMakeflows()[$makeflowName]->getPlaces()[$placeName];
            $place->bindUsersToPlace($userIdList);
        } catch (\Throwable $throwable) {
            return $this->json([
                "code" => $throwable->getCode(),
                "message" => $throwable->getMessage()
            ]);
        }

        $userList = $place->getUsers();

        return $this->json([
            "code" => -1,
            "data" => [
                "users" => $this->getUsersRenderData($userList)
            ]
        ]);
    }

    /**
     * @Route("/place-remove-users", name="makeflow_admin_place_remove_users", methods={"POST"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function placeRemoveUsers(Request $request)
    {
        /** @var string $makeflowName */
        /** @var string $placeName */
        /** @var int[] $userIdList */
        list($makeflowName, $placeName, $userIdList) = $this->getMakeflowNameAndPlaceNameAndUserIdListFromRequest($request);

        $makeflowManager = $this->get("App\Makeflow\MakeflowManager");
        try {
            /** @var Place $place */
            $place = $makeflowManager->getMakeflows()[$makeflowName]->getPlaces()[$placeName];
            $place->removeUsers($userIdList);
        } catch (\Throwable $throwable) {
            return $this->json([
                "code" => $throwable->getCode(),
                "message" => $throwable->getMessage()
            ]);
        }
        $userList = $place->getUsers();

        return $this->json([
            "code" => -1,
            "data" => [
                "users" => $this->getUsersRenderData($userList)
            ]
        ]);
    }


    protected function getMakeflowNameAndPlaceNameAndUserIdListFromRequest(Request $request)
    {
        $makeflowName = $request->request->get("makeflowName");
        $placeName = $request->request->get("placeName");
        $userIds = $request->request->get("userIds");
        $userIdList = [];
        foreach (explode(",", $userIds) as $userIdStr) {
            $userIdList[] = intval($userIdStr);
        }
        return [$makeflowName, $placeName, $userIdList];
    }

    /**
     * @param User[] $userList
     * @return array
     */
    protected function getUsersRenderData($userList)
    {
        $users = [];
        foreach ($userList as $user) {
            $users[] = [
                "id" => $user->getId(),
                "username" => $user->getUsername()
            ];
        }
        return $users;
    }

}
