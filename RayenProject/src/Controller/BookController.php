<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookrechercheType;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

    #[Route('/showbook', name: 'showbook')]
    public function showbook(BookRepository $B, Request $request): Response
    {
        $Book=$B->findAll();
        $Book = $B->triByAuthor();
        $form = $this->createForm(BookrechercheType::class);
        $form->handleRequest($request);
        $formView = $form->createView();
        $nbrp=$B->getpub();
        $nbrpno=$B->getpubno();
        $nbr_cat_sc=$B->getnb_cat_sc();
        if ($form->isSubmitted() && $form->isValid()) {
            $id = $form->getData(); 

            $Book = $B->searchbyref($id);
        } 
        

        return $this->render('book/showbook.html.twig', [
            'Book' => $Book ,
            'form'=>$formView,
            'nbrp'=>$nbrp,
            'nbrp'=>$nbrp,
            'nbrpno'=>$nbrpno,
            'nbr_cat_sc'=>$nbr_cat_sc,
        ]);
    }

    #[Route('/updatebook/{id}', name: 'updatebook')]
    public function updatebook($id,BookRepository $BookRepository,ManagerRegistry $m, Request $req): Response
    {
        $em=$m->getManager();
        $dataid=$BookRepository->find($id);
        $form=$this->createForm(BookType::class,$dataid);
        $form->handleRequest($req);
        if($form->isSubmitted() and $form->isValid()){
            $em->persist($dataid);
            $em->flush();    
            return $this->redirectToRoute('showbook');
        }

        return $this->renderForm('book/updatebook.html.twig', [
            'form'=>$form
        ]);

    }

    #[Route('/addbook', name: 'addbook')]
    public function addbook(ManagerRegistry $s, Request $req): Response
    {
        $em=$s->getManager();
        $book=new Book();
        $form=$this->createForm(BookType::class,$book);
        $form->handleRequest($req);
        if($form->isSubmitted() and $form->isValid()){
            $em->persist($book);
            $em->flush();    
            return $this->redirectToRoute('showbook');
        }
        return $this->renderForm('book/addbook.html.twig', [
            'form'=>$form
        ]);
    }
    #[Route('/searchbook', name: 'searchbook')]
    public function searchbook(BookRepository $B, Request $request): Response
    {
        $Book = $B->searchBook();
        $form = $this->createForm(BookrechercheType::class);
        $form->handleRequest($request);
        $formView = $form->createView();
        if ($form->isSubmitted() && $form->isValid()) {
            $id = $form->getData(); 

            $Book = $B->searchbyref($id);
        } 
        

        return $this->render('book/showbooks.html.twig', [
            'Book' => $Book ,
        ]);
    }

    #[Route('/showbookDate', name: 'showbookDate')]
    public function showbookDate(BookRepository $B, Request $request): Response
    {
        $Book=$B->getbookwithdate();
        

        return $this->render('book/showbookDate.html.twig', [
            'Book' => $Book ,
        ]);
    }
    
}
