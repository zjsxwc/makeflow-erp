<?php
namespace App\Makeflow\CaiGouMakeflow\Place;

use App\Makeflow\Dashboard\Entity\Workspace;
use App\Makeflow\AbstractPlace;
use Symfony\Component\HttpFoundation\Request;

class CaiGouChengPinDaoHuoRuKuPlace extends AbstractPlace
{
    public $label = "采购成品到货入库";

    public $description = "";

    public function processAction(Request $request, Workspace $workspace)
    {
        //return $this->render("xxx.html.twig", []);
    }

}