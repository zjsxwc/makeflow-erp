<?php
namespace App\Makeflow\DimaCaiGouMakeflow\Place;

use App\Makeflow\Dashboard\Entity\Workspace;
use App\Makeflow\Place;
use Symfony\Component\HttpFoundation\Request;

class ChengPinCangKuFaHuoPlace extends Place
{
    public $label = "成品仓库开始发货";

    public $description = "";

    public function processAction(Request $request, Workspace $workspace)
    {
        //return $this->render("xxx.html.twig", []);
    }

}