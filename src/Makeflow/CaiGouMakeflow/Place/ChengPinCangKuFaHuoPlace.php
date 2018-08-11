<?php
namespace App\Makeflow\CaiGouMakeflow\Place;

use App\Makeflow\Dashboard\Entity\Workspace;
use App\Makeflow\Place;
use Symfony\Component\HttpFoundation\Request;

class ChengPinCangKuFaHuoPlace extends Place
{
    protected $isFinalPlace = true;

    public $label = "成品仓库开始发货";

    public $description = "";

    protected $substitutionPrerequisite = "满足成品发货条件";

    public function processAction(Request $request, Workspace $workspace)
    {
        //return $this->render("xxx.html.twig", []);
    }

}