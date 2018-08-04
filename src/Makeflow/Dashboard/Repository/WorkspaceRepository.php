<?php
/**
 * Created by PhpStorm.
 * User: wangchao
 * Date: 31/07/2018
 * Time: 2:36 PM
 */

namespace App\Makeflow\Dashboard\Repository;


use App\Makeflow\Dashboard\Entity\Workspace;

class WorkspaceRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * @param string[] $makeflowNameList
     * @return Workspace[]
     */
    public function findProcessingWorkspacesByMakeflowNameList($makeflowNameList)
    {
        $qb = $this->createQueryBuilder("w")
            ->where("w.makeflowName IN (:makeflowNameList) AND w.status = :status")
            ->setParameter("makeflowNameList", $makeflowNameList)
            ->setParameter("status", Workspace::STATUS_PROCESSING)
        ;
        $query = $qb->getQuery();
        return $query->getResult();
    }


}