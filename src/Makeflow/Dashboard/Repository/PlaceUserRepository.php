<?php
/**
 * Created by PhpStorm.
 * User: wangchao
 * Date: 31/07/2018
 * Time: 2:36 PM
 */

namespace App\Makeflow\Dashboard\Repository;


use App\Makeflow\Dashboard\Entity\PlaceUser;
use App\Makeflow\Dashboard\Entity\Workspace;

class PlaceUserRepository extends \Doctrine\ORM\EntityRepository
{


    /**
     * @param string $makeflowName
     * @param string $placeName
     * @param int[] $userIdList
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     */
    public function deleteByMakeflowNameAndPlaceNameAndUserIdList(string $makeflowName, string $placeName, array $userIdList)
    {
        if (!$userIdList) {
            return true;
        }
        $idStrList = [];
        foreach ($userIdList as $userId) {
            $idStrList[] = strval(intval($userId));
        }
        $idListStr = implode(",", $idStrList);

        $sql = "DELETE FROM makeflow_place_user WHERE makeflow_place_user.makeflow_name = :makeflowName AND makeflow_place_user.place_name = :placeName AND makeflow_place_user.user_id IN ($idListStr)";
        $params = [
            'makeflowName' => $makeflowName,
            'placeName' => $placeName,
        ];

        $stmt = $this->_em->getConnection()->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * @param string $makeflowName
     * @param array $placeNameList
     * @return PlaceUser[]
     */
    public function findByMakeflowNameAndPlaceNameList(string $makeflowName, array $placeNameList)
    {
        $qb = $this->createQueryBuilder("pu")
            ->where("pu.makeflowName = :makeflowName AND pu.placeName IN (:placeNameList)")
            ->setParameter("makeflowName", $makeflowName)
            ->setParameter("placeNameList", $placeNameList);
        return $qb->getQuery()->getResult();
    }

}