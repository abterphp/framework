<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Pagination;

use PHPUnit\Framework\TestCase;

class NumbersTest extends TestCase
{
    /**
     * @return array
     */
    public function populateProvider(): array
    {
        return [
            '1-of-1'            => [1, [1], 1, ['1']],
            '1-of-2'            => [1, [1, 2], 2, ['1', '2', '>']],
            '2-of-2'            => [2, [1, 2], 2, ['<', '1', '2']],
            'first-not-visible' => [2, [2, 3], 3, ['<<', '<', '...', '2', '3', '>']],
            'both-not-visible'  => [2, [2, 3], 10, ['<<', '<', '...', '2', '3', '...', '>', '>>']],
        ];
    }

    /**
     * @dataProvider populateProvider
     *
     * @param int      $currentPage
     * @param array    $pageNumbers
     * @param int      $lastPage
     * @param string[] $expected
     */
    public function testPopulate(int $currentPage, array $pageNumbers, int $lastPage, array $expected): void
    {
        $sut = new Numbers('/foo?');

        $sut->populate($currentPage, $pageNumbers, $lastPage);

        $this->assertCount(count($expected), $sut);
        foreach ($expected as $idx => $content) {
            $this->assertStringContainsString($content, (string)$sut[$idx]);
        }
    }

    public function testPopulateWithChangedBaseUrl(): void
    {
        $originalUrl = '/foo?';
        $finalUrl    = '/bar?';

        $currentPage = 2;
        $pageNumbers = [2, 3];
        $lastPage    = 10;
        // $expected    = ['<<', '<', '...', '2', '3', '...', '>', '>>'];

        $sut = new Numbers($originalUrl);

        $sut->setBaseUrl($finalUrl);

        $sut->populate($currentPage, $pageNumbers, $lastPage);

        $this->assertStringContainsString($finalUrl, (string)$sut[0]);
        $this->assertStringContainsString($finalUrl, (string)$sut[1]);
        $this->assertStringContainsString($finalUrl, (string)$sut[4]);
        $this->assertStringContainsString($finalUrl, (string)$sut[6]);
        $this->assertStringContainsString($finalUrl, (string)$sut[7]);
    }
}
