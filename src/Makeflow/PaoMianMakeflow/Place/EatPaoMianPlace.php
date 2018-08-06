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
use App\Makeflow\Place;
use Symfony\Component\HttpFoundation\Request;

class EatPaoMianPlace extends Place
{
    protected $name = "EatPaoMian";
    public $label = "吃泡面";


    public function processAction(Request $request, Workspace $workspace)
    {
        if ($request->getMethod() === "POST") {
            $content = $request->request->get("content");
            $note = new Note();
            $note->setWorkspaceId($workspace->getId());
            $note->setContent($content);
            $note->setName("吃泡面的日记" . time());
            $this->entityManager->persist($note);
            $this->entityManager->flush();

            $this->finishPlace($workspace);

            return $this->json([
                "code" => -1
            ]);
        }
        $historyNoteList = $this->entityManager->getRepository("PaoMianMakeflow:Note")->getNoteListByWorkspaceId($workspace->getId());

        return $this->render("eat_pao_mian.html.twig", ['workspace' => $workspace, 'historyNoteList' =>  $historyNoteList]);


    }
}