<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Session;

use Opulence\Sessions\ISession;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MockSessionFactory
{
    /**
     * @param TestCase $testCase
     * @param array|null $sessionData
     * @param string $name
     * @param string|int $id
     *
     * @return ISession|MockObject|null
     */
    public static function create(
        TestCase $testCase,
        array $sessionData = null,
        string $name = 'foo',
        $sessionId = 'bar'
    ): ?ISession {
        if (!$sessionData) {
            return null;
        }

        /** @var ISession|MockObject $sessionMock */
        $sessionMock = $testCase->getMockBuilder(ISession::class)
            ->setMethods(
                [
                    'ageFlashData',
                    'delete',
                    'flash',
                    'flush',
                    'get',
                    'getAll',
                    'getId',
                    'getName',
                    'has',
                    'hasStarted',
                    'reflash',
                    'regenerateId',
                    'set',
                    'setId',
                    'setMany',
                    'setName',
                    'start',
                    'offsetExists',
                    'offsetGet',
                    'offsetSet',
                    'offsetUnset',
                ]
            )
            ->getMock();

        $sessionMock
            ->expects($testCase->any())
            ->method('get')
            ->willReturnCallback(
                function ($key, $defaultValue = null) use ($sessionData) {
                    if (array_key_exists($key, $sessionData)) {
                        return $sessionData[$key];
                    }

                    return $defaultValue;
                }
            );

        $sessionMock
            ->expects($testCase->any())
            ->method('has')
            ->willReturnCallback(
                function ($key) use ($sessionData) {
                    if (array_key_exists($key, $sessionData)) {
                        return true;
                    }

                    return false;
                }
            );

        $sessionMock
            ->expects($testCase->any())
            ->method('hasStarted')
            ->willReturn(true);

        $sessionMock
            ->expects($testCase->any())
            ->method('getAll')
            ->willReturn($sessionData);

        $sessionMock
            ->expects($testCase->any())
            ->method('getId')
            ->willReturn($sessionId);

        $sessionMock
            ->expects($testCase->any())
            ->method('getName')
            ->willReturn($name);

        $sessionMock
            ->expects($testCase->any())
            ->method('start')
            ->willReturn(true);

        return $sessionMock;
    }
}
