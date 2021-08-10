<?php

namespace App\Service;

use App\Entity\Template;

class TemplatesProvider
{
    private array $templates = [];

    public function __construct(private string $projectDirectory)
    {
        $markdown = file_get_contents(sprintf('%s/templates/messages/belgium/en/default.md', $this->projectDirectory));

        $template = new Template('en', $markdown);

        $this->templates[] = $template;
    }

    public function getTemplates(): array
    {
        return $this->templates;
    }
}
