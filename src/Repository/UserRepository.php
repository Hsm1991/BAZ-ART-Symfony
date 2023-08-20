<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct( ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function edit(User $user)
    {

//
//        return $this->createQueryBuilder('u')
//            ->set('u.firstname', ':firstname')
//            ->set('u.lastname', ':lastname')
//            ->set('u.address', ':address')
//            ->set('u.city', ':city')
//            ->set('u.tel', ':tel')
////            ->set('u.email', ':email')
////            ->set('u.password', ':password')
//            ->andWhere('u.id = :editId')
//            ->setParameter('firstname',$user->getFirstname())
//            ->setParameter('lastname',$user->getLastname())
//            ->setParameter('address',$user->getAddress())
//            ->setParameter('city',$user->getCity())
//            ->setParameter('tel',$user->getTel())
////            ->setParameter('email', $user->getEmail())
////            ->setParameter('password',$user->getPassword())
//            ->setParameter('editId', $user->getId())
//            ->getQuery()
//            ->execute();


    }



    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);


        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findOneByEmail($email): ?User
    {
        return $this->findOneBy(array('email' => $email));
    }

    public function findOneByResetToken(string $token): ?User
    {
        return $this->findOneBy(array('resetToken' => $token));
    }


        public function findByCity($value): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.city = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->execute()
        ;
    }

    public function countByMonth(){
       $query = $this->createQueryBuilder('u')
           ->select('SUBSTRING(u.created_at,6,2) as month,COUNT(u) as count')
           ->groupBy('month')
           ->orderBy('month','ASC');
       return $query->getQuery()->getResult();

    }

    public function countByYear(){
        $query = $this->createQueryBuilder('u')
            ->select('SUBSTRING(u.created_at,1,4) as year,COUNT(u) as count')
            ->groupBy('year')
            ->orderBy('year','ASC');
        return $query->getQuery()->getResult();
    }




    public function findByYear($value): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('SUBSTRING(u.created_at,1,4) = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
        ;
    }


    public function countByMonthAndYear($value){
        $query = $this->createQueryBuilder('u')
            ->andWhere('SUBSTRING(u.created_at,1,4) = :val')
            ->setParameter('val', $value)
            ->select('SUBSTRING(u.created_at,6,2) as month,COUNT(u) as count')
            ->groupBy('month')
            ->orderBy('month','ASC');
        return $query->getQuery()->getResult();

    }


}
