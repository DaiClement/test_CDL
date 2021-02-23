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
        $books = $repo->findAll();

        $book = new Book();
        $form = $this->createForm(SearchType::class, $book);

        if ($request->request->get('search'))
        {
            $request = $request->request->get('search');
            $list = [
                "name" => $request['name'],
                "categories" => null,
                "author" => null,
                "date" => 0
            ];

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

            $books = $repo->findByList($list);
        }
        dump($books);
        return $this->render('test_cdl/index.html.twig', [
            'controller_name' => 'TestCDLController',
            'books' => $books,
            'formSearch' => $form->createView()
        ]);
    }

    /**
     * @Route("/library/book/new", name="book_create")
     * @Route("/library/book/{id}/edit", name="book_edit")
     */
    public function create(Book $book = null, Request $request, EntityManagerInterface $manager)
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

        return $this->render('test_cdl/bookCreate.html.twig',[
            'formBook' => $form->createView(),
            'editMode' => $book->getId() !== null,
            'id' => $book->getId()
        ]);
    }

    /**
     * @Route("/library/category/new", name="category_create")
     */
    public function categoryCreate(CategoryRepository $repo, Category $category = null, Request $request, EntityManagerInterface $manager)
    {
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
            'categories' => $categories
        ]);
    }
    
    /**
     * @Route("/library/author/new", name="author_create")
     */
    public function authorCreate(AuthorRepository $repo, Author $author = null, Request $request, EntityManagerInterface $manager)
    {
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
            'authors' => $authors
        ]);
    }

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
