<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Image
 * @ORM\Entity
 * @ORM\Table(name="image", indexes={
 *      @ORM\Index(name="user_idx", columns={"user_id"}),
 * })
 */
class Image
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    public $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255)
     */
    public $path;

    /**
     * @var int
     *
     * @ORM\Column(name="create_time", type="bigint")
     */
    public $createTime;



    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set accountId
     *
     * @param integer $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Get accountId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set path
     *
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set createTime
     *
     * @param integer $createTime
     */
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;
    }

    /**
     * Get createTime
     *
     * @return int
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }


}

