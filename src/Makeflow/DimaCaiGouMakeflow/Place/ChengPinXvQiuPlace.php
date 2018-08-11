<?php
namespace App\Makeflow\DimaCaiGouMakeflow\Place;

use App\Makeflow\Dashboard\Entity\Workspace;
use App\Makeflow\Place;
use Symfony\Component\HttpFoundation\Request;

class ChengPinXvQiuPlace extends Place
{
    public $label = "成品需求";

    public $description = "";

    public function processAction(Request $request, Workspace $workspace)
    {
        //return $this->render("xxx.html.twig", []);
    }

}