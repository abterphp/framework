<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Pagination;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Grid\Action\Action;
use AbterPhp\Framework\Grid\Component\Actions;
use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\Attributes;

class Numbers extends Actions
{
    /** @var string */
    protected string $baseUrl = '';

    /** @var Attributes */
    protected Attributes $fakeBtnAttr;

    /** @var string[] */
    protected array $fakeBtnIntents = [Action::INTENT_PRIMARY];

    protected Attributes $realBtnAttr;

    /** @var string[] */
    protected array $realBtnIntents = [Action::INTENT_PRIMARY];

    /**
     * Numbers constructor.
     *
     * @param string $baseUrl
     */
    public function __construct(string $baseUrl)
    {
        parent::__construct();

        $this->fakeBtnAttr = new Attributes([Html5::ATTR_DISABLED => null]);
        $this->realBtnAttr = new Attributes();

        $this->baseUrl = $baseUrl;
    }

    /**
     * @param string $baseUrl
     */
    public function setBaseUrl(string $baseUrl): void
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * Numbers constructor.
     *
     * @param int   $currentPage
     * @param array $pageNumbers
     * @param int   $lastPage
     */
    public function populate(int $currentPage, array $pageNumbers, int $lastPage): void
    {
        $lastNumber = $pageNumbers[count($pageNumbers) - 1];

        $isFirst        = ($currentPage === 1);
        $isLast         = ($currentPage == $lastNumber);
        $isFirstVisible = ($pageNumbers[0] === 1);
        $isLastVisible  = ($lastPage <= $lastNumber);

        $this->attachLeft($isFirst, $isFirstVisible, $currentPage);
        $this->attachNumbers($pageNumbers, $currentPage);
        $this->attachRight($isLast, $isLastVisible, $currentPage, $lastPage);
    }

    /**
     * @param bool $isFirst
     * @param bool $isFirstVisible
     * @param int  $currentPage
     */
    protected function attachLeft(bool $isFirst, bool $isFirstVisible, int $currentPage): void
    {
        if (!$isFirstVisible) {
            $this->realBtnAttr
                ->replaceItem(new Attribute(Html5::ATTR_HREF, sprintf('%spage=%d', $this->baseUrl, 1)));

            $this->nodes[] = new Action('<<', $this->realBtnIntents, $this->realBtnAttr, [], Html5::TAG_A);
        }

        if (!$isFirst) {
            $this->realBtnAttr
                ->replaceItem(new Attribute(Html5::ATTR_HREF, sprintf('%spage=%d', $this->baseUrl, $currentPage - 1)));

            $this->nodes[] = new Action('<', $this->realBtnIntents, $this->realBtnAttr, [], Html5::TAG_A);
        }

        if (!$isFirstVisible) {
            $this->nodes[] = new Action('...', $this->realBtnIntents, $this->fakeBtnAttr, [], Html5::TAG_BUTTON);
        }
    }

    /**
     * @param int[] $numbers
     * @param int   $currentPage
     */
    protected function attachNumbers(array $numbers, int $currentPage): void
    {
        foreach ($numbers as $number) {
            if ($currentPage == $number) {
                $this->nodes[] = new Action("$number", $this->fakeBtnIntents, $this->fakeBtnAttr);
            } else {
                $this->realBtnAttr
                    ->replaceItem(new Attribute(Html5::ATTR_HREF, sprintf('%spage=%d', $this->baseUrl, $number)));

                $this->nodes[] = new Action("$number", $this->realBtnIntents, $this->realBtnAttr, [], Html5::TAG_A);
            }
        }
    }

    /**
     * @param bool $isLast
     * @param bool $isLastVisible
     * @param int  $currentPage
     * @param int  $lastPage
     */
    protected function attachRight(bool $isLast, bool $isLastVisible, int $currentPage, int $lastPage): void
    {
        if (!$isLastVisible) {
            $this->nodes[] = new Action('...', $this->fakeBtnIntents, $this->fakeBtnAttr);
        }

        if (!$isLast) {
            $this->realBtnAttr
                ->replaceItem(new Attribute(Html5::ATTR_HREF, sprintf('%spage=%d', $this->baseUrl, $currentPage + 1)));

            $this->nodes[] = new Action('>', $this->realBtnIntents, $this->realBtnAttr, [], Html5::TAG_A);
        }

        if (!$isLastVisible) {
            $this->realBtnAttr
                ->replaceItem(new Attribute(Html5::ATTR_HREF, sprintf('%spage=%d', $this->baseUrl, $lastPage)));

            $this->nodes[] = new Action('>>', $this->realBtnIntents, $this->realBtnAttr, [], Html5::TAG_A);
        }
    }
}
