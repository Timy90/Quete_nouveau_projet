<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/blog")
 */
class BlogController extends AbstractController
{
    /**
     * Show all row from category's entity
     *
     * @Route("/", name="blog_index")
     * @return Response A response instance
     */
    public function index() : Response
    {
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();

        if (!$categories) {
            throw $this->createNotFoundException(
                'No Category found in category\'s table.'
            );
        }

        return $this->render(
            'blog/index.html.twig',
            ['categories' => $categories]
        );
    }

    /**
     * Getting a article with a formatted slug for title
     *
     * @param string $slug The slugger
     *
     * @Route("/{slug<^[a-z0-9- ]+$/>}",
     *     defaults={"slug" = null},
     *     name="blog_slug")
     *  @return Response A response instance
     */
    public function show($slug) : Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find an article in article\'s table.');
        }

        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );

        $article = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);

        if (!$article) {
            throw $this->createNotFoundException(
                'No article with '.$slug.' title, found in article\'s table.'
            );
        }

        return $this->render(
            'blog/showArticle.html.twig',
            [
                'article' => $article,
                'slug' => $slug
            ]
        );
    }

    /**
     * Show all row from category's entity
     *
     * @Route("/category/{category}", name="blog_show_category")
     *
     * @return Response A response instance
     */
    public function showByCategory(string $category): Response
    {
        $categoryRepository = $this->getDoctrine()
            ->getRepository(Category::class);

        $articleRepository = $this->getDoctrine()
            ->getRepository(Article::class);

        $categoryFind = $categoryRepository->findOneByName($category);
        $articleFind = $articleRepository->findBy(
            ["category" => $categoryFind->getId()],
            ["id" => 'DESC'],
            3);
        return $this->render('blog/category.html.twig', [
            'articles' => $articleFind,
            'category' => $categoryFind
        ]);
    }

    /**
     * Getting a article with a for id
     *
     * @param string $id The id article
     *
     * @Route("/category/article/{id}",
     *     defaults={"id" = null},
     *     name="blog_show_article")
     *
     * @return Response A response instance
     */
    public function showByArticle(Article $id): Response
    {
        $article = $this->getDoctrine()
            ->getRepository(Article::class)
            ->find($id);

        if (!$article) {
            throw $this->createNotFoundException(
                'No article with '.$article.' title, found in article\'s table.'
            );
        }
        return $this->render(
            'blog/showArticle.html.twig',
            [
                'article' => $article,
            ]
        );

    }
}
