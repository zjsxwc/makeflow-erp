<?php
namespace App\Makeflow\CaiGouMakeflow\Place;

use App\Makeflow\Dashboard\Entity\Workspace;
use App\Makeflow\AbstractPlace;
use Symfony\Component\HttpFoundation\Request;

class ZiJiJiaGongChengPinCaiGouPiBuDaoHuoRuKuPlace extends AbstractPlace
{
    public $label = "坯布到货开始入库";

    public $description = "";

    public function processAction(Request $request, Workspace $workspace)
    {
        //return $this->render("xxx.html.twig", []);
    }

}