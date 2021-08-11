<?php

namespace App\Entity;

use League\CommonMark\Extension\FrontMatter\Data\SymfonyYamlFrontMatterParser;
use League\CommonMark\Extension\FrontMatter\FrontMatterParser;

class Template
{
    private string $name;
    private string $template;
    private string $title;

    public function __construct(
        private string $path,
        private string $locale,
        string $markdown
    ) {
        $frontMatterParser = new FrontMatterParser(new SymfonyYamlFrontMatterParser());
        $result = $frontMatterParser->parse($markdown);

        $front = $result->getFrontMatter();

        $this->name = $front['name'];
        $this->title = $front['title'];

        $this->template = $result->getContent();
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setTemplate(string $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }
    public function getFilename(): string
    {
        return basename($this->path);
    }
}
