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
 * Base class for all view related classes.
 *
 * This class can be used as a type hint, but should
 * not be directly extended by external classes.
 */
abstract class BaseView
{
    /**
     * Extra variables for view rendering.
     *
     * It's possible to set values directly.
     * But the property type itself should not be changed!
     *
     * @var array
     */
    public $vars = [];

    /**
     * Get a variable value by key.
     *
     * This method should only be used when the key can null.
     * Else it's faster to get the var's value directly.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getVar(string $key, $default = null)
    {
        if (array_key_exists($key, $this->vars)) {
            return $this->vars[$key];
        }

        return $default;
    }
}
