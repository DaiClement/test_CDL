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
use App\Repository\AuthorRepository;
use App\Repository\CategoryRepository;
use App\Form\BookType;
use App\Form\AuthorType;
use App\Form\CategoryType;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;

class TestCDLController extends AbstractController
{
    /**
     * @Route("/", name="library")
     */
    public function index(BookRepository $repo, Request $request, EntityManagerInterface $manager): Response
    {
        // Read whole DB
        $books = $repo->findAll();

        // create Form for search
        $book = new Book();
        $form = $this->createForm(SearchType::class, $book);

        if ($request->request->get('search'))
        {
            // make variable shorter
            $request = $request->request->get('search');
            $list = [
                "name" => $request['name'],
                "categories" => null,
                "author" => null,
                "date" => 0
                // setup to null or 0
            ];

            // check every parameters from request
            if (array_key_exists("category", $request))
            {
                $list["categories"] = $request['category'];
            }

            if ($request['author'])
            {
                $list["author"] = $request['author'];
            }
                
            if ($request['date'])
            {
                $list["date"] = new \DateTime($request['date']);
            }

            // sql query
            $books = $repo->findByList($list);
        }

        return $this->render('test_cdl/index.html.twig', [
            'controller_name' => 'TestCDLController',
            'books' => $books,
            'formSearch' => $form->createView()
        ]);
    }

    // 2 routes for 1 functions.
    /**
     * @Route("/library/book/new", name="book_create")
     * @Route("/library/book/{id}/edit", name="book_edit")
     */
    // if no id is given, set $book to null
    public function create(Book $book = null , Request $request, EntityManagerInterface $manager)
    {
        if (!$book)
        {
            $book = new Book();
        }

        // form for Create or Edit book
        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($book);
            $manager->flush();
            // it could be book_create also
            return $this->redirectToRoute('library');
        }

        return $this->render('test_cdl/bookCreate.html.twig',[
            'formBook' => $form->createView(),
            'editMode' => $book->getId() !== null, // flag to know if it is create mode or edit mode
            'id' => $book->getId() // need book id for delete
        ]);
    }

    /**
     * @Route("/library/category/new", name="category_create")
     */
    public function categoryCreate(CategoryRepository $repo, Category $category = null, Request $request, EntityManagerInterface $manager)
    {
        // get all categories from DB
        $categories = $repo->findAll();

        if (!$category)
        {
            $category = new Category();
        }

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($category);
            $manager->flush();
            
            return $this->redirectToRoute('category_create');
        }

        return $this->render('test_cdl/categoryCreate.html.twig',[
            'formCategory' => $form->createView(),
            'categories' => $categories // list of categories
        ]);
    }
    
    /**
     * @Route("/library/author/new", name="author_create")
     */
    public function authorCreate(AuthorRepository $repo, Author $author = null, Request $request, EntityManagerInterface $manager)
    {
        // get all author from DB
        $authors = $repo->findAll();

        if (!$author)
        {
            $author = new Author();
        }

        $form = $this->createForm(AuthorType::class, $author);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($author);
            $manager->flush();
            
            return $this->redirectToRoute('author_create');
        }

        return $this->render('test_cdl/authorCreate.html.twig',[
            'formAuthor' => $form->createView(),
            'authors' => $authors // list of all authors
        ]);
    }

    //delete route
    /**
     * @Route("/library/{id}/delete", name="book_delete")
     */
    public function delete(Book $book, EntityManagerInterface $manager)
    {
        $manager->remove($book);
        $manager->flush();
        
        return $this->redirectToRoute('library');
    }
}
