<?php

namespace Fico7489\PersistenceBundle\Tests\Functional;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\Tools\SchemaTool;
use Fico7489\PersistenceBundle\Event\UpdatedEntity;
use Fico7489\PersistenceBundle\Tests\Util\Entity\User;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;

class FunctionalTest extends KernelTestCase
{
    private Container $container;
    private static array $events;

    public function testSomething(): void
    {
        // 1 and 2 are from symfony docs: https://symfony.com/doc/current/testing.html

        // (1) boot the Symfony kernel
        self::bootKernel();

        // (2) use static::getContainer() to access the service container
        $this->container = static::getContainer();

        // loading doctrine registry from service container
        /** @var Registry $doctrine */
        $doctrine = $this->container->get('doctrine');

        // updating a schema in sqlite database
        $entityManager = $doctrine->getManager();
        $metaData = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->updateSchema($metaData);

        // start listening to our custom event
        $this->eventsStartListen(UpdatedEntity::class);

        // create, persist and flush entity
        $user = new User();
        $user->setName('test');
        $entityManager->persist($user);
        $entityManager->flush();

        // get events which are received
        $events = $this->eventsGet(UpdatedEntity::class);

        // assert that we received what we want
        $this->assertEquals(1, count($events));
        $this->assertEquals(1, $events[0]->getId());
        $this->assertEquals(User::class, $events[0]->getClassName());

        // clear database for the next run
        $filesystem = new Filesystem();
        $filesystem->remove('var/database.db3');
    }

    public function eventsStartListen(string $eventClass): void
    {
        // custom implementation for events listening

        /** @var EventDispatcher $eventDispatcher */
        $eventDispatcher = $this->container->get(EventDispatcherInterface::class);
        $eventDispatcher->addListener($eventClass, function ($event) use ($eventClass): void {
            self::$events[$eventClass][] = $event;
        });
    }

    public function eventsGet(string $eventClass): array
    {
        return self::$events[$eventClass] ?? [];
    }
}
