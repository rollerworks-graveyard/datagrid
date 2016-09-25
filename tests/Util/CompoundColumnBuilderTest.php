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

namespace Rollerworks\Component\Datagrid\Tests\Util;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Rollerworks\Component\Datagrid\Column\Column;
use Rollerworks\Component\Datagrid\Column\ColumnInterface;
use Rollerworks\Component\Datagrid\Column\CompoundColumn;
use Rollerworks\Component\Datagrid\Column\ResolvedColumnType;
use Rollerworks\Component\Datagrid\DatagridBuilderInterface;
use Rollerworks\Component\Datagrid\DatagridFactoryInterface;
use Rollerworks\Component\Datagrid\Extension\Core\Type\ColumnType;
use Rollerworks\Component\Datagrid\Extension\Core\Type\CompoundColumnType;
use Rollerworks\Component\Datagrid\Extension\Core\Type\NumberType;
use Rollerworks\Component\Datagrid\Extension\Core\Type\TextType;
use Rollerworks\Component\Datagrid\Util\CompoundColumnBuilder;

class CompoundColumnBuilderTest extends TestCase
{
    /**
     * @var DatagridFactoryInterface
     */
    private $factory;

    protected function setUp()
    {
        // Use an actual implementation as the details for CompoundColumn matter heavily here.
        $creator = function ($args) {
            // 0=name, 1=type, 2=options
            $type = new $args[1]();

            if ($type instanceof CompoundColumnType) {
                return new CompoundColumn($args[0], new ResolvedColumnType($type), $args[2]);
            }

            return new Column($args[0], new ResolvedColumnType($type), $args[2]);
        };

        $factory = $this->prophesize(DatagridFactoryInterface::class);
        $factory->createColumn(Argument::any(), Argument::any(), Argument::any())->will($creator);

        $this->factory = $factory->reveal();
    }

    /** @test */
    public function it_registers_columns()
    {
        /** @var CompoundColumn $compoundColumn */
        $compoundColumn = null;

        $datagridBuilder = $this->prophesize(DatagridBuilderInterface::class);
        $datagridBuilder->set(
            Argument::that(
                function ($column) use (&$compoundColumn) {
                    self::assertInstanceOf(CompoundColumn::class, $column);

                    $compoundColumn = $column;

                    return true;
                }
            )
        )->shouldBeCalledTimes(1);

        $builder = new CompoundColumnBuilder($this->factory, $datagridBuilder->reveal(), 'actions', ['label' => 'act']);
        $builder->add('id', NumberType::class);
        $builder->add('name', TextType::class, ['format' => '%s']);

        self::assertTrue($builder->has('id'));
        self::assertTrue($builder->has('name'));
        self::assertFalse($builder->has('date'));

        // Register it.
        $builder->end();

        if (!$compoundColumn) {
            self::fail('$compoundColumn was not set. So set() was not called internally.');
        }

        self::assertEquals('actions', $compoundColumn->getName());
        self::assertEquals(['label' => 'act'], $compoundColumn->getOptions());

        $columns = $compoundColumn->getColumns();

        self::assertArrayHasKey('id', $columns);
        self::assertArrayHasKey('name', $columns);
        self::assertArrayNotHasKey('date', $columns);

        self::assertColumnEquals($columns['id'], 'id', NumberType::class, ['parent_column' => $compoundColumn]);
        self::assertColumnEquals($columns['name'], 'name', TextType::class, ['format' => '%s', 'parent_column' => $compoundColumn]);
    }

    private static function assertColumnEquals(
        ColumnInterface $column,
        string $name,
        string $type = ColumnType::class,
        array $options = null
    ) {
        self::assertEquals($name, $column->getName());
        self::assertInstanceOf($type, $column->getType()->getInnerType());

        if (null !== $options) {
            self::assertEquals($options, $column->getOptions());
        }
    }
}
