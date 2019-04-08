<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Pagination;

class NumbersTest extends \PHPUnit\Framework\TestCase
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
    public function testPopulate(int $currentPage, array $pageNumbers, int $lastPage, array $expected)
    {
        $sut = new Numbers('/foo');

        $sut->populate($currentPage, $pageNumbers, $lastPage);

        $this->assertCount(count($expected), $sut);
        foreach ($expected as $idx => $content) {
            $this->assertContains($content, (string)$sut[$idx]);
        }
    }
}
