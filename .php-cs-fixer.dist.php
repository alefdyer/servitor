<?php


$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->notPath([
        'bootstrap/cache',
        'storage',
    ]);
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PER-CS' => true,
        '@PHP82Migration' => true,
    ])
    ->setFinder($finder)
;
