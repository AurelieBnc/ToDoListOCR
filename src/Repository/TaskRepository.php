<?php

namespace App\Repository;

use App\Entity\Task;
use App\EnumTodo\TaskStatus;
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

    public function findByPagination(int $page, TaskStatus $status): array
    {
        $limit = 12;
        $result = [];
        $statusToString = null;

        if ($status === TaskStatus::IsDone) {
            $statusToString = 'isDone';
        }
        if ($status === TaskStatus::Todo) {
            $statusToString = 'todo';
        }

        $query = $this->createQueryBuilder('t')
        ->orderBy('t.id', 'ASC')
        ->where('t.status = :status')
        ->setParameter('status', $statusToString)
        ->setMaxResults($limit)
        ->setFirstResult($page * $limit - $limit);

        $paginator = new Paginator($query);
        $data = $paginator->getQuery()->getResult();

        if (empty($data)) {
            $result['data'] = [];
            $result['pages'] = 1;
            return $result;
        }
        $pages = ceil($paginator->count() / $limit);
        $result['data'] = $data;
        $result['pages'] = $pages;

        return $result;
    }

    public function add(Task $task): void
    {
        $this->getEntityManager()->persist($task);
        $this->getEntityManager()->flush();
    }

    public function update(Task $task): void
    {
        $this->add($task);
    }

    public function remove(Task $task): void
    {
        $this->getEntityManager()->remove($task);
        $this->getEntityManager()->flush();
    }
}
