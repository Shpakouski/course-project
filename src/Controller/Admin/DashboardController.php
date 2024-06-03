<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Item;
use App\Entity\ItemCollection;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Admin Dashboard');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Users', 'fa-solid fa-users', User::class);
        yield MenuItem::linkToCrud('Categories', 'fa-regular fa-rectangle-list', Category::class);
        yield MenuItem::linkToCrud('Collections', 'fa-solid fa-icons', ItemCollection::class);
        yield MenuItem::linkToCrud('Items', 'fa-regular fa-clipboard', Item::class);
    }
}
