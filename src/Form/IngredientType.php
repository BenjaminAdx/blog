<?php

namespace App\Form;

use App\Entity\Ingredient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class IngredientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $labelSubmit = "";
        switch ($options['label']) {
            case 'ingredient.new':
                $labelSubmit = "Créer mon ingrédient";
                break;
            case 'ingredient.edit':
                $labelSubmit = "Modifier mon ingrédient";
                break;
        };
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => '2',
                    'maxlength' => '50'
                ],
                'label' => 'Nom',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],

            ])
            ->add('quantity', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Quantité (précisé grammes, litres, ...)',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary mt-4'
                ],
                'label' => $labelSubmit
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ingredient::class,
            'validation_groups' => function (FormInterface $form) {
                $data = $form->getData();
                $groups = ['Default'];

                if ($data && $data->getId() === null) {
                    $groups[] = 'create';
                }

                return $groups;
            }
        ]);
    }
}
