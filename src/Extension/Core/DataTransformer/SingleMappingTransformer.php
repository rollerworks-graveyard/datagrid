<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\Extension\Core\DataTransformer;

use Rollerworks\Component\Datagrid\DataTransformerInterface;

/**
 * Transforms to use the only one mapped value.
 *
 * If there is only mapped value its used.
 * This removes the need for transformers to know about mapping-data
 * unless they really need it.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class SingleMappingTransformer implements DataTransformerInterface
{
    /**
     * @var array
     */
    private $mappingFields;

    public function __construct(array $mappingFields = [])
    {
        $this->mappingFields = $mappingFields;
    }

    public function transform($value)
    {
        if (!is_array($value) || 1 !== count($value) || 1 !== count($this->mappingFields)) {
            return $value;
        }

        // If there is only one field but is does not exist the value
        // is assumed to be an array-value and not a data-mapping result.
        if (!array_key_exists(key($this->mappingFields), $value)) {
            return $value;
        }

        return reset($value);
    }
}
