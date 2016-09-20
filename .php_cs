<?php

require_once __DIR__.'/vendor/sllh/php-cs-fixer-styleci-bridge/autoload.php';

$header = <<<EOF
This file is part of the RollerworksDatagrid package.

(c) Sebastiaan Stok <s.stok@rollerscapes.net>

This source file is subject to the MIT license that is bundled
with this source code in the file LICENSE.
EOF;

$config = SLLH\StyleCIBridge\ConfigBridge::create()
    ->setUsingCache(true)
    ->setRiskyAllowed(true)
;

$config->setRules(
    array_merge(
        $config->getRules(),
        ['header_comment' => ['header' => $header]]
    )
);

return $config;
