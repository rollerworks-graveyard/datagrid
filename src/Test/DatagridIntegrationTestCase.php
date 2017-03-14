<?php

declare(strict_types=1);

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\Test;

use PHPUnit\Framework\TestCase;
use Rollerworks\Component\Datagrid\Column\ColumnTypeRegistry;
use Rollerworks\Component\Datagrid\Column\ResolvedColumnTypeFactory;
use Rollerworks\Component\Datagrid\DatagridFactory;
use Rollerworks\Component\Datagrid\DatagridRegistry;
use Rollerworks\Component\Datagrid\Extension\Core\CoreExtension;

abstract class DatagridIntegrationTestCase extends TestCase
{
    /**
     * @var DatagridFactory
     */
    protected $factory;

    protected function setUp()
    {
        $resolvedTypeFactory = new ResolvedColumnTypeFactory();

        $extensions = [new CoreExtension()];
        $extensions = array_merge($extensions, $this->getExtensions());

        $typesRegistry = new ColumnTypeRegistry($extensions, $resolvedTypeFactory);
        $datagridRegistry = new DatagridRegistry();
        $this->factory = new DatagridFactory($typesRegistry, $datagridRegistry);
    }

    protected function getExtensions(): array
    {
        return [];
    }
}
