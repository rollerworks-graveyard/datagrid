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

/**
 * A DatagridConfigurator configures a DatagridBuilder instance.
 *
 * The purpose of a configurator is to allow re-usage of a Datagrid.
 * When you want to combine configurators, simply use PHP inheritance and
 * traits.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
interface DatagridConfiguratorInterface
{
    public function buildDatagrid(DatagridBuilderInterface $builder, array $options);
}
