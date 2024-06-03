<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            EmailField::new('email'),
            ArrayField::new('roles'),
            BooleanField::new('blocked'),
            TextField::new('password')->onlyOnForms(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $blockUser = Action::new('blockUser', 'Block', 'fas fa-ban')
            ->linkToCrudAction('blockUser')
            ->setCssClass('btn btn-danger')
            ->displayIf(fn (User $user) => !$user->isBlocked());

        $unblockUser = Action::new('unblockUser', 'Unblock', 'fas fa-check')
            ->linkToCrudAction('unblockUser')
            ->setCssClass('btn btn-success')
            ->displayIf(fn (User $user) => $user->isBlocked());

        return $actions
            ->add(Crud::PAGE_INDEX, $blockUser)
            ->add(Crud::PAGE_INDEX, $unblockUser)
            ->add(Crud::PAGE_DETAIL, $blockUser)
            ->add(Crud::PAGE_DETAIL, $unblockUser);
    }

    public function blockUser(EntityManagerInterface $entityManager, AdminUrlGenerator $adminUrlGenerator, Request $request): RedirectResponse
    {
        $userId = $request->query->get('entityId');
        $user = $entityManager->getRepository(User::class)->find($userId);
        $user->setBlocked(true);
        $entityManager->flush();

        $url = $adminUrlGenerator->setController(UserCrudController::class)->setAction(Crud::PAGE_INDEX)->generateUrl();
        return $this->redirect($url);
    }

    public function unblockUser(EntityManagerInterface $entityManager, AdminUrlGenerator $adminUrlGenerator, Request $request): RedirectResponse
    {
        $userId = $request->query->get('entityId');
        $user = $entityManager->getRepository(User::class)->find($userId);
        $user->setBlocked(false);
        $entityManager->flush();

        $url = $adminUrlGenerator->setController(UserCrudController::class)->setAction(Crud::PAGE_INDEX)->generateUrl();
        return $this->redirect($url);
    }
}
