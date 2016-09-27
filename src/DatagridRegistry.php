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

use Rollerworks\Component\Datagrid\Exception\InvalidArgumentException;
use Rollerworks\Component\Datagrid\Exception\UnexpectedTypeException;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class DatagridRegistry implements DatagridRegistryInterface
{
    private $configurators;

    /** @var \Closure[] */
    private $lazyConfigurators = [];

    /**
     * Constructor.
     *
     * Note you don't have to register the Configurator when it has no constructor
     * or setter dependencies. You can simple use the FQCN of the configurator class,
     * and will be initialized upon first usage.
     *
     * @param \Closure[] $configurators An array of lazy loading configurators.
     *                                  The Closure when called it expected to return
     *                                  the DatagridConfiguratorInterface object
     *
     * @throws UnexpectedTypeException
     */
    public function __construct(array $configurators = [])
    {
        /** @var string $name */
        foreach ($configurators as $name => $configurator) {
            if (!$configurator instanceof \Closure) {
                throw new UnexpectedTypeException($configurator, \Closure::class);
            }

            $this->lazyConfigurators[$name] = $configurator;
        }
    }

    /**
     * Returns a DatagridConfigurator by name.
     *
     * @param string $name The name of the datagrid configurator
     *
     * @throws InvalidArgumentException if the configurator can not be retrieved
     *
     * @return DatagridConfiguratorInterface
     */
    public function getConfigurator(string $name): DatagridConfiguratorInterface
    {
        if (!isset($this->configurators[$name])) {
            $configurator = null;

            if (isset($this->lazyConfigurators[$name])) {
                $configurator = $this->lazyConfigurators[$name]();
            }

            if (!$configurator) {
                // Support fully-qualified class names.
                if (!class_exists($name) || !in_array(DatagridConfiguratorInterface::class, class_implements($name), true)) {
                    throw new InvalidArgumentException(sprintf('Could not load datagrid configurator "%s"', $name));
                }

                $configurator = new $name();
            }

            $this->configurators[$name] = $configurator;
        }

        return $this->configurators[$name];
    }

    /**
     * Returns whether the given DatagridConfigurator is supported.
     *
     * @param string $name The name of the datagrid configurator
     *
     * @return bool
     */
    public function hasConfigurator(string $name): bool
    {
        if (isset($this->configurators[$name])) {
            return true;
        }

        if (isset($this->lazyConfigurators[$name])) {
            return true;
        }

        return class_exists($name) && in_array(DatagridConfiguratorInterface::class, class_implements($name), true);
    }
}
