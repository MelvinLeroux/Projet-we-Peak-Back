<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Sport;
use App\Entity\Filter;
use App\Entity\Activity;
use App\Entity\Difficulty;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\ActivityCrudController;
use App\Entity\Comment;
use App\Entity\Pictures;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {

        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(ActivityCrudController::class)->generateUrl());

    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
        ->setTitle('<div style="text-align: center;"><img src="assets/images/Logo_BWG.png" alt="WePeak Logo" style="margin-bottom: 20px;" /><br /><span style="font-size: 24px;">Back-Office</span></div>');
            
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Activités', 'fa fa-book', Activity::class);
        yield MenuItem::linkToCrud('Utilisateurs', 'fa fa-user', User::class);
        yield MenuItem::linkToCrud('Sports', 'fa fa-building', Sport::class);
        yield MenuItem::linkToCrud('Difficultés', 'fa fa-tags', Difficulty::class);
        yield MenuItem::linkToCrud('Commentaires', 'fa fa-comment', Comment::class);
        yield MenuItem::linkToCrud('Photos', 'fa fa-image', Pictures::class);
        yield MenuItem::linkToCrud('Filtres', 'fa fa-filter', Filter::class);
    }
}
