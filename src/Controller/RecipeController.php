<?php

namespace App\Controller;

use App\Entity\Mark;
use App\Entity\Recipe;
use App\Form\MarkType;
use App\Form\RecipeType;
use App\Repository\MarkRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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

    #[IsGranted('ROLE_USER')]
    #[Route('/recette/publique', name: 'recipe.index.public', methods: ['GET'])]
    public function indexPublic(RecipeRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $recipes = $paginator->paginate(
            $repository->findAllPublic(null),
            $request->query->getInt('page', 1),
            6
        );

        return $this->render('pages/recipe/indexPublic.html.twig', [
            'recipes' => $recipes
        ]);
    }

    #[Security("recipe.isIsPublic() === true or (is_granted('ROLE_USER') and user === recipe.getUser())")]
    #[Route('/recette/{id<\d+>}', name: 'recipe.show', methods: ['GET', 'POST'])]
    public function show(Recipe $recipe, EntityManagerInterface $manager, Request $request, MarkRepository $markRepository, Mark $mark): Response
    {
        $existingMark = $markRepository->findOneBy(['user' => $this->getUser(), 'recipe' => $recipe]);

        if (!$existingMark) {
            $mark = new Mark();
        }
        $form = $this->createForm(MarkType::class, $mark);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mark = $form->getData();
            $mark->setUser($this->getUser())
                ->setRecipe($recipe);

            if (!$existingMark) {
                $manager->persist($mark);
                $this->addFlash('success', 'Votre note a été ajoutée avec succès');
            } else {
                $existingMark->setMark($form->getData()->getMark());
                $this->addFlash('success', 'Votre note a été modifiée avec succès');
            }
            $manager->flush();
            return $this->redirectToRoute('recipe.show', ['id' => $recipe->getId()]);
        }

        return $this->render('pages/recipe/show.html.twig', [
            'recipe' => $recipe,
            'form' => $form->createView(),
            'mark' => $mark,
            'existingMark' => $markRepository->findOneBy(['user' => $this->getUser(), 'recipe' => $recipe])
        ]);
    }

    /**
     * This function add a new recipe
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */

    #[IsGranted('ROLE_USER')]
    #[Route('/recette/nouvelle', name: 'recipe.new', methods: ['GET', 'POST'])]
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
