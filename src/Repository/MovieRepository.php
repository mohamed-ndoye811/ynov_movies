<?php

namespace App\Repository;

use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Movie>
 *
 * @method Movie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Movie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Movie[]    findAll()
 * @method Movie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Movie::class);
    }

   /**
    * @return Movie[] Returns an array of Movie objects
    */
   public function findByExampleField($value): array
   {
       return $this->createQueryBuilder('f')
           ->andWhere('f.exampleField = :val')
           ->setParameter('val', $value)
           ->orderBy('f.uuid', 'ASC')
           ->setMaxResults(10)
           ->getQuery()
           ->getResult()
       ;
   }

    /**
     * @return Movie|null Returns a Movie object
     */
   public function findOneBySomeField($value): ?Movie
   {
       return $this->createQueryBuilder('f')
           ->andWhere('f.exampleField = :val')
           ->setParameter('val', $value)
           ->getQuery()
           ->getOneOrNullResult()
       ;
   }

    /**
     * Récupère tous les movies.
     *
     * @return Movie[] Renvoie un tableau de movies.
     */
    public function findAllMovies(int $page = 1, int $pageSize = 10): array
    {
        return $this->createQueryBuilder('f')
            ->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize)
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche des movies par titre ou description.
     *
     * @param string $searchTerm Le terme de recherche.
     * @return Movie[] Renvoie un tableau de movies correspondants.
     */
    public function findByTitleOrDescription(string $searchTerm): array
    {
        $queryBuilder = $this->createQueryBuilder('f');
        $queryBuilder->where('f.name LIKE :searchTerm')
                    ->orWhere('f.description LIKE :searchTerm')
                    ->setParameter('searchTerm', '%' . $searchTerm . '%');

        return $queryBuilder->getQuery()->getResult();
    }
}
