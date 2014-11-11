<?php

namespace Rudak\BlogBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * PostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PostRepository extends EntityRepository
{
    public function getPostById($id)
    {
        $qb = $this->createQueryBuilder('p')
            ->addSelect('pic')
            ->where('p.id = :id')->setParameter('id', $id)
            ->andWhere('p.public = true')
            ->leftJoin('p.picture', 'pic')
            ->getQuery();
        return $qb->getOneOrNullResult();
    }

    public function getPostsByPage($page, $nb_par_page)
    {
        $first_result = ($page - 1) * $nb_par_page;
        $qb           = $this->createQueryBuilder('p')
            ->addSelect('pic')
            ->where('p.public = true')
            ->leftJoin('p.picture', 'pic')
            ->setMaxResults($nb_par_page)
            ->setFirstResult($first_result)
            ->getQuery();
        return $qb->execute();
    }

    public function getLastPosts($nb)
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.public = true')
            ->addOrderBy('p.id', 'DESC')
            ->setMaxResults($nb)
            ->getQuery();
        return $qb->execute();
    }

    public function getPopularPosts($nb)
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.public = true')
            ->addOrderBy('p.hit', 'DESC')
            ->setMaxResults($nb)
            ->getQuery();
        return $qb->execute();
    }

    public function getNbTotalPosts()
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.public = true')
            ->select('count(p) nb')
            ->getQuery();
        return $qb->getSingleScalarResult();
    }

    /**
     * Va chercher le post precedent
     * @param $id
     * @return mixed
     */
    public function getPrevPost($id)
    {
        $qb = $this->createQueryBuilder('p')
            ->addSelect('pic')
            ->where('p.public = true')
            ->andWhere('p.id < :id')->setParameter('id', $id)
            ->leftJoin('p.picture','pic')
            ->orderBy('p.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        if ($result = $qb->getOneOrNullResult()) {
            return $result;
        } else {
            $qb = $this->createQueryBuilder('p')
                ->addSelect('pic')
                ->where('p.public = true')
                ->orderBy('p.id', 'DESC')
                ->leftJoin('p.picture','pic')
                ->setMaxResults(1)
                ->getQuery();
            return $qb->getOneOrNullResult();
        }
    }

    public function getNextPost($id)
    {
        $qb = $this->createQueryBuilder('p')
            ->addSelect('pic')
            ->where('p.public = true')
            ->andWhere('p.id > :id')->setParameter('id', $id)
            ->leftJoin('p.picture','pic')
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery();
        if ($result = $qb->getOneOrNullResult()) {
            return $result;
        } else {
            $qb = $this->createQueryBuilder('p')
                ->addSelect('pic')
                ->where('p.public = true')
                ->leftJoin('p.picture','pic')
                ->orderBy('p.id', 'ASC')
                ->setMaxResults(1)
                ->getQuery();
            return $qb->getOneOrNullResult();
        }
    }
}
