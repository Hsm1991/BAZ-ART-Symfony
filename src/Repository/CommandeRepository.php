<?php

namespace App\Repository;

use App\Controller\PanierController;
use App\Entity\Commande;
use ContainerXWcjAIg\getDoctrine_QueryDqlCommandService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use http\Env\Request;
use PhpParser\Node\Expr\Array_;

/**
 * @extends ServiceEntityRepository<Commande>
 *
 * @method Commande|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commande|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commande[]    findAll()
 * @method Commande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    public function save(Commande $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Commande $commande): void
    {
        $em = $this->getEntityManager();

        $query = $em->createQuery('DELETE FROM App\Entity\Commande c WHERE c.idcmd LIKE :idcmd')
            ->setParameter('idcmd' , '%'.$commande->getIdcmd().'%');

        $query->execute();

    }

    public function updateEtat(Commande $commande , string $etat) : string {

        $em = $this->getEntityManager();

        $query = $em->createQuery("UPDATE App\Entity\Commande c SET c.etatcmd = :etat  WHERE c.idcmd = :idcmd")
            ->setParameter('idcmd' , $commande->getIdcmd())
            ->setParameter('etat' , $etat);

        $sql = $query->getDQL();

        $query->execute();

        return $sql;
    }


    public function getCommandesByUserPartitioned(PanierController $panierController,\Symfony\Component\HttpFoundation\Request $request) {

        $idpanier = $panierController->getSessionId($request);
        $commandesAll = $this->findAll();
        $commandesUser =[];


        if (!$commandesAll){
            return [];
        }
        //define $last///////////////////////////////////////////////////////////////////////
        $count = 1;
        foreach ($commandesAll as $commande) {
            if($commande->getIdpanier() == $idpanier) {
                $last = $commande->getIdcmd();
                break;
            }
            else{
                $count++;
            }
        }
        if($count == count($commandesAll)){
            return [];
        }
        //////////////////////////////////////////////////////////////////////////////////////
        $temp = [];
        $commandesPartitioned = [];
        foreach ($commandesAll as $commande){
            if($commande->getIdpanier() == $idpanier){
                $commandesUser[] = $commande;

                if($commande->getIdcmd() == $last){
                    $temp[] = $commande;

                }
                else{
                    $last = $commande->getIdcmd();
                    if($temp) {
                        $commandesPartitioned[] = $temp;
                    }
                    $temp = [];
                    $temp[] = $commande;
                }
            }
            else{
                if($temp){
                    $commandesPartitioned[] = $temp;
                    $temp = [];
                }
            }
        }
        if($temp) {
            $commandesPartitioned[] = $temp;
        }
        ////////////////////////////////////////////////////////////////////////////////////////
        return $commandesPartitioned ;
    }


    public function getNonCanceledCommande($commandesUserPartitioned){
        foreach ($commandesUserPartitioned as $commande){
            $canceled = false;
            foreach ($commande as $prod){
                if ($prod->getEtatcmd() != 'en attente de confirmation'){
                    $canceled = true;
                    break;
                }
            }
            if ($canceled){
                $index = array_search($commande,$commandesUserPartitioned);
                unset($commandesUserPartitioned[$index]);
            }
        }
        return $commandesUserPartitioned;
    }


    public function getItemCount($idcmd){
        $pc = new PanierController();
        $commandesUser = $this->getCommandesByUserPartitioned($pc);
        $nonCanceled = $this->getNonCanceledCommande($commandesUser);
        foreach ($nonCanceled as $commande){
            foreach ($commande as $prod){
                if($prod->getIdcmd() == $idcmd){
                    return count($commande);
                }
            }
        }
    }

    public function getTotalCommande($idcmd , PanierController $pc , \Symfony\Component\HttpFoundation\Request $request){
        $total = 0;
        $commandesUser = $this->getNonCanceledCommande($this->getCommandesByUserPartitioned($pc,$request));
        foreach ($commandesUser as $commande){
            foreach ($commande as $prod){
                if ($prod->getIdcmd() ==$idcmd){
                    $total = $total + $prod->getPrixremise() * $prod->getQuantite();
                }
            }
        }
        return $total;

    }

    public function partitionAll(){
        $commandesAll = $this->findAll();
        $commandesPartitioned = [] ;

        //define $last///////////////////////////////////////////////////////////////////////
        $last = 1;
        //////////////////////////////////////////////////////////////////////////////////////
        $temp = [];
        foreach ($commandesAll as $commande){

            if($commande->getIdcmd() == $last) {
                $temp[] = $commande;
            }

            else{
                $last = $commande->getIdcmd();
                if($temp) {
                    $commandesPartitioned[] = $temp;
                }
                $temp = [];
                $temp[] = $commande;
            }
        }
        if ($temp){
            $commandesPartitioned[] = $temp;
        }
        return $commandesPartitioned;
    }

    public function test(){
        $query = $this->createQueryBuilder('c')->where('c.idCmd = 1 ');
        $dql = $query->getDQL();
        return $dql;
    }

}
