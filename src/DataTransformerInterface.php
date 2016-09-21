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

use Rollerworks\Component\Datagrid\Exception\TransformationFailedException;

/**
 * Transforms a value from different representations.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
interface DataTransformerInterface
{
    /**
     * Transforms a value from the original representation to a transformed representation.
     *
     * This method is called to transform a normalized value to a
     * view representation eg. a localized or formatted representation.
     *
     * This method must be able to deal with empty values. Usually this will
     * be NULL, but depending on your implementation other empty values are
     * possible as well (such as empty strings). The reasoning behind this is
     * that value transformers must be chainable. If the transform() method
     * of the first value transformer outputs NULL, the second value transformer
     * must be able to process that value.
     *
     * By convention, transform() should return an empty string if NULL is
     * passed.
     *
     * @param mixed $value The value in the original representation
     *
     * @throws TransformationFailedException When the transformation fails
     *
     * @return mixed The value in the transformed representation
     */
    public function transform($value);
}
