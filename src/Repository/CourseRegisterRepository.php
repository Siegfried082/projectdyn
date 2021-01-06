<?php

namespace App\Repository;

use App\Entity\CourseRegister;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CourseRegister|null find($id, $lockMode = null, $lockVersion = null)
 * @method CourseRegister|null findOneBy(array $criteria, array $orderBy = null)
 * @method CourseRegister[]    findAll()
 * @method CourseRegister[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CourseRegisterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CourseRegister::class);
    }


    public function userRegisterCourse(int $userId, int $courseId) : bool
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.user = :author')
            ->andWhere('c.course = :course')
            ->setParameter('author', $userId)
            ->setParameter('course', $courseId)
            ->getQuery()
            ->getResult()
        ;



        return count($query) == 0;
    }

    // /**
    //  * @return CourseRegister[] Returns an array of CourseRegister objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CourseRegister
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
