<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Factory\Table;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Grid\Component\Body;

class BodyFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $getters      = ['foo' => '__toString'];
        $rowArguments = [Html5::ATTR_CLASS => 'foo'];

        $sut = new BodyFactory();

        $body = $sut->create($getters, $rowArguments, null);

        $this->assertInstanceOf(Body::class, $body);
    }
}
