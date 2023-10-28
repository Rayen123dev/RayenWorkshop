<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Form\SearchautherType;
use App\Repository\AuthorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

    #[Route('/showauthor', name: 'showauthor')]
    public function showauthor(AuthorRepository $A ,Request $request): Response
    {
        $Author=$A->findAll();
        $form = $this->createForm(SearchautherType::class);
        $form->handleRequest($request);
        $formView = $form->createView();
        if ($form->isSubmitted() && $form->isValid()) {
            $minBooks = $form->get('Min_Number')->getData();
            $maxBooks = $form->get('Max_Number')->getData();

            $Author = $A->findByNbBooksRange($minBooks, $maxBooks);
        }
        return $this->render('author/showauthor.html.twig', [
            'Author' => $Author,
            'form' => $formView,
        ]);
    }

    #[Route('/updateauthor/{id}', name: 'updateauthor')]
    public function updateauthor($id,AuthorRepository $AuthorRepository,ManagerRegistry $m, Request $req): Response
    {
        $em=$m->getManager();
        $dataid=$AuthorRepository->find($id);
        $form=$this->createForm(AuthorType::class,$dataid);
        $form->handleRequest($req);
        if($form->isSubmitted() and $form->isValid()){
            $em->persist($dataid);
            $em->flush();    
            return $this->redirectToRoute('showauthor');
        }

        return $this->renderForm('author/updateauthor.html.twig', [
            'form'=>$form
        ]);

    }

    #[Route('/addauthor', name: 'addauthor')]
    public function addauthor(ManagerRegistry $s, Request $req): Response
    {
        $em=$s->getManager();
        $author=new Author();
        $form=$this->createForm(AuthorType::class,$author);
        $form->handleRequest($req);
        if($form->isSubmitted() and $form->isValid()){
            $em->persist($author);
            $em->flush();    
            return $this->redirectToRoute('showauthor');
        }
        return $this->renderForm('author/addauthor.html.twig', [
            'form'=>$form
        ]);
    }

    #[Route('/deletezero', name: 'deletezero')]
    public function deletezero(AuthorRepository $A): Response
    {
        $Author=$A->deleteAuthorsWithZeroBooks();
        

        return $this->redirectToRoute('showauthor');
    }

    #[Route('/deleteauthor/{id}', name: 'deleteauthor')]
    public function deleteauthor($id,AuthorRepository $AuthorRepository,ManagerRegistry $s, Request $req): Response
    {
        $em=$s->getManager();
        $dataid=$AuthorRepository->find($id);
        $em->remove($dataid);
        $em->flush();

        

        return $this->redirectToRoute('showauthor');

    }
}
