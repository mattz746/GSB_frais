<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListUsersController extends AbstractController
{
    #[Route('/listusers', name: 'app_list_users')]
    public function index(): Response
    {
        return $this->render('list_users/index.html.twig', [
            'controller_name' => 'ListUsersController',

        ]);
    }
}
