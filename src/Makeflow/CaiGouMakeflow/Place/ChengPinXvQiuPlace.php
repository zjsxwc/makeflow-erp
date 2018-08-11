<?php
namespace App\Makeflow\CaiGouMakeflow\Place;

use App\Makeflow\Dashboard\Entity\Workspace;
use App\Makeflow\Place;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ChengPinXvQiuPlace extends Place
{
    public $label = "成品需求";

    public $description = "";

    public $canVisit = true;

    public function processAction(Request $request, Workspace $workspace)
    {
        //return $this->render("xxx.html.twig", []);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function visitAction(Request $request)
    {
        dump("这里可以进行该节点不是工作流主要内容的附属额外功能，比如查询剩余成品库存列表、查询剩余坯布库存列表等等");die;
    }

}