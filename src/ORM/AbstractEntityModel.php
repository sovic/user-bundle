<?php

namespace UserBundle\ORM;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractEntityModel
{
    protected EntityManagerInterface $entityManager;
    protected RouterInterface $router;
    protected TranslatorInterface $translator;

    public mixed $entity;

    #[Required]
    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    #[Required]
    public function setRouter(RouterInterface $router): void
    {
        $this->router = $router;
    }

    #[Required]
    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    protected function flush(): void
    {
        $this->entityManager->persist($this->entity);
        $this->entityManager->flush();
    }

    public function remove(): void
    {
        $this->entityManager->remove($this->entity);
        $this->entityManager->flush();
    }

    /**
     * @throws ORMException
     */
    public function refresh(): void
    {
        $this->entityManager->refresh($this->entity);
    }
}
