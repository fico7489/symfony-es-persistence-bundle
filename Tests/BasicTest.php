<?php

namespace SymfonyEs\Bundle\PersistenceBundle\Tests;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\Tools\SchemaTool;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;
use SymfonyEs\Bundle\PersistenceBundle\Service\PersistenceBundleTest;
use SymfonyEs\Bundle\PersistenceBundle\Tests\Util\Entity\User;

class BasicTest extends KernelTestCase
{
    private Container $container;
    private static array $events;

    public function testSomething(): void
    {
        // (1) boot the Symfony kernel
        self::bootKernel();

        // (2) use static::getContainer() to access the service container
        $this->container = static::getContainer();

        /** @var Registry $doctrine */
        $doctrine = $this->container->get('doctrine');

        $entityManager = $doctrine->getManager();
        $metaData = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->updateSchema($metaData);

        $user = new User();
        $user->setName('test');

        $entityManager->persist($user);
        $entityManager->flush();

        $this->assertEquals(1, 1);

        $doctrine = $this->container->get(PersistenceBundleTest::class);

        dd($entityManager->getRepository(User::class)->findAll());
    }

    public function eventsStartListen(string $eventClass): void
    {
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
