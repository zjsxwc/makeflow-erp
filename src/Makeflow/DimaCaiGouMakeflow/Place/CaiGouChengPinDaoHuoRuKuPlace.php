<?php
namespace App\Makeflow\DimaCaiGouMakeflow\Place;

use App\Makeflow\Dashboard\Entity\Workspace;
use App\Makeflow\Place;
use Symfony\Component\HttpFoundation\Request;

class CaiGouChengPinDaoHuoRuKuPlace extends Place
{
    public $label = "采购成品到货入库";

    public $description = "";

    public function processAction(Request $request, Workspace $workspace)
    {
        //return $this->render("xxx.html.twig", []);
    }

}