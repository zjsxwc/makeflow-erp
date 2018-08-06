<?php
/**
 * Created by PhpStorm.
 * User: wangchao
 * Date: 31/07/2018
 * Time: 8:53 AM
 */

namespace App\Makeflow\PaoMianMakeflow\Place;


use App\Makeflow\Dashboard\Entity\Workspace;
use App\Makeflow\Place;
use Symfony\Component\HttpFoundation\Request;

class MakePaoMianPlace extends Place
{
    protected $name = "MakePaoMian";
    public $label="泡泡面";

    public function processAction(Request $request, Workspace $workspace)
    {

        $noteList = $this->entityManager->getRepository("PaoMianMakeflow:Note")->getNoteListByWorkspaceId($workspace->getId());

        dump($noteList);die;

    }
}