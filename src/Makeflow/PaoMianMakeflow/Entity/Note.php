<?php
/**
 * Created by PhpStorm.
 * User: wangchao
 * Date: 30/07/2018
 * Time: 3:29 PM
 */

namespace App\Makeflow\PaoMianMakeflow\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="pao_mian_makeflow_note")
 * @ORM\Entity(repositoryClass="App\Makeflow\PaoMianMakeflow\Repository\NoteRepository")
 */
class Note
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @var integer
     *
     * @ORM\Column(name="workspace_id", type="integer", nullable=true)
     */
    public $workspaceId;



    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    public $name;



    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    public $content;

    /**
     * @return int
     */
    public function getWorkspaceId(): int
    {
        return $this->workspaceId;
    }

    /**
     * @param int $workspaceId
     */
    public function setWorkspaceId(int $workspaceId): void
    {
        $this->workspaceId = $workspaceId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }



}