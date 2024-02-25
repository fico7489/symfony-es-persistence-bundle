<?php

namespace Fico7489\PersistenceBundle\Tests\Unit;

use Fico7489\PersistenceBundle\Service\PersistenceBundleService;
use PHPUnit\Framework\TestCase;

class UnitTest extends TestCase
{
    public function testSomething()
    {
        $this->assertEquals('PersistenceBundleTest', (new PersistenceBundleService())->test());
    }
}
