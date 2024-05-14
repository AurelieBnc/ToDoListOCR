<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function findTasksListIsDonePaginated(int $page, int $limit = 12): array
    {
        $limit = abs($limit);
        $result = [];

        $query = $this->createQueryBuilder('t')
        ->orderBy('t.createdAt', 'ASC')
        ->where('t.isDone = true')
        ->setMaxResults($limit)
        ->setFirstResult($page * $limit - $limit);

        $paginator = new Paginator($query);
        $data = $paginator->getQuery()->getResult();

        if (empty($data)) {
            return $result;
        }
        $pages = ceil($paginator->count() / $limit);
        $result['data'] = $data;
        $result['pages'] = $pages;
        $result['page'] = $page;
        $result['limit'] = $limit;

        return $result;
    }

    public function findTasksListIsNotDonePaginated(int $page, int $limit = 12): array
    {
        $limit = abs($limit);
        $result = [];

        $query = $this->createQueryBuilder('t')
        ->orderBy('t.createdAt', 'ASC')
        ->where('t.isDone = false')
        ->setMaxResults($limit)
        ->setFirstResult($page * $limit - $limit);

        $paginator = new Paginator($query);
        $data = $paginator->getQuery()->getResult();

        if (empty($data)) {
            return $result;
        }
        $pages = ceil($paginator->count() / $limit);
        $result['data'] = $data;
        $result['pages'] = $pages;
        $result['page'] = $page;
        $result['limit'] = $limit;

        return $result;
    }

    public function add(Task $task, bool $flush = false): void
    {
        $this->getEntityManager()->persist($task);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function update(Task $task, bool $flush = false): void
    {
        $this->add($task, $flush);
    }

    public function remove(Task $task, bool $flush = false): void
    {
        $this->getEntityManager()->remove($task);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
