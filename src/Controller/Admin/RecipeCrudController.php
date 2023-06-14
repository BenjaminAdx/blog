<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class RecipeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Recipe::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Recettes')
            ->setEntityLabelInSingular('Recette')
            ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig');
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            TextField::new('name'),
            TextField::new('imageFile')
                ->hideOnIndex()
                ->setFormType(VichImageType::class),
            IntegerField::new('time'),
            IntegerField::new('nbPeople'),
            IntegerField::new('difficulty'),
            TextareaField::new('description')
                ->hideOnIndex()
                ->setFormType(CKEditorType::class),
            IntegerField::new('price'),
            BooleanField::new('isFavorite')
                ->hideOnIndex(),
            BooleanField::new('isPublic'),
            CollectionField::new('ingredients'),
            DateTimeField::new('createdAt')
                ->hideOnForm(),
        ];
    }
}
