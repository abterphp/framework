<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Template;

interface IBuilder
{
    const LABEL   = 'label';
    const CONTENT = 'body';
    const IMAGE   = 'image';

    const LIST_TAG    = 'list-tag';
    const ITEM_TAG    = 'item-tag';
    const LABEL_TAG   = 'label-tag';
    const CONTENT_TAG = 'content-tag';
    const IMAGE_TAG   = 'image-tag';

    const LIST_CLASS    = 'list-class';
    const ITEM_CLASS    = 'item-class';
    const LABEL_CLASS   = 'label-class';
    const CONTENT_CLASS = 'content-class';
    const IMAGE_CLASS   = 'image-class';

    const WITH_LABEL_OPTION = 'with-label';
    const WITH_IMAGE_OPTION = 'with-image';

    /**
     * @param mixed               $data
     * @param ParsedTemplate|null $template
     *
     * @return IData
     */
    public function build($data, ?ParsedTemplate $template = null): IData;

    /**
     * @return string
     */
    public function getIdentifier(): string;
}
