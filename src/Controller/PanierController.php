<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Panier;
use App\Entity\Produit;
use App\Form\PanierType;
use App\Repository\CommandeRepository;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/panier')]
class PanierController extends AbstractController
{

    public function __construct(private UserRepository $userRepository)
    {
    }



    #[Route('/', name: 'app_panier_index', methods: ['GET'])]
    public function index(): Response
    {
        $panier = $this->getPanier();

        return $this->render('panier/index.html.twig', [
            'panier' => $panier,
        ]);
    }


    /* fonctions speciales
    *
     *
     */

    #[Route('/passerCommande', name: 'app_panier_passerCommande' ) ]
    public function passerCommande(ManagerRegistry $doctrine,Request $request) : Response {
        $commandeRepository = $doctrine->getRepository(Commande::class);
        $panier = $this->getPanier();
        $em = $doctrine->getManager();

        //generer le id de la commande
        $idCommande = $this->generateCommandeId($doctrine);
        foreach ($panier as $prod) {
            $commande = new Commande();
            $commande->setIdcmd($idCommande);
            $commande->setIdpanier($this->getSessionId($request));
            $commande->setIdprod($prod['idProd']);
            $commande->setNomprod($prod['nomProd']);
            $commande->setQuantite($prod['quantite']);
            $commande->setPrixprod($prod['prixProd']);
            $commande->setPrixremise($this->getPrixRemise($prod['idProd']));
            $commande->setEtatcmd('en attente de confirmation');
            $commande->setDatecmd(date('Y/m/d H:i:s'));

            $em->persist($commande);
            $em->flush();
        }

        return $this->redirectToRoute('app_commande_indexFront' ,
            []
        );

    }


    #[Route('/{idprod}/ajouterPanier', name: 'app_panier_ajouter')]
    public function ajouterPanier(Produit $produit , ProduitRepository $produitRepository): Response {


        ///calcul quantité
        $quant = 1;
        $found=$this->findProd($produit->getIdprod());
        if ($found){
            $quant = $found['quant']+1;
        }

        //creer le produit a ajouter
        $prodArray = array('idProd' => $produit->getIdprod(),
            'nomProd' => $produit->getNomprod(),
            'quantite' => $quant,
            'prixProd' => $produit->getPrixprod(),
            'image' => $produit->getImagem1()
        );
        //valider lutilisation de unserialize
        if (!isset($_COOKIE['panier'])){
            $panier = [];
            array_push($panier , $prodArray);
            setcookie('panier',serialize($panier),time()+60*60*10,'/');
        }
        //si on a deja un panier
        else{
            $panier = unserialize($_COOKIE['panier']);
            if($found){
                $index = $found['index'];
                $panier[$index]['quantite'] = $prodArray['quantite'];
                setcookie('panier',serialize($panier),time()+60*60*72,'/');
            }
            else{
                array_push($panier , $prodArray);
                setcookie('panier',serialize($panier),time()+60*60*72,'/');
            }
        }

        return $this->redirectToRoute('app_shop');
    }

    public function findProd(int $id){
        if(!isset($_COOKIE['panier'])){
            return false;
        }
        $panier = unserialize($_COOKIE['panier']);
        if(!$panier){
            return false;
        }
        else{
            foreach ($panier as $prod){
                if($prod['idProd'] == $id){
                    return array(
                        'index' => array_search($prod,$panier),
                        'quant' => $prod['quantite']
                    );
                }
            }
        }
        return false;
    }

    public function getPanier(){
        if (!isset($_COOKIE['panier'])){
            $panier = [];
            setcookie('panier',serialize($panier),time()+60*60*10,'/');
            $_COOKIE['panier'] = serialize($panier);
            return $panier;
        }
        else{
            $panier = unserialize($_COOKIE['panier']);
            return $panier;
        }
    }

    #[Route('/{idprod}/deleteProd', name: 'app_panier_delete' ) ]
    public function deleteProd(Produit $produit){
        $panier = $this->getPanier();
        $found = $this->findProd($produit->getIdprod());
        if ($found){
            $index = $found['index'];
            unset($panier[$index]);
            setcookie('panier',serialize($panier),time()+3600*24,'/');
            $_COOKIE['panier'] = serialize($panier);
        }
        return $this->redirectToRoute('app_panier_index',
            ['panier' => $panier]
        );

    }

    #[Route('/{idprod}/increaseQ', name: 'app_panier_increaseQ' ) ]
    public function increaseQ(Produit $produit){
        $panier = $this->getPanier();
        $found = $this->findProd($produit->getIdprod());
        if ($found){
            $index = $found['index'];
            $panier[$index]['quantite'] = $panier[$index]['quantite'] + 1 ;
            setcookie('panier',serialize($panier),time()+3600*24,'/');
            $_COOKIE['panier'] = serialize($panier);
        }
        return $this->redirectToRoute('app_panier_index' ,
            ['panier' => $panier]
        );
    }

    #[Route('/{idprod}/decreaseQ', name: 'app_panier_decreaseQ' ) ]
    public function decreaseQ(Produit $produit , FlashyNotifier $flashy){
        $panier = $this->getPanier();
        $found = $this->findProd($produit->getIdprod());
        $index = $found['index'];
        $quantite = $panier[$index]['quantite'];
        if ($quantite <= 1){
            $flashy->warning('impossible de mettre la quantité à 0, clicker le bouton x pour supprimer le produit' , '');

        }
        if ($found and $quantite>1){
            $panier[$index]['quantite'] = $panier[$index]['quantite'] - 1 ;
            setcookie('panier',serialize($panier),time()+3600*24,'/');
            $_COOKIE['panier'] = serialize($panier);
        }
        return $this->redirectToRoute('app_panier_index' ,
            ['panier' => $panier ]
        );
    }




    public function getSessionId(Request $request) : int {
        $email = $request->getSession()->get(Security::LAST_USERNAME) ;
//        if (!isset($_COOKIE['session'])){
//            setcookie('session',$sessid,0,'/');
//            $_COOKIE['session'] = $sessid;
//            return $sessid;
//        }
//        else{
//            $sessid = $_COOKIE['session'];
//            return $sessid;
//        }

        $user = $this->userRepository->findOneBy(['email'=>$email]);
        return $user->getId();

    }

    public function generateCommandeId(ManagerRegistry $doctrine) : int {
        $maxId = 0;
        $commandes = $doctrine->getRepository(Commande::class)->findAll();

        foreach ($commandes as $commande){
            if ($commande->getIdcmd() > $maxId){
                $maxId = $commande->getIdcmd();
            }
        }
        return $maxId + 1 ;
    }

    public function getPrixRemise($id){
        $pc = new PanierController($this->userRepository);
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
        $pc = new PanierController();
        $panier = $pc->getPanier();
        $total = 0;

        foreach ($panier as $prod){
            $pr = $this->getPrixRemise($prod['idProd']);
            $total = $total + $prod['quantite'] * $pr;
        }
        return $total;
    }

}
