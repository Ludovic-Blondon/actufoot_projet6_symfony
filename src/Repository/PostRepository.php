<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\PostSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * @return array Post[]
     */
    public function findLatest(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.post_at', 'DESC')
            ->setMaxResults(12)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param PostSearch $search
     * @return Query
     */
    public function findAllDescQuery(PostSearch $search): Query
    {
        $query = $this->createQueryBuilder('p')
            ->orderBy('p.post_at', 'DESC');

        if ($search->getKeyWord()) {
            $query = $query
                ->where('p.title LIKE :keyword')
                ->orWhere('p.content LIKE :keyword')
                ->setParameter('keyword', '%' . $search->getKeyWord() . '%')
            ;
        }

        return $query->getQuery();
    }

    // /**
    //  * @return Post[] Returns an array of Post objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
