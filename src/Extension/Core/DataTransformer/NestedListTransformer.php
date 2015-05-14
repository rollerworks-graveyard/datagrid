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
 * Transforms all the nested values.
 */
class NestedListTransformer implements DataTransformerInterface
{
    /**
     * @var DataTransformerInterface[]
     */
    private $transformers = [];

    /**
     * @param DataTransformerInterface $transformer
     */
    public function addTransformer(DataTransformerInterface $transformer)
    {
        $this->transformers[] = $transformer;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (!is_array($value) || !$this->transformers) {
            return $value;
        }

        $values = [];

        foreach ($value as $val) {
            $values[] = $this->normToView($val);
        }

        return $values;
    }

    /**
     * Transforms the value if a value transformer is set.
     *
     * @param mixed $value The value to transform
     *
     * @return mixed
     */
    private function normToView($value)
    {
        // Scalar values should be converted to strings to
        // facilitate differentiation between empty ("") and zero (0).
        if (!$this->transformers) {
            return null === $value || is_scalar($value) ? (string) $value : $value;
        }

        foreach ($this->transformers as $transformer) {
            $value = $transformer->transform($value);
        }

        return $value;
    }
}
