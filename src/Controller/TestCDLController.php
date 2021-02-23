<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use App\Entity\Book;
use App\Entity\Author;
use App\Entity\Category;
use App\Repository\BookRepository;
use App\Form\BookType;
use Doctrine\ORM\EntityManagerInterface;

class TestCDLController extends AbstractController
{
    /**
     * @Route("/", name="library")
     */
    public function index(BookRepository $repo): Response
    {
        $books = $repo->findAll();

        return $this->render('test_cdl/index.html.twig', [
            'controller_name' => 'TestCDLController',
            'books' => $books
        ]);
    }

    /**
     * @Route("/library/new", name="library_create")
     * @Route("/library/{id}/edit", name="library_edit")
     */
    public function create(Book $book = null , Request $request, EntityManagerInterface $manager)
    {
        if (!$book)
        {
            $book = new Book();
        }

        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($book);
            $manager->flush();
            
            return $this->redirectToRoute('library');
        }

        return $this->render('test_cdl/create.html.twig',[
            'formBook' => $form->createView(),
            'editMode' => $book->getId() !== null
        ]);
    }

    /**
     * @Route("/temp", name="temp")
     */
    public function temp()
    {
        return $this->render('test_cdl/temp.html.twig');
    }
}
