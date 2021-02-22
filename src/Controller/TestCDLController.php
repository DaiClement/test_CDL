<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\BookRepository;

class TestCDLController extends AbstractController
{
    /**
     * @Route("/", name="home")
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
     * @Route("/temp", name="temp")
     */
    public function temp()
    {
        return $this->render('test_cdl/temp.html.twig');
    }
}
