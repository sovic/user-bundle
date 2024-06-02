<?php

namespace UserBundle\ORM;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class EntityModelFactory
{
    public function __construct(
        protected readonly EntityManagerInterface $entityManager,
        protected readonly RouterInterface        $router,
        protected readonly TranslatorInterface    $translator
    ) {
    }

    protected function loadEntityModel(mixed $entity, string $modelClass): mixed
    {
        if (null === $entity) {
            return null;
        }

        /** @var AbstractEntityModel|mixed $model */
        $model = new $modelClass();
        $model->setEntityManager($this->entityManager);
        $model->setTranslator($this->translator);
        $model->setRouter($this->router);

        $model->entity = $entity;

        return $model;
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    protected function loadModelById(string $entityClass, string $modelClass, int $id): mixed
    {
        return $this->loadModelBy($entityClass, $modelClass, ['id' => $id]);
    }

    protected function loadModelBy(
        string $entityClass,
        string $modelClass,
        array  $criteria,
        ?array $orderBy = null
    ): mixed {
        $repository = $this->entityManager->getRepository($entityClass);
        $entity = $repository->findOneBy($criteria, $orderBy);
        if (!$entity) {
            return null;
        }

        return $this->loadEntityModel($entity, $modelClass);
    }
}
