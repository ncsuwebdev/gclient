<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('node_modules')
    ->exclude('vendor')
    ->in(__DIR__);

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2' => true,
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'binary_operator_spaces' => ['align_double_arrow' => true, 'align_equals' => null],
        'concat_space' => ['spacing' => 'one'],
        'ordered_imports' => true,
        'phpdoc_summary' => false,
    ])
    ->setFinder($finder);
