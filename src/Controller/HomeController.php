<?php

namespace App\Controller;

use App\Form\ChooseYear;
use App\Form\UserType;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(FlashyNotifier $flashy): Response
    {
//        $flashy->success('A Verification Email has been sent');

        return $this->render('home/index.html.twig');

    }
    #[Route('/home-front', name: 'app_home_front')]
    public function indexfront(): Response
    {
//        $flashy->success('A Verification Email has been sent');

        return $this->render('front/index.html.twig');

    }
    #[Route('/home2', name: 'app_home2')]
    public function index2(): Response
    {

        return $this->render('base-back.html.twig');

    }

    #[Route('/welcome', name: 'app_welcome')]
    public function base(): Response
    {

        return $this->render('/home/welcome.html.twig');

    }

    #[Route('/firstfront', name: 'app_first')]
    public function first(): Response
    {

        return $this->render('base-front.html.twig');

    }



    #[Route('users/stats', name: 'app_stats')]
    public function statistiques(UserRepository $userRepository, CategoryRepository $categoryRepository,): Response
    {

        // choose year

            // nb users par mois

            $users = $userRepository->countByMonthAndYear('2022');
            $months = [];
            $count_user = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            $month_year = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];

            foreach ($users as $user) {
                if ((in_array($user['month'], $month_year))) {
                    $index = (int)$user['month'] - 1;
                    $count_user[$index] = $user['count'];
                    $months [] = $user['month'];


                }


            }




        $categories = $categoryRepository->findAll();

        $count_cat = [];
        $categnom = [];

        // nb user par category
        foreach ($categories as $category) {

            $categnom [] = $category->getNom();

            $count_cat [] = count($category->getUsers());


        }


        $users3 = $userRepository->countByYear('2022');
        $years = [];
        $count_user_year = [];
        foreach ($users3 as $user) {

            $years [] = $user['year'];
            $count_user_year[] = $user['count'];

        }

        return $this->render('user/stats.html.twig',[
            'categnom'=>json_encode($categnom),
            'count_cat'=>json_encode($count_cat),
            'months'=>json_encode($months),
            'month_year'=>json_encode($month_year),
            'count_user'=>json_encode($count_user),
            'years'=>json_encode($years),
            'count_user_year'=>json_encode($count_user_year),






        ]);

    }


}
