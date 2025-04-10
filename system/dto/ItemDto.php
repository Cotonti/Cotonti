<?php

declare(strict_types=1);

namespace cot\dto;

defined('COT_CODE') or die('Wrong URL');

/**
 * Item data transfer object
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
final class ItemDto
{
    /**
     * @var string
     */
    public $source;

    /**
     * @var int|string
     */
    public $id;

    /**
     * @var string
     */
    public $extensionCode;

    /**
     * @var string
     */
    public $typeTitle;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $titleHtml = '';

    /**
     * @var string
     */
    public $description = '';

    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $categoryCode = '';

    /**
     * @var string
     */
    public $categoryTitle = '';

    /**
     * @var string
     */
    public $categoryUrl = '';

    /**
     * @var int|null
     */
    public $authorId = null;

    /**
     * @var array
     */
    public $data = [];

    public function __construct(
        string $source,
        int $sourceId,
        string $extensionCode,
        string $typeTitle,
        string $title,
        string $description,
        string $url,
        ?int $authorId = null
    ) {
        $this->source = $source;
        $this->id = $sourceId;
        $this->extensionCode = $extensionCode;
        $this->typeTitle = $typeTitle;
        $this->title = $title;
        $this->description = $description;
        $this->url = $url;
        $this->authorId = $authorId;
    }

    public function getTitleHtml(): string
    {
        if ($this->titleHtml !== '') {
            return $this->titleHtml;
        }

        if ($this->url !== '') {
            return cot_rc_link($this->url, htmlspecialchars($this->title));
        }

        return $this->title;
    }
}