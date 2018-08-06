<?php
/**
 * Created by PhpStorm.
 * User: wangchao
 * Date: 31/07/2018
 * Time: 2:36 PM
 */

namespace App\Makeflow\PaoMianMakeflow\Repository;


use App\Makeflow\PaoMianMakeflow\Entity\Note;

class NoteRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * @return Note[]
     * @inheritdoc
     */
    public function getNoteListByWorkspaceId($workspaceId)
    {
        return $this->findBy([
            "workspaceId" => $workspaceId
        ]);
    }

}