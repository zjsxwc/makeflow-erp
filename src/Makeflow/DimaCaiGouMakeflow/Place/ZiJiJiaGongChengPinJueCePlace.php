<?php
namespace App\Makeflow\DimaCaiGouMakeflow\Place;

use App\Makeflow\Dashboard\Entity\Workspace;
use App\Makeflow\Place;
use Symfony\Component\HttpFoundation\Request;

class ZiJiJiaGongChengPinJueCePlace extends Place
{

    public $label = "自己加工成品方案选择";

    public $description = "";


    public function processAction(Request $request, Workspace $workspace)
    {
        //return $this->render("xxx.html.twig", []);
    }

}