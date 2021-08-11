<?php

namespace App\Entity;

use League\CommonMark\Extension\FrontMatter\Data\SymfonyYamlFrontMatterParser;
use League\CommonMark\Extension\FrontMatter\FrontMatterParser;

class Template
{
    private string $name;
    private string $title;
    private string $locale;
    private string $template;

    public function __construct(string $locale, string $markdown)
    {
        $this->locale = $locale;

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
}
