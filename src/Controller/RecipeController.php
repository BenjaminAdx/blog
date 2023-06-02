<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    /**
     * This function display all recipes
     *
     * @param RecipeRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */ #[Route('/recette', name: 'recipe.index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(RecipeRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $recipes = $paginator->paginate(
            $repository->findBy(['user' => $this->getUser()]),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }

    /**
     * This function add a new recipe
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */ #[Route('/recette/nouvelle', 'recipe.new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe, ['label' => "recipe.new"]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            $recipe->setUser($this->getUser());

            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash('success', 'Recette ajoutée avec succès');
            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('pages/recipe/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * This function edit a recipe
     *
     * @param Request $request
     * @param Recipe $recipe
     * @param EntityManagerInterface $manager
     * @return Response
     */ #[Route('/recette/edition/{id}', 'recipe.edit', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_USER') and user === recipe.getUser()")]
    public function edit(Request $request, Recipe $recipe, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe, ['label' => "recipe.edit"]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            $manager->persist($recipe);
            $manager->flush();
            $this->addFlash('success', 'Recette modifiée avec succès');
            return $this->redirectToRoute('recipe.index');
        }
        return $this->render('pages/recipe/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/recette/suppression/{id}', 'recipe.delete', methods: ['GET'])]
    #[Security("is_granted('ROLE_USER') and user === recipe.getUser()")]
    public function delete(Recipe $recipe, EntityManagerInterface $manager): Response
    {
        $manager->remove($recipe);
        $manager->flush();
        $this->addFlash('success', 'Recette supprimée avec succès');

        return $this->redirectToRoute('recipe.index');
    }
}
