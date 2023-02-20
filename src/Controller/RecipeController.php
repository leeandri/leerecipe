<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class RecipeController extends AbstractController
{
    /**
     * This controller display all recipes
     *
     * @param RecipeRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/recipe', name: 'recipe.index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(RecipeRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $recipes = $paginator->paginate(
            $repository->findBy(['user' => $this->getUser()]),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    /**
     * This controller allows us to add a recipe
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/recipe/new', 'recipe.new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(
        Request $request,
        EntityManagerInterface $manager
    ): Response {
        $recipe = new Recipe;
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            $recipe->setUser($this->getUser());

            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'New recipe added !'
            );
            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('pages/recipe/new.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * This controller allows us to edit a recipe
     *
     * @param Recipe $recipe
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/recipe/edit/{id}', 'recipe.edit', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_USER') and user === recipe.getUser()")]
    public function edit(
        Recipe $recipe,
        Request $request,
        EntityManagerInterface $manager
    ): Response {
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();

            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'recipe edited !'
            );
            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('pages/recipe/new.html.twig', [
            'form' => $form->createView()
        ]);

        return $this->render('pages/recipe/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     *  This controller allows us to delete a recipe
     *
     * @param EntityManagerInterface $manager
     * @param Recipe $recipe
     * @return Response
     */
    #[Route('/recipe/delete/{id}', 'recipe.delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager, Recipe $recipe): Response
    {
        $manager->remove($recipe);
        $manager->flush();

        $this->addFlash(
            'success',
            'Recipe successfully deleted !'
        );

        return $this->redirectToRoute('recipe.index');
    }
}
