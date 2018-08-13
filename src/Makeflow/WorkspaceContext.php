<?php
/**
 * Created by PhpStorm.
 * User: wangchao
 * Date: 02/08/2018
 * Time: 10:08 AM
 */

namespace App\Makeflow;


use App\Makeflow\Dashboard\Entity\PlaceUser;
use App\Makeflow\Dashboard\Entity\Workspace;
use Doctrine\ORM\EntityManagerInterface;

class WorkspaceContext
{

    protected $makeflowManager;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /**
     * WorkspaceContext constructor.
     * @param MakeflowManager $makeflowManager
     * @param EntityManagerInterface $entityManager
     * @param Workspace $workspace
     */
    public function __construct(MakeflowManager $makeflowManager, EntityManagerInterface $entityManager, Workspace $workspace)
    {
        $this->makeflowManager = $makeflowManager;
        $this->entityManager = $entityManager;
        $this->setWorkspace($workspace);
    }

    /** @var Workspace */
    protected $workspace;
    /** @var Makeflow */
    protected $makeflow;

    protected function setWorkspace(Workspace $workspace)
    {
        $this->workspace = $workspace;
        $this->makeflow = $this->makeflowManager->getMakeflowByWorkspace($workspace);

        $this->checkContext();
    }

    /**
     * @return Workspace
     */
    public function getWorkspace()
    {
        return $this->workspace;
    }


    protected function checkContext()
    {
        if (!$this->makeflow) {
            throw new  \LogicException(sprintf('We need a makeflow for the workspace context'));
        }
        if (!$this->workspace) {
            throw new  \LogicException(sprintf('We need a workspace for the makeflow %s', $this->makeflow->getName()));
        }
    }

    /**
     * @param AbstractPlace $processingPlace
     * @param string[] $prerequisitePlaceNames
     */
    public function fromProcessingPlaceDeletePrerequisites(AbstractPlace $processingPlace, array $prerequisitePlaceNames)
    {
        $currentProcessingPlaces = $this->getCurrentProcessingPlaces();
        $isProcessingPlaceAllowed = false;
        foreach ($currentProcessingPlaces as $currentProcessingPlace)
        {
            if ($currentProcessingPlace->getName() === $processingPlace->getName()) {
                $isProcessingPlaceAllowed = true;
                break;
            }
        }
        if (!$isProcessingPlaceAllowed) {
            throw new  \LogicException(sprintf('Place %s is not current processing', $processingPlace->getName()));
        }

        $makeflowConfig = $this->makeflow->getMakeflowConfig();
        if (!isset($makeflowConfig[$processingPlace->getName()])) {
            throw new  \LogicException(sprintf('Place name %s is not in makeflow config', $processingPlace->getName()));
        }

        $places = $this->makeflow->getPlaces();
        foreach ($prerequisitePlaceNames as $prerequisitePlaceName) {
            if (!isset($places[$prerequisitePlaceName])) {
                throw new \LogicException(sprintf("Not valid prerequisite placeName %s", $prerequisitePlaceName), 2);
            }
            if (!in_array($prerequisitePlaceName, $makeflowConfig[$processingPlace->getName()])) {
                throw new  \LogicException(sprintf('Some of prerequisite place names (%s) are not in allowed place names (%s)', json_encode($prerequisitePlaceNames), json_encode($makeflowConfig[$processingPlace->getName()])));
            }
        }

        $workspaceDirectory = $this->workspace->getDirectory();
        $workspaceDirectory = array_diff($workspaceDirectory, $prerequisitePlaceNames);
        $this->workspace->setDirectory($workspaceDirectory);
        $this->entityManager->flush();
    }


    /**
     * @param $userId
     * @return AbstractPlace[]|array
     */
    public function getUserCurrentProcessingPlaces($userId)
    {
        $placeUserRepo = $this->entityManager->getRepository("MakeflowDashboard:PlaceUser");
        /** @var PlaceUser[] $placeUserList */
        $placeUserList = $placeUserRepo->findBy([
            "userId" => $userId,
            "makeflowName" => $this->workspace->getMakeflowName()
        ]);
        /** @var string[] $userPlaceNames */
        $userPlaceNames = [];
        foreach ($placeUserList as $placeUser) {
            $userPlaceNames[] = $placeUser->getPlaceName();
        }

        /** @var AbstractPlace[] $currentUserProcessingPlaces */
        $currentUserProcessingPlaces = [];
        $currentProcessingPlaces = $this->getCurrentProcessingPlaces();
        foreach ($currentProcessingPlaces as $currentProcessingPlace) {
            if (in_array($currentProcessingPlace->getName(), $userPlaceNames)) {
                $currentUserProcessingPlaces[] = $currentProcessingPlace;
            }
        }
        return $currentUserProcessingPlaces;
    }



    /**
     * @return AbstractPlace[]|array
     */
    public function getCurrentProcessingPlaces()
    {
        $makeflowConfig = $this->makeflow->getMakeflowConfig();
        $workspaceDirectory = $this->workspace->getDirectory();

        $places = $this->makeflow->getPlaces();
        //获取 先决条件满足，但目标没有出现的那些place

        $processingPlaceNames = [];
        foreach ($makeflowConfig as $targetPlaceName => $prerequisites) {
            if (!in_array($targetPlaceName, $workspaceDirectory)) {
                $targetPlace = $places[$targetPlaceName];

                if ($targetPlace->getExtraPrerequisite()) {
                    $prerequisites[] = $targetPlace->getExtraPrerequisite();
                }

                $isAllPrerequisitesInDirectory = true;
                foreach ($prerequisites as $prerequisite) {
                    if (!in_array($prerequisite, $workspaceDirectory)) {
                        $isAllPrerequisitesInDirectory = false;
                        break;
                    }
                }
                if ($targetPlace->getSubstitutionPrerequisite()) {
                    $substitutionPrerequisite = $targetPlace->getSubstitutionPrerequisite();
                    if (in_array($substitutionPrerequisite, $workspaceDirectory)) {
                        $isAllPrerequisitesInDirectory = true;
                    }
                }
                if ($isAllPrerequisitesInDirectory) {
                    $processingPlaceNames[] = $targetPlaceName;
                }
            }
        }

        /** @var AbstractPlace[] $processingPlaces */
        $processingPlaces = [];

        foreach ($processingPlaceNames as $processingPlaceName) {
            $places = $this->makeflow->getPlaces();
            if (isset($places[$processingPlaceName])) {
                $processingPlaces[] = $places[$processingPlaceName];
            } else {
                throw new  \LogicException(sprintf('Place %s not exist for the makeflow %s', $processingPlaceName, $this->makeflow->getName()));
            }
        }
        return $processingPlaces;

    }

}