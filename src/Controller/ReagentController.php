<?php

namespace App\Controller;

use App\Entity\Reagent;
use App\Form\ReagentType;
use App\Repository\ReagentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/reagent")
 */
class ReagentController extends AbstractController
{
    /**
     * @Route("/", name="reagent_index", methods={"GET"})
     */
    public function index(ReagentRepository $reagentRepository): Response
    {
        return $this->render('reagent/index.html.twig', [
            'reagents' => $reagentRepository->findAll(),
        ]);
    }

    /**
     * @Route("/finder", name="reagent_finder")
     */
    public function ajaxAction(Request $request, ReagentRepository $reagentRepository) {
        if ($request->getMethod() == 'GET') {
            $name = $request->query->get('name');;
            // var_dump($name);
            $rgts = $reagentRepository->findByName($name);
            $jsonstring = '[';
            foreach ($rgts as $rgt) {
                $jsonstring .= '{"id":"'. $rgt->getId() 
                    .'","name":"'. $rgt->getName() 
                    .'","formula":"'. $rgt->getFormula() 
                    .'","cas":"'. $rgt->getCas() 
                    .'","private":"'. $rgt->getPrivate() 
                    .'","secure":"'. $rgt->getSecure() 
                    .'","notes":"'. $rgt->getNotes() .'"},';
            }
            $jsonstring .= ']';
            $jsonstring = str_replace('},]', '}]', $jsonstring);
            return new Response($jsonstring);
        } else {
            return new Response('no hay na');
        }
    }

    /**
     * @Route("/new", name="reagent_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $reagent = new Reagent();
        $form = $this->createForm(ReagentType::class, $reagent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reagent);
            $entityManager->flush();

            return $this->redirectToRoute('reagent_index');
        }

        return $this->render('reagent/new.html.twig', [
            'reagent' => $reagent,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="reagent_show", methods={"GET"})
     */
    public function show(Reagent $reagent): Response
    {
        return $this->render('reagent/show.html.twig', [
            'reagent' => $reagent,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="reagent_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Reagent $reagent): Response
    {
        $form = $this->createForm(ReagentType::class, $reagent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('reagent_index');
        }

        return $this->render('reagent/edit.html.twig', [
            'reagent' => $reagent,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="reagent_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Reagent $reagent): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reagent->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($reagent);
            $entityManager->flush();
        }

        return $this->redirectToRoute('reagent_index');
    }
}
