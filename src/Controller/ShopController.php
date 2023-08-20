<?php

namespace App\Controller;

use App\Entity\Likeee;
use App\Entity\Produit;
use App\Entity\User;
use App\Repository\LikeRepository;
use App\Repository\ProduitRepository;
use App\Repository\UserRepository;
use http\Env\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends AbstractController
{
    #[Route('/shop', name: 'app_shop')]
    public function index(ProduitRepository $produitRepository,PaginatorInterface $paginator,\Symfony\Component\HttpFoundation\Request $request): Response
    {
        $paginateME = $produitRepository->findAll();
        $produits = $paginator->paginate(
            $paginateME, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            9 /*limit per page*/
        );
        return $this->render('shop/index.html.twig', [
            'controller_name' => 'ShopController',
            'produits' => $produits
        ]);
    }

    #[Route('/shop/{idprod}', name: 'app_shop_show')]
    public function showFront(Produit$produit, ProduitRepository $produitRepository){
        return $this->render(('produit/showFront.html.twig'),[
            'produits' => $produitRepository->findBy(['idprod'=>$produit->getIdprod()])
        ]);
    }

    #[Route('/shop/like/{idprod}', name: 'app_shop_like' , requirements: ['idprod' => '\d+']) ]
    public function like(Produit $produit , ProduitRepository $produitRepository,\Symfony\Component\HttpFoundation\Request $request,\Doctrine\Persistence\ManagerRegistry $doctrine,LikeRepository $likeRepository,UserRepository $userRepository){
        $userController = new UserController();
        $em = $doctrine->getManager();
        $user = $userController->getCurrentUser($request,$userRepository);

        if (!$this->hasLiked($user,$produit,$likeRepository)){
            if ($this->hasDisliked($user,$produit,$likeRepository)){
                //change dislike to like
                $produitRepository->changeDislikeToLike($user,$produit,$likeRepository);
                //update rating
                $produitRepository->updateRating($produit);
            }
            else{
                //add new like
                $like = new Likeee();
                $like->setIduser($user->getId());
                $like->setNom('Likee');
                $like->setProduit($produit->getIdprod());
                $em->persist($like);
                $em->flush();
                //update rating
                $produitRepository->updateRating($produit);
            }
        }

        return $this->redirectToRoute('app_shop');
    }


    #[Route('/shop/dislike/{idprod}', name: 'app_shop_dislike' , requirements: ['idprod' => '\d+']) ]
    public function dislike(Produit $produit , ProduitRepository $produitRepository,\Symfony\Component\HttpFoundation\Request $request,\Doctrine\Persistence\ManagerRegistry $doctrine,LikeRepository $likeRepository,UserRepository $userRepository){

        $userController = new UserController();
        $em = $doctrine->getManager();
        $user = $userController->getCurrentUser($request,$userRepository);

        if (!$this->hasDisliked($user,$produit,$likeRepository)){
            if ($this->hasliked($user,$produit,$likeRepository)){
                //change like to dislike
                $produitRepository->changeLikeToDislike($user,$produit,$likeRepository);
                //update rating
                $produitRepository->updateRating($produit);
            }
            else{
                //add new like object
                $dislike = new Likeee();
                $dislike->setIduser($user->getId());
                $dislike->setNom('Dislikee');
                $dislike->setProduit($produit->getIdprod());
                $em->persist($dislike);
                $em->flush();
                //update rating
                $produitRepository->updateRating($produit);
            }
        }



        return $this->redirectToRoute('app_shop');

    }


    public function hasLiked(User $user,Produit $produit,LikeRepository $likeRepository){

        $like = $likeRepository->findLike($user,$produit);

        if ($like == null){
            return false;
        }
        else{
            return true;
        }
    }

    public function hasDisliked(User $user,Produit $produit,LikeRepository $likeRepository){

     $dislike = $likeRepository->findDislike($user,$produit);

        if ($dislike == null){
            return false;
        }
        else{
            return true;
        }
    }




}
