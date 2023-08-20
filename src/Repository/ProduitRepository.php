<?php

namespace App\Repository;

use App\Controller\ShopController;
use App\Entity\Produit;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 *
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry , private LikeRepository $likeRepository)
    {
        parent::__construct($registry, Produit::class);
    }

    public function save(Produit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Produit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Produit[] Returns an array of Produit objects
     */
    public function findProd($value): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.idprod = :val')
            ->setParameter('val', $value)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
            ;
    }

    public function updateRating(Produit $produit ){
        $likesArray = $this->likeRepository->findBy(['produit'=> $produit->getIdprod(),'nom'=>'Likee']);
        $dislikesArray = $this->likeRepository->findBy(['produit'=> $produit->getIdprod(),'nom'=>'Dislikee']);
        $rating = count($likesArray) - count($dislikesArray) ;

        $em = $this->getEntityManager();

        $query = $em->createQuery("UPDATE App\Entity\Produit p SET p.rating = :rating  WHERE p.idprod = :idprod")
            ->setParameter('idprod' , $produit->getIdprod())
            ->setParameter('rating' , $rating)
            ;

        $query->execute();
    }


    public function changeDislikeToLike(User $user, Produit $produit , LikeRepository $likeRepository){

        $dislike = $likeRepository->findDislike($user,$produit);

        $em =  $this->getEntityManager();
        $query = $em->createQuery("UPDATE App\Entity\Likeee l SET l.nom = 'Likee'  WHERE l.id = :id ")
            ->setParameter('id' , $dislike->getId())
        ;
        $query->execute();
    }

    public function changeLikeToDislike(User $user, Produit $produit , LikeRepository $likeRepository){
        $like = $likeRepository->findLike($user,$produit);

        $em =  $this->getEntityManager();
        $query = $em->createQuery("UPDATE App\Entity\Likeee l SET l.nom = 'Dislikee'  WHERE l.id = :id ")
            ->setParameter('id' , $like->getId())
        ;
        $query->execute();
    }




//    public function findOneBySomeField($value): ?Produit
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
