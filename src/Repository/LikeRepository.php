<?php

namespace App\Repository;

use App\Entity\Likeee;
use App\Entity\Produit;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Likeee>
 *
 * @method Likeee|null find($id, $lockMode = null, $lockVersion = null)
 * @method Likeee|null findOneBy(array $criteria, array $orderBy = null)
 * @method Likeee[]    findAll()
 * @method Likeee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Likeee::class);
    }

    public function add(Likeee $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Likeee $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Likeee[] Returns an array of Like objects
     */
    public function findProd($value): array
    {
       return $this->createQueryBuilder('i')
            ->andWhere('i.produit = :val')
            ->setParameter('val', $value)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findDislike(User $user,Produit $produit){
//        $query = $this->getEntityManager()->createQuery("SELECT l FROM App\Entity\Likeee l WHERE  l.nom = 'Dislikee' l.iduser = :iduser AND l.produit = :produit ")
//            ->setParameter('iduser',$user->getId())
//            ->setParameter('produit',$produit->getIdprod())
//           // ->setParameter('nom','Dislike')
//            ;
//
//        $dislike = $query->getOneOrNullResult();

        $dislike = $this->findOneBy(['nom'=>'Dislikee','produit'=>$produit->getIdprod(),'iduser'=>$user->getId()]);



        return $dislike;
    }

    public function findLike(User $user,Produit $produit){
//        $query = $this->getEntityManager()->createQuery("SELECT l FROM App\Entity\Likeee l WHERE  l.nom = 'Likee' l.iduser = :iduser AND l.produit = :produit ")
//                                          ->setParameter('iduser',$user->getId())
//                                          ->setParameter('produit',$produit->getIdprod())
//                                          //  ->setParameter('nom','Like')
//                                                 ;
//        $like = $query->getOneOrNullResult();

        $like = $this->findOneBy(['nom'=>'Likee','produit'=>$produit->getIdprod(),'iduser'=>$user->getId()]);

        return $like;
    }

//    public function findOneBySomeField($value): ?Like
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
