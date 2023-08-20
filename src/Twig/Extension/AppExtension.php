<?php

namespace App\Twig\Extension;

use App\Controller\PanierController;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;


class AppExtension extends AbstractExtension
{

    public function __construct(private ManagerRegistry $doctrine  ,private UserRepository $userRepository)
    {
//        parent::__construct($registry, Category::class);
    }
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('filter_name', [$this, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getPanier', [$this, 'getPanier']),
            new TwigFunction('getPrixRemise',[$this, 'getPrixRemise']),
            new TwigFunction('getTotal', [$this, 'getTotal']),
            new TwigFunction('getRole', [$this, 'getRole']),
            new TwigFunction('getImgName',[$this,'getImgName'])
        ];
    }

    public function getPanier()
    {
        if (!isset($_COOKIE['panier'])){
            $panier = [];
            setcookie('panier',serialize($panier),time()+60*60*10,'/');
        }

        else{
            $panier = unserialize($_COOKIE['panier']);
        }

        return $panier;
    }

    public function getPrixRemise($id){
        $pc = new PanierController(new UserRepository($this->doctrine));
        $panier = $pc->getPanier();
        $prixRemise = 0;
        $found = $pc->findProd($id);
        if ($found){
            $index = $found['index'];
            if($found['quant'] > 5){
                return 0.8 * $panier[$index]['prixProd'];
            }
            else{
                return $panier[$index]['prixProd'];
            }
        }
        return $prixRemise;
    }

    public function getTotal(){
        $pc = new PanierController(new UserRepository($this->doctrine));
        $panier = $pc->getPanier();
        $total = 0;

        foreach ($panier as $prod){
            $pr = $this->getPrixRemise($prod['idProd']);
            $total = $total + $prod['quantite'] * $pr;
        }
        return $total;
    }

    public function getRole(Request $request){

        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user=$this->userRepository->findOneBy(['email'=>$email]);

        return  $user->getRoles()[0];
    }

    public function getImgName($path){
        return $imgName = basename($path);
    }
}
