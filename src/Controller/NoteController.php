<?php

namespace App\Controller;

use App\Entity\Note;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;

class NoteController extends AbstractController
{
    /**
     * @Route("/add", name="note_add")
     * @IsGranted("ROLE_USER")
     */
    public function add(Request $request): Response
    {
        $arr = $request->get('note');
        if (empty($arr)) {
            return $this->json(NULL);
        } else {
            $user = $this->getUser();
            $note = new Note;
            $note->setUser($user);
            $note->setContent($arr['content']);
            $note->setTitle($arr['title']);
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($note);
            $em->flush();
            $arr['id'] = $note->getId();
            return $this->json($arr);
        }
    }

    /**
     * @Route("/list", name="note_list")
     * @IsGranted("ROLE_USER")
     */
    public function list(): Response
    {
        $user = $this->getUser();
        $notes = $user->getNotes();
        $arr = [];
        $i = 0;
        foreach ($notes as $note) {
            $arr[$i]['title'] = $note->getTitle();
            $arr[$i]['content'] = $note->getContent();
            $arr[$i]['id'] = $note->getId();
            $i++;
        }
        return $this->json($arr);
    }

    /**
     * @Route("/shared", name="note_shared")
     * @IsGranted("ROLE_USER")
     */
    public function shared()
    {
        $user = $this->getUser();
        $notes = $user->getSharedNotes();
        $arr = [];
        $i = 0;
        foreach ($notes as $note) {
            $arr[$i]['title'] = $note->getTitle();
            $arr[$i]['content'] = $note->getContent();
            $arr[$i]['owner'] = $note->getUser()->getEmail();
            $i++;
        }
        return $this->json($arr);
    }

    /**
     * @Route("/share/{id<\d+>}", name="note_share")
     * @IsGranted("ROLE_USER")
     */
    public function share(Request $request, int $id): Response
    {
        $noteRepository = $this->getDoctrine()->getRepository(Note::class);
        $note = $noteRepository->findOneBy([
            'id'    => $id
        ]);
        if ($note && $note->getUser() == $this->getUser()) {
            if ($request->get('email')) {
                $userRepository = $this->getDoctrine()->getRepository(User::class);
                $user = $userRepository->findOneBy([
                    'email'     => $request->get('email')
                ]);
                if ($user) {
                    $user->addSharedNote($note);
                    $this->getDoctrine()->getManager()->flush();
                }
            }
            return $this->render('note/share.html.twig', [
    
            ]);
        } else {
            return $this->redirectToRoute("dashboard");
        }
    }

    /**
     * @Route("/remove/{id<\d+>}", name="note_remove")
     */
    public function remove(int $id): Response
    {
        $noteRepository = $this->getDoctrine()->getRepository(Note::class);
        $note = $noteRepository->findOneBy([
            'id'    => $id
        ]);
        if ($note && $note->getUser() == $this->getUser()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($note);
            $em->flush();
            return $this->json(['id' => $id]);
        } else {
            return $this->json(['id' => 0]);
        }
    } 
}
