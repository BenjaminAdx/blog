<?php

namespace App\Controller\Admin;

use App\Entity\Mark;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class MarkCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Mark::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Notes')
            ->setEntityLabelInSingular('Note');
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            IntegerField::new('mark'),
            TextField::new('user.fullName')
                ->hideOnForm(),
            TextField::new('recipe.name')
                ->hideOnForm(),
            DateTimeField::new('createdAt')
                ->hideOnForm(),
        ];
    }
}
