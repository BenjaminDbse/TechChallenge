<?php

namespace App\Controller;

use App\Entity\Crew;
use App\Form\CrewType;
use App\Repository\CrewRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home", methods={"GET","POST"})
     */
    public function index(Request $request, CrewRepository $crewRepository): Response
    {
        $crew = new Crew();
        $form = $this->createForm(CrewType::class, $crew);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($crew);
            $entityManager->flush();

            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('home/index.html.twig', [
            'crew' => $crew,
            'form' => $form->createView(),
            'crews' => $crewRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="crew_delete", methods={"POST"})
     */
    public function delete(Request $request, Crew $crew): Response
    {
        if ($this->isCsrfTokenValid('delete'.$crew->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($crew);
            $entityManager->flush();
        }

        return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
    }
}
