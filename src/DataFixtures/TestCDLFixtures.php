<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Book;
use App\Entity\Category;
use App\Entity\Author;
use App\Repository\CategoryRepository;
use App\Repository\AuthorRepository;

class TestCDLFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // data from https://github.com/lecomptoirdeslangues/test_CDL/blob/main/instruction.pdf

        $data = [
            [
                "name" => "Harry potter à l'école des sorciers",
                "date" => "1997",
                "author" => [
                    "name" => "J. K. Rolling",
                    "birth_date" => "1965/7/31"
                ],
                "category" => "Roman"
            ],
            [
                "name" => "Harry potter et la Chambre des secrets",
                "date" => "1998",
                "author" => [
                    "name" => "J. K. Rolling",
                    "birth_date" => "1965/7/31"
                ],
                "category" => "BD"
            ],
            [
                "name" => "Harry potter et le Prisonnier d'Azkaban",
                "date" => "1999",
                "author" => [
                    "name" => "J. K. Rolling",
                    "birth_date" => "1965/7/31"
                ],
                "category" => "Roman"
            ],
            [
                "name" => "Astérix le Gaulois",
                "date" => "1961",
                "author" => [
                    "name" => "Uderzo",
                    "birth_date" => "1927/4/25"
                ],
                "category" => "BD"
            ],
            [
                "name" => "La Serpe d'or",
                "date" => NULL,
                "author" => [
                    "name" => "Uderzo",
                    "birth_date" => "1927/4/25"
                ],
                "category" => "BD"
            ],
            [
                "name" => "One-Punch Man",
                "date" => NULL,
                "author" => NULL,
                "category" => "Manga"
            ],
            [
                "name" => "Naruto - Tome 1",
                "date" => "1995",
                "author" => [
                    "name" => "Masashi Kishimoto",
                    "birth_date" => "1974/11/8"
                ],
                "category" => "Manga"
            ],
            [
                "name" => "La jeune fille et la nuit",
                "date" => "2018",
                "author" => [
                    "name" => "Guillaume Musso",
                    "birth_date" => "1974/6/6"
                ],
                "category" => "Roman"
            ]
        ];

        for ($i = 0; $i < count($data); $i++)
        {
            // uniqueness
            $repo =$manager->getRepository(Category::class);
            if (!($category = $repo->findOneByName($data[$i]["category"])))
            {
                $category = new Category();
                $category->setName($data[$i]["category"]);
            }

            // uniqueness
            $repo =$manager->getRepository(Author::class);
            if ($data[$i]["author"])
            {
                if (!($author = $repo->findOneByName($data[$i]["author"]["name"])))
                {
                    $author = new Author();
                    $author->setName($data[$i]["author"]["name"])
                    ->setBirthDate(new \DateTime($data[$i]["author"]["birth_date"]));
                }
            }
            else
            {
                $author = NULL; //author can be null
            }

            if ($date = $data[$i]["date"])
            {
                $date = new \DateTime($data[$i]["date"]);
            }
            else
            {
                $date = NULL; // date of book can be null
            }

            $book = new Book();

            $book->setName($data[$i]["name"])
                 ->setDate($date)
                 ->setAuthor($author)
                 ->setCategory($category);

            $manager->persist($book);
            $manager->flush();
        }
    }
}
