<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tests')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony'                => true,
        'array_syntax'            => ['syntax' => 'short'],
        'ordered_imports'         => [
            'imports_order'  => [
                'class', 'function', 'const',
            ],
            'sort_algorithm' => 'alpha',
        ],
        'no_unused_imports'       => true,
        'global_namespace_import' => ['import_classes' => true, 'import_constants' => true, 'import_functions' => true],
    ])
    ->setFinder($finder)
;
