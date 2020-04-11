<?php

namespace App\Controller;

use App\Entity\Reagent;
use App\Form\ReagentType;
use App\Repository\ReagentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/reagent")
 */
class ReagentController extends AbstractController
{
    /**
     * @Route("/", name="reagent_index", methods={"GET"})
     */
    public function index(ReagentRepository $reagentRepository, Security $security): Response
    {
        $rgts = $reagentRepository->findAll();
        $currentUser = $security->getUser();
        for ($i=0; $i < count($rgts); $i++) { 
            if ($rgts[$i]->getPrivate() && $rgts[$i]->getOwner() != $currentUser) {
                unset($rgts[$i]);
            } else if ($rgts[$i]->getSecure() && !$currentUser->getAdmin()) {
                unset($rgts[$i]);
            }
        }
        return $this->render('reagent/index.html.twig', [
            'currentUser' => $currentUser->getUsername(),
            'reagents' => $rgts,
        ]);
    }

    /**
     * @Route("/finder", name="reagent_finder")
     */
    public function ajaxAction(Request $request, ReagentRepository $reagentRepository, Security $security) {
        if ($request->getMethod() == 'GET') {
            $name = $request->query->get('name');
            $currentUser = $security->getUser();
            self::logSearch($name, $currentUser);
            $rgts = $reagentRepository->findByName($name);
            $jsonstring = '[';
            foreach ($rgts as $rgt) {
                if ($rgt->getPrivate() && $rgt->getOwner() != $currentUser) {
                    continue;
                }
                if ($rgt->getSecure() && !$currentUser->getAdmin()) {
                    continue;
                }
                $jsonstring .= '{"id":"'. $rgt->getId() 
                    .'","name":"'. $rgt->getName() 
                    .'","formula":"'. $rgt->getFormula() 
                    .'","cas":"'. $rgt->getCas() 
                    .'","private":"'. $rgt->getPrivate() 
                    .'","secure":"'. $rgt->getSecure() 
                    .'","owner":"'. $rgt->getOwner()->getUsername() 
                    .'","notes":"'. $rgt->getNotes() .'"},';
            }
            $jsonstring .= ']';
            $jsonstring = str_replace('},]', '}]', $jsonstring);
            return new Response($jsonstring);
        } else {
            return new Response('no hay na');
        }
    }

    private static function logSearch($query, $user) {
        $log = file_get_contents(__DIR__.'/logs/'. $user->getId() .'.txt');
        $log .= $query .'@'. time() .';';
        file_put_contents(__DIR__.'/logs/'. $user->getId() .'.txt', $log);
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
    public function edit(Request $request, Reagent $reagent, Security $security): Response {
        if ($security->getUser() != $reagent->getOwner()) {
            return $this->render('reagent/not_editable.html.twig', ['cause' => 'You are not the owner of this reagent.']);
        }
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
