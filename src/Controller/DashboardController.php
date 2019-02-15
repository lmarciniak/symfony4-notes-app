<?php

namespace App\Controller;

use App\Entity\Note;
use App\Form\NoteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;


class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="dashboard")
     * @IsGranted("ROLE_USER")
     */
    public function index(): Response
    {
        $user = $this->getUser();
        $userNotesCounter = count($user->getNotes());
        $sharedNotesCounter = count($user->getSharedNotes());
        $note = new Note;
        $form = $this->createForm(NoteType::class, $note, [
            'action' => $this->generateUrl('note_add'),
            'method' => 'POST',
        ]);
        

        return $this->render('dashboard/index.html.twig', [
            'form_note'             => $form->createView(),
            'userNotesCounter'      => $userNotesCounter,
            'sharedNotesCounter'    => $sharedNotesCounter
        ]);
    }
}
