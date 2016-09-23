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

namespace Rollerworks\Component\Datagrid;

use Rollerworks\Component\Datagrid\Extension\Core\CoreExtension;
use Rollerworks\Component\Datagrid\Util\DatagridFactoryBuilder;

/**
 * Entry point of the Datagrid system.
 *
 * *You should not use this class when integrating the Datagrid system
 * with a Framework that supports Dependency Injection.*
 *
 * Use this class to conveniently create new datagrid factories:
 *
 * <code>
 * use Rollerworks\Component\Datagrid\Datagrids;
 * use Rollerworks\Component\Datagrid\Extension\Core\Type as ColumnType;
 *
 * $datagridFactory = Datagrids::createDatagridFactory();
 *
 * $datagrid = $datagridFactory->createDatagridBuilder()
 *    ->add('id', ColumnType\NumberType::class)
 *    ->add('username', ColumnType\TextType::class)
 *    ->add('registered_on', ColumnType\DateTimeType::class)
 *    ->add('enabled', ColumnType\BooleanType::class, ['true_value' => 'Yes', 'false_value' => 'No'])
 *    ->getDatagrid('users_datagrid')
 * ;
 * </code>
 *
 * You can also add custom extensions to the datagrid factory:
 *
 * <code>
 * $datagridFactory = Datagrids::createDatagridFactoryBuilder();
 *     ->addExtension(new AcmeExtension())
 *     ->getDatagridFactory()
 * ;
 * </code>
 *
 * If you create custom types, it is not required to register them
 * as they will be be automatically loaded by there FQCN
 * `Acme\Datagrid\Type\PhoneNumberType`.
 *
 * But when they have external dependencies you need to register them
 * manually. It's recommended to create your own extensions that lazily
 * loads these types and type extensions.
 *
 * When there are no performance problems, you can also pass them directly
 * to the datagrid factory:
 *
 * <code>
 * use Rollerworks\Component\Datagrid\Datagrids;
 *
 * $datagridFactory = Datagrids::createDatagridFactoryBuilder();
 *     ->addType(new PhoneNumberType())
 *     ->addTypeExtension(new GedmoExtension())
 *     ->getDatagridFactory()
 * ;
 * </code>
 *
 * **Note:** Type extensions are not loaded automatically, you must always register them.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
final class Datagrids
{
    /**
     * Creates a DatagridFactory builder with the default configuration.
     *
     * @return DatagridFactoryBuilder
     */
    public static function createDatagridFactoryBuilder(): DatagridFactoryBuilder
    {
        $builder = new DatagridFactoryBuilder();
        $builder->addExtension(new CoreExtension());

        return $builder;
    }

    /**
     * @return DatagridFactory
     */
    public static function createDatagridFactory(): DatagridFactory
    {
        $builder = new DatagridFactoryBuilder();
        $builder->addExtension(new CoreExtension());

        return $builder->getDatagridFactory();
    }

    /**
     * This class cannot be instantiated.
     */
    private function __construct()
    {
    }
}
