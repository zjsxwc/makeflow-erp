<?php
/**
 * Created by PhpStorm.
 * User: wangchao
 * Date: 31/07/2018
 * Time: 8:52 AM
 */

namespace App\Makeflow\PaoMianMakeflow\Place;


use App\Makeflow\Dashboard\Entity\Workspace;
use App\Makeflow\PaoMianMakeflow\Entity\Note;
use App\Makeflow\Place;
use Symfony\Component\HttpFoundation\Request;

class BuyPaoMianPlace extends Place
{
    protected $name = "BuyPaoMian";
    public $label = "买泡面";
    public $description = "吃泡面当然要去超市买泡面先了";

    public function processAction(Request $request, Workspace $workspace)
    {

        //$noteList = $this->entityManager->getRepository("PaoMianMakeflow:Note")->findMoreComplex();

        if ($request->getMethod() === "POST") {
            $content = $request->request->get("content");
            $note = new Note();
            $note->setWorkspaceId($workspace->getId());
            $note->setContent($content);
            $note->setName("买泡面的日记" . time());
            $this->entityManager->persist($note);
            $this->entityManager->flush();

            $this->finishPlace($workspace);

            return $this->json([
                "code" => -1
            ]);

        }

        return $this->render("buy_pao_mian.html.twig", ['workspace' => $workspace]);

    }

}