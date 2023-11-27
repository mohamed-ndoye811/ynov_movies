<?php

namespace App\Repository;

use App\Entity\Film;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Film>
 *
 * @method Film|null find($id, $lockMode = null, $lockVersion = null)
 * @method Film|null findOneBy(array $criteria, array $orderBy = null)
 * @method Film[]    findAll()
 * @method Film[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FilmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Film::class);
    }

   /**
    * @return Film[] Returns an array of Film objects
    */
   public function findByExampleField($value): array
   {
       return $this->createQueryBuilder('f')
           ->andWhere('f.exampleField = :val')
           ->setParameter('val', $value)
           ->orderBy('f.id', 'ASC')
           ->setMaxResults(10)
           ->getQuery()
           ->getResult()
       ;
   }

    /**
     * @return Film|null Returns a Film object
     */
   public function findOneBySomeField($value): ?Film
   {
       return $this->createQueryBuilder('f')
           ->andWhere('f.exampleField = :val')
           ->setParameter('val', $value)
           ->getQuery()
           ->getOneOrNullResult()
       ;
   }

    /**
     * Récupère tous les films.
     *
     * @return Film[] Renvoie un tableau de films.
     */
    public function findAllFilms(int $page = 1, int $pageSize = 10): array
    {
        return $this->createQueryBuilder('f')
            ->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize)
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche des films par titre ou description.
     *
     * @param string $searchTerm Le terme de recherche.
     * @return Film[] Renvoie un tableau de films correspondants.
     */
    public function findByTitleOrDescription(string $searchTerm): array
    {
        $queryBuilder = $this->createQueryBuilder('f');
        $queryBuilder->where('f.nom LIKE :searchTerm')
                    ->orWhere('f.description LIKE :searchTerm')
                    ->setParameter('searchTerm', '%' . $searchTerm . '%');

        return $queryBuilder->getQuery()->getResult();
    }

}
