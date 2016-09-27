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
use Rollerworks\Component\Datagrid\Column\ColumnTypeRegistry;
use Rollerworks\Component\Datagrid\Column\ResolvedColumnTypeFactoryInterface;
use Rollerworks\Component\Datagrid\DatagridRegistryInterface;
use Rollerworks\Component\Datagrid\Extension\Core\CoreExtension;
use Rollerworks\Component\Datagrid\Extension\Core\Type\DateTimeType;
use Rollerworks\Component\Datagrid\PreloadedExtension;
use Rollerworks\Component\Datagrid\Tests\Fixtures\Extension\DateTypeExtension;
use Rollerworks\Component\Datagrid\Tests\Fixtures\Extension\FooType;
use Rollerworks\Component\Datagrid\Util\DatagridFactoryBuilder;

final class DatagridFactoryBuilderTest extends TestCase
{
    /** @var \Rollerworks\Component\Datagrid\Util\DatagridFactoryBuilder */
    private $builder;

    /** @var ResolvedColumnTypeFactoryInterface */
    private $resolvedColumnFactory;

    /** @var DatagridRegistryInterface */
    private $datagridRegistry;

    /** @before */
    public function setUpBuilder()
    {
        $this->resolvedColumnFactory = $this->createMock(ResolvedColumnTypeFactoryInterface::class);
        $this->datagridRegistry = $this->createMock(DatagridRegistryInterface::class);

        $this->builder = new DatagridFactoryBuilder();
        $this->builder->setResolvedTypeFactory($this->resolvedColumnFactory);
        $this->builder->setDatagridRegistry($this->datagridRegistry);
        $this->builder->addExtension(new CoreExtension());
        $this->builder->addType(new FooType());
        $this->builder->addTypeExtension(new DateTypeExtension());
    }

    /** @test */
    public function custom_resolvedColumnTypeFactory_is_used()
    {
        $factory = $this->builder->getDatagridFactory();

        /** @var ColumnTypeRegistry $typeRegistry */
        $typeRegistry = $this->extractObjectProperty($factory, 'typeRegistry');

        self::assertSame(
            $this->resolvedColumnFactory,
            $this->extractObjectProperty($typeRegistry, 'resolvedTypeFactory')
        );
    }

    /** @test */
    public function custom_DatagridRegistry_is_used()
    {
        $factory = $this->builder->getDatagridFactory();

        /** @var DatagridRegistryInterface $datagridRegistry */
        $datagridRegistry = $this->extractObjectProperty($factory, 'datagridRegistry');

        self::assertSame(
            $this->datagridRegistry,
            $datagridRegistry
        );
    }

    /** @test */
    public function extensions_types_are_registered()
    {
        $factory = $this->builder->getDatagridFactory();
        /** @var ColumnTypeRegistry $typeRegistry */
        $typeRegistry = $this->extractObjectProperty($factory, 'typeRegistry');

        self::assertEquals(
            [
                new CoreExtension(),
                new PreloadedExtension(
                    [FooType::class => new FooType()],
                    [DateTimeType::class => [new DateTypeExtension()]]
                ),
            ],
            $typeRegistry->getExtensions()
        );
    }

    /**
     * Extract a property from an object.
     *
     * Normally you should not do this, but as this the only
     * way to test if the object is properly build.
     * Else the test would become overly complex.
     *
     * @param object $factory
     *
     * @return mixed
     */
    private function extractObjectProperty($factory, $name)
    {
        $func = function ($name) {
            return $this->{$name};
        };

        $helper = $func->bindTo($factory, $factory);

        return $helper($name);
    }
}
