<?php
/**
 * Created by PhpStorm.
 * User: wangchao
 * Date: 02/08/2018
 * Time: 10:59 AM
 */

namespace App\Makeflow;


use App\Makeflow\Dashboard\Entity\Workspace;
use Doctrine\ORM\EntityManagerInterface;

class WorkspaceContextFactory
{

    protected $makeflowManager;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /**
     * WorkspaceContextFactory constructor.
     * @param MakeflowManager $makeflowManager
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(MakeflowManager $makeflowManager, EntityManagerInterface $entityManager)
    {
        $this->makeflowManager = $makeflowManager;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Workspace $workspace
     * @return WorkspaceContext
     */
    public function getContext(Workspace $workspace)
    {
        return new WorkspaceContext($this->makeflowManager, $this->entityManager, $workspace);
    }

}