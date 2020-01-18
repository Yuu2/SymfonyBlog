<?php

namespace App\Repository;

use App\Entity\Article;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Yuu2
 * updated 2020.01.19
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository {

  /**
   * @access public
   * @param RegistryInterface $registry
   * @param PaginatorInterface $paginatorInterface
   */
  public function __construct(RegistryInterface $registry, PaginatorInterface $paginator) {
    parent::__construct($registry, Article::class);
    $this->paginator = $paginator;
  }
  
  /**
   * 게시글 일람 쿼리
   * @access public
   * @param int $page
   * @return Object
   */
  public function paging(int $page): ?Object {

    $query = $this->createQueryBuilder('a')
      ->andWhere('a.visible = :visible')
      ->setParameter('visible', true)
      ->addOrderBy('a.id', 'DESC')
      ->getQuery()
      ->getResult();
    ;
    return $this->paginator->paginate($query, $page, 3);
  }

  /**
   * 게시글 카운트 쿼리
   * @access public
   * @param Integer $board
   */
  function total($board) {
    return $this->createQueryBuilder('at')
                ->select('count(at.id) as total')
                ->where('at.board = :board')
                ->setParameter('board', $board)
                ->getQuery()
                ->getOneOrNullResult();
  }

  /**
   * 게시글 상세 쿼리
   * @access public
   */
  function show($id) {
    return $this->createQueryBuilder('at')
                ->select('at.id, at.title, at.content, at.created_at', 'ac.username', 'b.subject')
                ->innerJoin('at.account', 'ac')
                ->innerJoin('at.board', 'b')
                ->where('at.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getOneOrNullResult();
  }
}
