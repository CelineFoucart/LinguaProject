<?php

namespace App\Repository;

use App\Entity\Article;
use App\Service\PaginatorService;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    private PaginatorService $paginatorService;

    public function __construct(ManagerRegistry $registry, PaginatorService $paginatorService)
    {
        parent::__construct($registry, Article::class);
        $this->paginatorService = $paginatorService;
    }

    public function save(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Article[] Returns an array of Article objects
     */
    public function searchPaginatedItems(array $parameters): array
    {
        $builder = $this->createQueryBuilder('a')->leftJoin('a.category', 'c')->addSelect('c');

        if (isset($parameters['search']['value']) && strlen($parameters['search']['value']) > 1) {
            $builder->andWhere('a.title = :title')->setParameter('title', $parameters['search']['value']);
        }

        $limit = isset($parameters['length']) ? (int)$parameters['length'] : 10;
        $builder->setMaxResults($limit);

        $orderColumns = isset($parameters['order'][0]['column']) ? (int)$parameters['order'][0]['column'] : 0;
        $direction = isset($parameters['order'][0]['dir']) ? $parameters['order'][0]['dir'] : 'asc';
        
        $orderBy = isset($parameters['columns'][$orderColumns]['name']) ? $parameters['columns'][$orderColumns]['name'] : 'a.id';
            
        $builder->orderBy($orderBy, $direction);

        return $builder->getQuery()->getResult();
    }

    public function countSearchTotal(array $parameters): array
    {
        $builder = $this->createQueryBuilder('a')
            ->select('COUNT(a.id) AS recordsFiltered');

        if (isset($parameters['search']['value']) && strlen($parameters['search']['value']) > 1) {
            $builder->andWhere('a.title = :title')->setParameter('title', $parameters['search']['value']);
        }

        return $builder->getQuery()->getOneOrNullResult();
    }

    public function findPaginated(int $page = 1, int $limit = 15, ?int $categoryId = null): PaginationInterface
    {
        $query = $this->createQueryBuilder('a')->leftJoin('a.category', 'c')->addSelect('c')->orderBy('a.title');

        if ($categoryId) {
            $query->andWhere('c.id = :id')->setParameter('id', $categoryId);
        }

        return $this->paginatorService->setLimit($limit)->paginate($query, $page);
    }
}
