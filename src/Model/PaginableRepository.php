<?php

namespace Adamski\Symfony\HelpersBundle\Model;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

abstract class PaginableRepository extends ServiceEntityRepository {

    /**
     * Get paginated collection limited by specified items limit.
     *
     * @param int $page
     * @param int $limit
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public abstract function getPaginated(int $page = 1, int $limit = 20);

    /**
     * Get Query Builder to apply DataTables filters.
     *
     * @return QueryBuilder
     */
    public function getFilteringQueryBuilder() {
        return $this->getEntityManager()->getRepository(self::getClassName())
            ->createQueryBuilder("_");
    }

    /**
     * Modify specified Query to provide pagination.
     *
     * @param Query|QueryBuilder $query
     * @param int                $page
     * @param int                $limit
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    protected function paginate($query, int $page = 1, int $limit = 20) {

        // Define instance of Doctrine Paginator
        $paginator = new Paginator($query);

        // Modify specified Query
        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);

        return $paginator;
    }
}
