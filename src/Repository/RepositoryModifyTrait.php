<?php

namespace App\Repository;

trait RepositoryModifyTrait
{
    public function save(object $object): void
    {
        assert($this->getClassName() === $object::class);
        $this->getEntityManager()->persist($object);
    }

    public function remove(object $object): void
    {
        assert($this->getClassName() === $object::class);
        $this->getEntityManager()->remove($object);
    }

    public function commit(): void
    {
        $this->getEntityManager()->flush();
    }

    public function saveAndCommit(object $object): void
    {
        $this->save($object);
        $this->commit();
    }

    public function removeAndCommit(object $object): void
    {
        $this->remove($object);
        $this->commit();
    }
}
