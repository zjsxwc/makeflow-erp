<?php
/**
 * Created by PhpStorm.
 * User: wangchao
 * Date: 30/07/2018
 * Time: 3:29 PM
 */

namespace App\Makeflow\Dashboard\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="makeflow_place_user", uniqueConstraints={@ORM\UniqueConstraint(name="unique_user_to_one_place_idx", columns={"user_id", "makeflow_name", "place_name"})})
 * @ORM\Entity(repositoryClass="App\Makeflow\Dashboard\Repository\PlaceUserRepository")
 */
class PlaceUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    public $userId;


    /**
     * @var string
     *
     * @ORM\Column(name="makeflow_name", type="string", length=255)
     */
    public $makeflowName;


    /**
     * @var string
     *
     * @ORM\Column(name="place_name", type="string", length=255)
     */
    public $placeName;

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
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
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
    public function getPlaceName(): string
    {
        return $this->placeName;
    }

    /**
     * @param string $placeName
     */
    public function setPlaceName(string $placeName): void
    {
        $this->placeName = $placeName;
    }

}