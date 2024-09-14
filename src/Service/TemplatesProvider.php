<?php

namespace App\Service;

use App\Entity\Template;

class TemplatesProvider
{
    private array $templates = [];

    public function __construct(private readonly string $projectDirectory)
    {
        $glob = glob(sprintf('%s/templates/messages/*/*/*.md', $this->projectDirectory));

        foreach ($glob as $path) {
            if (true === is_readable($path)) {
                $region = basename(\dirname($path, 2));
                $locale = basename(\dirname($path));

                $markdown = file_get_contents($path);

                $template = new Template($path, $locale, $markdown);

                $this->templates[$region][] = $template;
            }
        }
    }

    public function getTemplates(string $region): array
    {
        return $this->templates[$region] ?? [];
    }
}
