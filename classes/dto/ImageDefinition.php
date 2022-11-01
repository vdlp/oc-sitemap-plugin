<?php

declare(strict_types=1);

namespace Vdlp\Sitemap\Classes\Dto;

use Vdlp\Sitemap\Classes\Contracts\Dto;

final class ImageDefinition implements Dto
{
    public function __construct(
        private string $url,
        private ?string $title = null
    ) {
    }

    public static function fromArray(array $data): ImageDefinition
    {
        return new self($data['url'] ?? '', $data['title'] ?? null);
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): ImageDefinition
    {
        $this->url = $url;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): ImageDefinition
    {
        $this->title = $title;

        return $this;
    }
}
