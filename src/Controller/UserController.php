<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    /**
     * This function edit a user
     *
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */ #[Route('/utilisateur/edition/{id}', 'user.edit', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_USER') and user === choosenUser")]
    public function edit(User $choosenUser, Request $request, EntityManagerInterface $manager): Response
    {

        $form = $this->createForm(UserType::class, $choosenUser);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $choosenUser = $form->getData();
            $manager->persist($choosenUser);
            $manager->flush();

            $this->addFlash('success', 'Profil modifié avec succès');

            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('pages/user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/utilisateur/edition-mot-de-passe/{id}', 'user.edit.password', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_USER') and user === choosenUser")]
    public function editPassword(User $choosenUser, Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(UserPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($hasher->isPasswordValid($choosenUser, $form->getData()['plainPassword'])) {

                $choosenUser->setUpdatedAt(new \DateTimeImmutable);
                $choosenUser->setPlainPassword($form->getData()['newPassword']);

                $manager->persist($choosenUser);
                $manager->flush();

                $this->addFlash('success', 'Mot de passe modifié avec succès');

                return $this->redirectToRoute('recipe.index');
            } else {
                $this->addFlash('danger', 'Mot de passe incorrect');
            }
        }

        return $this->render('pages/user/edit_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
