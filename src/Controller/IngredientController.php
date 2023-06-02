<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IngredientController extends AbstractController
{
    /**
     * This function display all ingredients
     *
     * @param IngredientRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/ingredient', name: 'ingredient.index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients = $paginator->paginate(
            $repository->findBy(['user' => $this->getUser()]), /* query */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients' => $ingredients
        ]);
    }

    /**
     * This function add a new ingredient
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */ #[Route('/ingredient/nouveau', 'ingredient.new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient, ['label' => "ingredient.new"]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            $ingredient->setUser($this->getUser());

            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash('success', 'Ingrédient ajouté avec succès');

            return $this->redirectToRoute('ingredient.index');
        }
        return $this->render('pages/ingredient/new.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * This function update one ingredient by id
     *
     * @param Ingredient $ingredient
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ingredient/edition/{id}', 'ingredient.edit', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_USER') and user === ingredient.getUser()")]
    public function edit(Ingredient $ingredient, Request $request, EntityManagerInterface $manager): Response
    {

        $form = $this->createForm(IngredientType::class, $ingredient, ['label' => "ingredient.edit"]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash('success', 'Ingrédient modifié avec succès');

            return $this->redirectToRoute('ingredient.index');
        }

        return $this->render('pages/ingredient/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * This function delete one ingredient
     *
     * @param EntityManagerInterface $manager
     * @param Ingredient $ingredient
     * @return Response
     */ #[Route('/ingredient/suppression/{id}', 'ingredient.delete', methods: ['GET'])]
    #[Security("is_granted('ROLE_USER') and user === ingredient.getUser()")]
    public function delete(EntityManagerInterface $manager, Ingredient $ingredient): Response
    {
        $manager->remove($ingredient);
        $manager->flush();
        $this->addFlash('success', 'Ingrédient supprimé avec succès');

        return $this->redirectToRoute('ingredient.index');
    }
}
