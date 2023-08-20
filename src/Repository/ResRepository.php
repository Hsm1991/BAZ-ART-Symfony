<?php

namespace App\Repository;

use App\Entity\Res;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Twilio\Rest\Client;

/**
 * @extends ServiceEntityRepository<Res>
 *
 * @method Res|null find($id, $lockMode = null, $lockVersion = null)
 * @method Res|null findOneBy(array $criteria, array $orderBy = null)
 * @method Res[]    findAll()
 * @method Res[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Res::class);
    }

    public function save(Res $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Res $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function sendsms(): void
    {
        //require ('vendor\autoload.php');
        $sid = "AC7d179ab890c0a5116dd784765e67c970" ; //getenv("AC7d179ab890c0a5116dd784765e67c970");
        $token = "a0c188b7bb1bb859cf4c7be2ec7f142f" ; //getenv("a0c188b7bb1bb859cf4c7be2ec7f142f");
        $client = new Client( $sid, $token);

        $message = $client->messages
            ->create("+21692468486", // to
                ["body" => "Votre Reservation est bien confirmée ", "from" => "+19789244142"]
            );

    }

//    /**
//     * @return Res[] Returns an array of Res objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Res
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
