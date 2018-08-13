<?php
namespace App\Makeflow\CaiGouMakeflow\Place;

use App\Makeflow\Dashboard\Entity\Workspace;
use App\Makeflow\AbstractPlace;
use Symfony\Component\HttpFoundation\Request;

class ZiJiJiaGongChengPinJueCePlace extends AbstractPlace
{

    public $label = "自己加工成品方案选择";

    public $description = "";


    public function processAction(Request $request, Workspace $workspace)
    {
        //return $this->render("xxx.html.twig", []);
    }

}