<?php
/**
 * Created by PhpStorm.
 * User: wangchao
 * Date: 30/07/2018
 * Time: 3:29 PM
 */

namespace App\Makeflow\Dashboard\Entity;

use Doctrine\ORM\Mapping as ORM;
use voku\helper\AntiXSS;

/**
 * @ORM\Entity
 * @ORM\Table(name="makeflow_workspace")
 * @ORM\Entity(repositoryClass="App\Makeflow\Dashboard\Repository\WorkspaceRepository")
 */
class Workspace
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;


    /**
     * @var string[]
     *
     * @ORM\Column(name="directory", type="json_array")
     */
    public $directory;


    /**
     * @var string
     *
     * @ORM\Column(name="makeflow_name", type="string", length=255)
     */
    public $makeflowName;


    /**
     * @var string
     *
     * @ORM\Column(name="build_user_id", type="integer")
     */
    public $buildUserId;


    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    public $title;



    /**
     * @var int
     *
     * @ORM\Column(name="build_time", type="bigint")
     */
    public $buildTime;



    /**
     * @var int
     *
     * @ORM\Column(name="status", type="smallint", options={"default"=0})
     */
    public $status;


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return array
     */
    public function getDirectory(): array
    {
        return $this->directory;
    }

    /**
     * @param array $directory
     */
    public function setDirectory(array $directory): void
    {
        $this->directory = $directory;
        $this->directory = array_unique($this->directory);
    }

    /**
     * @param string $placeName
     */
    public function addDirectory(string $placeName): void
    {
        $this->directory[] = $placeName;
        $this->directory = array_unique($this->directory);
    }


    /**
     * @return string
     */
    public function getMakeflowName(): string
    {
        return $this->makeflowName;
    }

    /**
     * @param string $makeflowName
     */
    public function setMakeflowName(string $makeflowName): void
    {
        $this->makeflowName = $makeflowName;
    }

    /**
     * @return string
     */
    public function getBuildUserId(): string
    {
        return $this->buildUserId;
    }

    /**
     * @param string $buildUserId
     */
    public function setBuildUserId(string $buildUserId): void
    {
        $this->buildUserId = $buildUserId;
    }

    /**
     * @return \DateTime
     */
    public function getBuildDate(): \DateTime
    {
        $buildDate = new \DateTime();
        $buildDate->setTimestamp($this->buildTime);
        return $buildDate;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getBuildTime(): int
    {
        return $this->buildTime;
    }

    /**
     * @param int $buildTime
     */
    public function setBuildTime(int $buildTime): void
    {
        $this->buildTime = $buildTime;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $antiXss = new AntiXSS();

        $this->title = $antiXss->xss_clean($title);
    }


    const STATUS_PROCESSING = 0;
    const STATUS_FINISHED = 1;

}