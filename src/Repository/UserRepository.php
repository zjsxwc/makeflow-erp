<?php
/**
 * Created by PhpStorm.
 * User: wangchao
 * Date: 31/07/2018
 * Time: 2:36 PM
 */

namespace App\Repository;


use App\Entity\User;

class UserRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param $idList
     * @return User[]
     */
    public function findUsersByIdList($idList)
    {
        $qb = $this->createQueryBuilder("u")
            ->where("u.id IN (:idList)")
            ->setParameter("idList", $idList);
        return $qb->getQuery()->getResult();
    }


}