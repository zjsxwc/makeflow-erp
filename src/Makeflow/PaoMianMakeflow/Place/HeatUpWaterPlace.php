<?php
/**
 * Created by PhpStorm.
 * User: wangchao
 * Date: 31/07/2018
 * Time: 8:53 AM
 */

namespace App\Makeflow\PaoMianMakeflow\Place;


use App\Makeflow\Dashboard\Entity\Workspace;
use App\Makeflow\PaoMianMakeflow\Entity\Note;
use App\Makeflow\AbstractPlace;
use Symfony\Component\HttpFoundation\Request;

class HeatUpWaterPlace extends AbstractPlace
{
    protected $name = "HeatUpWater";
    public $label = "烧水";

    public function processAction(Request $request, Workspace $workspace)
    {
        if ($request->getMethod() === "POST") {
            $content = $request->request->get("content");
            $note = new Note();
            $note->setWorkspaceId($workspace->getId());
            $note->setContent($content);
            $note->setName("烧水的日记" . time());
            $this->entityManager->persist($note);
            $this->entityManager->flush();

            $this->finishPlace($workspace);

            return $this->json([
                "code" => -1
            ]);
        }

        return $this->render("heat_up_water.html.twig", ['workspace' => $workspace]);


    }
}