<?php

namespace AbterPhp\Framework\Bootstrappers\Http;

use PHPUnit\Framework\TestCase;

class RouterBootstrapperTest extends TestCase
{
    /** @var RouterBootstrapper */
    protected RouterBootstrapper $sut;

    public function setUp(): void
    {
        $this->sut = new RouterBootstrapper();
    }

    public function testRegisterBindings()
    {
        $this->markTestIncomplete();
    }
}
