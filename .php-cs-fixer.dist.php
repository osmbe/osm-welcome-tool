<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('node_modules')
    ->exclude('var');

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'concat_space' => ['spacing' => 'one'],
    ])
    ->setFinder($finder);
