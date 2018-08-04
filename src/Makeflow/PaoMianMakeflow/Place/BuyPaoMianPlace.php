<?php
/**
 * Created by PhpStorm.
 * User: wangchao
 * Date: 31/07/2018
 * Time: 8:52 AM
 */

namespace App\Makeflow\PaoMianMakeflow\Place;


use App\Makeflow\Dashboard\Entity\Workspace;
use App\Makeflow\Place;
use Symfony\Component\HttpFoundation\Request;

class BuyPaoMianPlace extends Place
{
    protected $name = "BuyPaoMian";
    public $label = "买泡面";
    public $description = "吃泡面当然要去超市买泡面先了";
    public function processAction(Request $request, Workspace $workspace)
    {
        $noteList = $this->entityManager->getRepository("PaoMianMakeflow:Note")->findMoreComplex();
        return $this->render("buy_pao_mian.html.twig", ["noteList" => $noteList, 'workspace' => $workspace]);
    }

}