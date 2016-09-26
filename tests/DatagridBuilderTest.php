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
use Prophecy\Argument;
use Rollerworks\Component\Datagrid\Column\ColumnInterface;
use Rollerworks\Component\Datagrid\Column\CompoundColumn;
use Rollerworks\Component\Datagrid\Column\ResolvedColumnType;
use Rollerworks\Component\Datagrid\DatagridBuilder;
use Rollerworks\Component\Datagrid\DatagridFactoryInterface;
use Rollerworks\Component\Datagrid\DatagridInterface;
use Rollerworks\Component\Datagrid\Extension\Core\Type\ColumnType;
use Rollerworks\Component\Datagrid\Extension\Core\Type\CompoundColumnType;
use Rollerworks\Component\Datagrid\Extension\Core\Type\NumberType;
use Rollerworks\Component\Datagrid\Extension\Core\Type\TextType;

final class DatagridBuilderTest extends TestCase
{
    /**
     * @var DatagridFactoryInterface
     */
    private $factory;

    protected function setUp()
    {
        // Ensure access to the testing context.
        $test = $this;

        $creator = function ($args) use ($test) {
            // 0=name, 1=type, 2=options
            $type = new $args[1]();

            if ($type instanceof CompoundColumnType) {
                $column = $test->createMock(CompoundColumn::class);
                $column->expects(self::once())->method('setColumns');
            } else {
                $column = $test->createMock(ColumnInterface::class);
            }

            $column->expects(self::any())->method('getName')->willReturn($args[0]);
            $column->expects(self::any())->method('getType')->willReturn(new ResolvedColumnType($type));
            $column->expects(self::any())->method('getOptions')->willReturn($args[2]);

            return $column;
        };

        $factory = $this->prophesize(DatagridFactoryInterface::class);
        $factory->createColumn(Argument::any(), Argument::any(), Argument::any())->will($creator);

        $this->factory = $factory->reveal();
    }

    /** @test */
    public function it_generates_datagrid()
    {
        $builder = new DatagridBuilder($this->factory);
        $builder->add('id', NumberType::class);
        $builder->add('name', TextType::class, ['format' => '%s']);

        self::assertTrue($builder->has('id'));
        self::assertTrue($builder->has('name'));
        self::assertFalse($builder->has('date'));

        $datagrid = $builder->getDatagrid('my_grid');

        self::assertSame('my_grid', $datagrid->getName());
        self::assertDatagridHasColumn($datagrid, 'id', NumberType::class);
        self::assertDatagridHasColumn($datagrid, 'name', TextType::class, ['format' => '%s']);
    }

    /** @test */
    public function it_generates_a_compoundColumn()
    {
        $builder = new DatagridBuilder($this->factory);
        $builder->add('id', NumberType::class);
        $builder->add('name', TextType::class, ['format' => '%s']);
        $builder
            ->createCompound('actions', ['abel' => 'act'])
                ->add('id', NumberType::class)
            ->end()
        ;

        $datagrid = $builder->getDatagrid('my_grid');

        self::assertDatagridHasColumn(
            $datagrid,
            'actions',
            CompoundColumnType::class,
            ['abel' => 'act', 'data_provider' => null]
        );
    }

    /** @test */
    public function it_gets_a_resolved_column()
    {
        $builder = new DatagridBuilder($this->factory);
        $builder->add('id', NumberType::class);
        $builder->set($c = $this->factory->createColumn('name', TextType::class, ['format' => '%s']));

        self::assertTrue($builder->has('id'));
        self::assertTrue($builder->has('name'));
        self::assertFalse($builder->has('date'));

        self::assertColumnEquals($builder->get('id'), 'id', NumberType::class);
        self::assertSame($c, $builder->get('name'));

        self::assertTrue($builder->has('id'));
        self::assertTrue($builder->has('name'));
        self::assertFalse($builder->has('date'));
    }

    /** @test */
    public function it_returns_a_new_datagrid_every_time()
    {
        $builder = new DatagridBuilder($this->factory);
        $builder->add('id', NumberType::class);
        $builder->add('name', TextType::class, ['format' => '%s']);

        $datagrid = $builder->getDatagrid('my_grid');
        $datagrid2 = $builder->getDatagrid('my_grid'); // duplicate name not allowed, but not checked here.

        self::assertNotSame($datagrid, $datagrid2);

        $builder->remove('id');

        $datagrid3 = $builder->getDatagrid('my_grid');

        self::assertTrue($datagrid->hasColumn('id'));
        self::assertTrue($datagrid2->hasColumn('id'));
        self::assertFalse($datagrid3->hasColumn('id'));
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

    private static function assertDatagridHasColumn(
        DatagridInterface $datagrid,
        string $name,
        string $type = ColumnType::class,
        array $options = null
    ) {
        self::assertTrue($datagrid->hasColumn($name), sprintf('Datagrid does not have a column named "%s"', $name));
        self::assertColumnEquals($datagrid->getColumn($name), $name, $type, $options);
    }
}
