<?php
/**
 * Created by PhpStorm.
 * User: wangchao
 * Date: 31/07/2018
 * Time: 10:05 AM
 */

namespace App\Makeflow;


use App\Makeflow\Dashboard\Entity\Workspace;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Yaml\Yaml;

abstract class Makeflow
{

    /** @var EntityManagerInterface */
    protected $entityManager;
    /** @var \Twig_Environment */
    protected $twig;

    /** @var string  */
    protected $timezone;

    /**
     * Makeflow constructor.
     * @param EntityManagerInterface $entityManager
     * @param \Twig_Environment $twig
     * @param string $timezone
     */
    public function __construct(EntityManagerInterface $entityManager, \Twig_Environment $twig, $timezone = 'Asia/Shanghai')
    {
        $this->entityManager = $entityManager;
        $this->twig = $twig;
        $this->timezone = $timezone;

        $this->initialize();
    }

    protected function initialize()
    {
        $this->getName();
        $this->getMakeflowDir();
        $this->getNamespaceName();
        $this->getPlaceDir();
        $this->getEntityDir();
        $this->getPlaces();
        $this->getMakeflowConfig();
    }


    protected $name;

    public function getName()
    {
        if (!$this->name) {
            $this->name = get_class($this);
        }
        return $this->name;

    }

    public $label;

    public function getLabel()
    {
        if ($this->label) {
            return $this->label;
        }
        return $this->getName();
    }


    protected $namespaceName;

    public function getNamespaceName()
    {
        if (!$this->namespaceName) {
            $ro = new \ReflectionObject($this);
            $this->namespaceName = $ro->getNamespaceName();
        }
        return $this->namespaceName;
    }

    protected $makeflowDir;

    public function getMakeflowDir()
    {
        if (!$this->makeflowDir) {
            $ro = new \ReflectionObject($this);
            $fullFileName = $ro->getFileName();
            $this->makeflowDir = dirname($fullFileName);
        }
        return $this->makeflowDir;
    }

    protected $placeDir;

    public function getPlaceDir()
    {
        if (!$this->placeDir) {
            $this->placeDir = $this->getMakeflowDir() . "/Place";
        }
        return $this->placeDir;
    }

    protected $entityDir;

    public function getEntityDir()
    {
        if (!$this->entityDir) {
            $this->entityDir = $this->getMakeflowDir() . "/Entity";
        }
        return $this->entityDir;
    }

    /** @var AbstractPlace[] */
    protected $places = [];

    /**
     * @return AbstractPlace[]
     */
    public function getPlaces()
    {
        if (!$this->places) {
            $placeDir = $this->getPlaceDir();
            $phpFileFullPathList = glob($placeDir . "/*.php");
            foreach ($phpFileFullPathList as $phpFileFullPath) {
                $baseName = basename($phpFileFullPath, ".php");
                $placeClassName = $this->getNamespaceName() . "\\Place\\" . $baseName;

                $mayPlace = new $placeClassName($this, $this->entityManager, $this->twig);
                if (!$mayPlace instanceof AbstractPlace) {
                    continue;
                }
                $name = $mayPlace->getName();
                if (!$name) {
                    list($name) = explode("Place", $baseName);
                    $mayPlace->setName($name);
                }

                if (isset($this->places[$name])) {
                    throw new \LogicException(sprintf('Trying to register two place with the same name "%s" to makeflow "%s"', $name, $this->getName()));
                }
                $this->places[$name] = $mayPlace;
            }
        }

        return $this->places;
    }


    /** @var string[][] */
    protected $makeflowConfig = [];

    public function getMakeflowConfig()
    {
        if (!$this->makeflowConfig) {
            $this->makeflowConfig = Yaml::parseFile($this->getMakeflowDir() . "/makeflow.yaml");
        }
        $copyMakeflowConfig = $this->makeflowConfig;
        foreach ($copyMakeflowConfig as $target => $prerequisites) {
            if (is_string($prerequisites)) {
                $this->makeflowConfig[$target] = [$prerequisites];
                $prerequisites = [$prerequisites];
            }
            if (!is_array($prerequisites)) {
                continue;
            }
            foreach ($prerequisites as $prerequisite) {
                if (!is_string($prerequisite)) {
                    continue;
                }
                if (!isset($this->makeflowConfig[$prerequisite])) {
                    $this->makeflowConfig[$prerequisite] = [];
                }
            }
        }

        return $this->makeflowConfig;
    }


    /**
     * @param $userId
     * @return bool
     * @throws \LogicException
     */
    public function checkUserAllowedToCreateWorkspace($userId)
    {
        //判断userId是否在无前置条件的place里面
        $makeflowConfig = $this->getMakeflowConfig();
        $startingPlaceNames = [];
        foreach ($makeflowConfig as $targetPlaceName => $prerequisites) {
            if (!$prerequisites) {
                $startingPlaceNames[] = $targetPlaceName;
            }
        }

        $placeUserRepo = $this->entityManager->getRepository("MakeflowDashboard:PlaceUser");
        $placeUserList = $placeUserRepo->findByMakeflowNameAndPlaceNameList($this->getName(), $startingPlaceNames);

        $isAllowed = false;
        foreach ($placeUserList as $placeUser) {
            if ($placeUser->getUserId() === $userId) {
                $isAllowed = true;
                break;
            }
        }

        if (!$isAllowed) {
            throw new \LogicException(sprintf('User id %s is not allowed to build workspace %s', $userId, $this->getName()));
        }

        return $isAllowed;
    }

    /**
     * @param int $userId
     * @param string $title
     * @return Workspace
     * @throws \LogicException
     */
    public function createWorkspace(int $userId, string $title)
    {
        $this->checkUserAllowedToCreateWorkspace($userId);

        $workspace = new Workspace();
        $workspace->setTitle($title);
        $workspace->setDirectory([]);
        $workspace->setMakeflowName($this->getName());
        $workspace->setBuildUserId($userId);
        $workspace->setStatus(Workspace::STATUS_PROCESSING);

        $workspace->setBuildTime(time());

        $this->entityManager->persist($workspace);
        $this->entityManager->flush();
        return $workspace;
    }

    /**
     * @return Workspace[]
     */
    public function getWorkspaces()
    {
        $workspaceRepo = $this->entityManager->getRepository("MakeflowDashboard:Workspace");
        $workspaceList = $workspaceRepo->findBy([
            "makeflowName" => $this->getName()
        ]);
        return $workspaceList;
    }


}