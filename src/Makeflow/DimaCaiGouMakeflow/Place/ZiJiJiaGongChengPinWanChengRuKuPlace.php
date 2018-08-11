<?php
namespace App\Makeflow\DimaCaiGouMakeflow\Place;

use App\Makeflow\Dashboard\Entity\Workspace;
use App\Makeflow\Place;
use Symfony\Component\HttpFoundation\Request;

class ZiJiJiaGongChengPinWanChengRuKuPlace extends Place
{

    public $label = "完成自己加工成品开始入库操作";

    public $description = "";

    public function processAction(Request $request, Workspace $workspace)
    {
        //return $this->render("xxx.html.twig", []);
    }

}