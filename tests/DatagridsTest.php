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

namespace Rollerworks\Component\Datagrid\Tests;

use PHPUnit\Framework\TestCase;
use Rollerworks\Component\Datagrid\Datagrids;
use Rollerworks\Component\Datagrid\Extension\Core\Type as ColumnType;

final class DatagridsTest extends TestCase
{
    /** @test */
    public function creates_a_datagrid_factory()
    {
        $datagridFactory = Datagrids::createDatagridFactory();

        $datagrid = $datagridFactory->createDatagridBuilder()
            ->add('id', ColumnType\NumberType::class)
            ->add('username', ColumnType\TextType::class)
            ->add('registered_on', ColumnType\DateTimeType::class)
            ->add('enabled', ColumnType\BooleanType::class, ['true_value' => 'Yes', 'false_value' => 'No'])
            ->getDatagrid('users_datagrid')
        ;

        // Dummy test, once the Datagrid is generated all is well.
        self::assertEquals('users_datagrid', $datagrid->getName());
    }

    /** @test */
    public function creates_a_datagrid_factory_builder()
    {
        $datagridFactory = Datagrids::createDatagridFactoryBuilder()->getDatagridFactory();

        $datagrid = $datagridFactory->createDatagridBuilder()
            ->add('id', ColumnType\NumberType::class)
            ->add('username', ColumnType\TextType::class)
            ->add('registered_on', ColumnType\DateTimeType::class)
            ->add('enabled', ColumnType\BooleanType::class, ['true_value' => 'Yes', 'false_value' => 'No'])
            ->getDatagrid('users_datagrid')
        ;

        // Dummy test, once the Datagrid is generated all is well.
        self::assertEquals('users_datagrid', $datagrid->getName());
    }
}
