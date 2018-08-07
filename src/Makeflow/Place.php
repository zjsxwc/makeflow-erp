<?php
/**
 * Created by PhpStorm.
 * User: wangchao
 * Date: 31/07/2018
 * Time: 11:31 AM
 */

namespace App\Makeflow;

use App\Makeflow\Dashboard\Entity\PlaceUser;
use App\Makeflow\Dashboard\Entity\Workspace;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\ORM\EntityManagerInterface;
use SensioLabs\Security\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\Request;

abstract class Place
{

    /** @var string[]
     * 额外的前置要求，用于有多个结果的分支情况，比如泡面超市卖光了，
     * 这时就应该触发`不吃泡面`节点，而这个节点就是`买泡面` 节点的另一个分支，
     * 于是这个节点就需要提供`extraPrerequisites`属性来区别默认的`泡泡面`节点。
     * eg: `不吃泡面`节点的`extraPrerequisites`是`["NO_PAOMIAN_IN_SHOP"]`
     * 我们可以在`买泡面`节点完成时，给Workspace的directory多增加"NO_PAOMIAN_IN_SHOP"，
     * 为了防止同时触发默认的`泡泡面`节点，我们也要给`泡泡面`节点的extraPrerequisites`设置个值["EXIST_PAOMIAN_IN_SHOP"]
     */
    protected $extraPrerequisites = [];

    /**
     * @return string[]
     */
    public function getExtraPrerequisites()
    {
        return $this->extraPrerequisites;
    }

    /** @var Makeflow */
    protected $makeflow;

    /** @var EntityManagerInterface */
    protected $entityManager;
    /** @var \Twig_Environment */
    protected $twig;

    /**
     * Place constructor.
     * @param Makeflow $makeflow
     * @param EntityManagerInterface $entityManager
     * @param \Twig_Environment $twig
     */
    public function __construct(Makeflow $makeflow, EntityManagerInterface $entityManager, \Twig_Environment $twig)
    {
        $this->makeflow = $makeflow;
        $this->entityManager = $entityManager;
        $this->twig = $twig;

        $this->initialize();
    }

    protected function initialize()
    {
        $this->getName();
    }


    protected $name;

    public $label = "";

    public $description = "";


    public function getLabel()
    {
        if ($this->label) {
            return $this->label;
        }
        return $this->getName();
    }

    public function getName()
    {
        if (!$this->name) {
            $className = get_class($this);
            $nameSegments = explode("\\", $className);
            $baseName = $nameSegments[count($nameSegments) - 1];
            list($name) = explode("Place", $baseName);
            $this->name = $name;
        }
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }


    /**
     * @param Request $request
     * @param Workspace $workspace
     * @return Response
     */
    public function processAction(Request $request, Workspace $workspace)
    {
        $request->getMethod();
        $workspace->getId();
        throw new RuntimeException("Need processAction()");
    }

    /**
     * @param string $view
     * @param array $parameters
     * @param Response|null $response
     * @return Response
     * @inheritdoc
     */
    public function render(string $view, array $parameters = array(), Response $response = null)
    {
        if (!$response) {
            $response = new Response();
        }
        $twigNamespace = $this->makeflow->getName() . 'Makeflow';
        $content = $this->twig->render("@" . $twigNamespace . "/" . $view, $parameters);
        $response->setContent($content);
        return $response;
    }

    /**
     * Returns a JsonResponse.
     * @param $data
     * @param int $status
     * @param array $headers
     * @param array $context
     * @return JsonResponse
     */
    protected function json($data, int $status = 200, array $headers = array(), array $context = array()): JsonResponse
    {
        return new JsonResponse($data, $status, $headers);
    }


    public function isUserAllowedInPlace(int $userId)
    {
        $repo = $this->entityManager->getRepository("MakeflowDashboard:PlaceUser");
        $mayPlaceUser = $repo->findOneBy([
            "userId" => $userId,
            "makeflowName" => $this->makeflow->getName(),
            "placeName" => $this->getName(),
        ]);
        if ($mayPlaceUser) {
            return true;
        }
        return false;
    }

    /**
     * @return \App\Entity\User[]
     */
    public function getUsers()
    {
        $repo = $this->entityManager->getRepository("MakeflowDashboard:PlaceUser");
        /** @var PlaceUser[] $placeUserList */
        $placeUserList = $repo->findBy([
            "makeflowName" => $this->makeflow->getName(),
            "placeName" => $this->getName()
        ]);
        $userIdList = [];
        foreach ($placeUserList as $placeUser) {
            $userIdList[] = $placeUser->getUserId();
        }
        /** @var UserRepository $userRepo */
        $userRepo = $this->entityManager->getRepository("App:User");
        return $userRepo->findUsersByIdList($userIdList);

    }

    /**
     * @param $userIdList
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     */
    public function removeUsers(array $userIdList)
    {
        $repo = $this->entityManager->getRepository("MakeflowDashboard:PlaceUser");

        return $repo->deleteByMakeflowNameAndPlaceNameAndUserIdList($this->makeflow->getName(), $this->getName(), $userIdList);

    }

    /**
     * @param array $userIdList
     */
    public function bindUsersToPlace(array $userIdList)
    {
        $repo = $this->entityManager->getRepository("MakeflowDashboard:PlaceUser");
        $existPlaceUserList = $repo->findByMakeflowNameAndPlaceNameList($this->makeflow->getName(), [$this->getName()]);
        $alreadyExistUserIdList = [];
        foreach ($existPlaceUserList as $existPlaceUser) {
            $alreadyExistUserIdList[] = $existPlaceUser->getUserId();
        }

        foreach ($userIdList as $userId) {
            if (in_array($userId, $alreadyExistUserIdList)) {
                continue;
            }
            $placeUser = new PlaceUser();
            $placeUser->setUserId(intval($userId));
            $placeUser->setMakeflowName($this->makeflow->getName());
            $placeUser->setPlaceName($this->getName());
            $this->entityManager->persist($placeUser);
        }
        $this->entityManager->flush();
    }


    protected $isFinalPlace = false;

    /**
     * @param Workspace $workspace
     * @param string $extraPrerequisiteName  refer to \App\Makeflow\Place::$extraPrerequisites
     */
    protected function finishPlace(Workspace $workspace, $extraPrerequisiteName = "")
    {
        if ($workspace->getMakeflowName() !== $this->makeflow->getName()) {
            throw new  \LogicException(sprintf('Place %s makeflow  %s not for workspace makeflow %s', $this->getName(), $this->makeflow->getName(), $workspace->getMakeflowName()));
        }
        $placeName = $this->getName();
        $workspace->addDirectory($placeName);
        if ($extraPrerequisiteName) {
            $workspace->addDirectory($extraPrerequisiteName);
        }

        if ($this->isFinalPlace) {
            $workspace->setStatus(Workspace::STATUS_FINISHED);
        }
        $this->entityManager->flush();
    }

}