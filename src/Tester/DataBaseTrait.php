<?php

namespace Symfobooster\Devkit\Tester;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @mixin ClientTrait
 * @mixin WebTestCase
 */
trait DataBaseTrait
{
    protected function getEntityManager(): EntityManagerInterface
    {
        if (!$this instanceof KernelTestCase) {
            throw new \BadMethodCallException('Override the method to provide Entity Manager instance');
        }

        if (!static::$kernel) {
            throw new \BadFunctionCallException('Boot the kernel first');
        }

        return static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    protected function loadFixtures(array $fixtures, bool $append = false): void
    {
        $loader = new Loader();
        foreach ($fixtures as $fixtureClassName) {
            if (!class_exists($fixtureClassName)) {
                throw new \RuntimeException("Fixture class $fixtureClassName is not exist");
            }

            $loader->addFixture(new $fixtureClassName());
        }

        $executor = new ORMExecutor($this->getEntityManager(), new ORMPurger());
        $executor->execute($loader->getFixtures(), $append);
    }

    protected function purgeDatabase(): void
    {
        $executor = new ORMExecutor($this->getEntityManager(), new ORMPurger());
        $executor->purge();
    }

    protected function seeRecord(string $entity, array $data, ?array $postCheck = null): void
    {
        $repository = $this->getEntityManager()->getRepository($entity);
        $record = $repository->findOneBy($data);
        $this->assertNotNull($record);
        if (!is_null($postCheck)) {
            foreach ($postCheck as $key => $item) {
                $this->assertEquals($item, $record->{'get' . ucfirst($key)}());
            }
        }
    }

    protected function dontSeeRecord(string $entity, array $data): void
    {
        $repository = $this->getEntityManager()->getRepository($entity);
        $record = $repository->findOneBy($data);
        $this->assertNull($record);
    }
}
