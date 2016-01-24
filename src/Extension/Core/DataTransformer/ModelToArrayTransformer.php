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

use Doctrine\Common\Collections\Collection;
use Rollerworks\Component\Datagrid\DataTransformerInterface;
use Rollerworks\Component\Datagrid\Exception\TransformationFailedException;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\Exception\ExceptionInterface;

/**
 * Transforms the Model to an array.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author FSi sp. z o.o. <info@fsi.pl>
 */
class ModelToArrayTransformer implements DataTransformerInterface
{

    /**
     * @var string[]
     */
    private $fields;

    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    /**
     * Constructor.
     *
     * @param PropertyAccessor $propertyAccessor
     * @param array            $fields
     */
    public function __construct(PropertyAccessor $propertyAccessor, array $fields)
    {
        $this->fields = $fields;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if ($value instanceof Collection) {
            $value = $value->toArray();
        }

        $values = [];
        $objectValues = [];

        try {
            if (is_array($value)) {
                foreach ($value as $object) {
                    foreach ($this->fields as $field) {
                        $objectValues[$field] = $this->propertyAccessor->getValue($object, $field);
                    }

                    $values[] = $objectValues;
                }
            } else {
                foreach ($this->fields as $field) {
                    $objectValues[$field] = null !== $value ? $this->propertyAccessor->getValue($value, $field) : null;
                }

                $values[] = $objectValues;
            }
        } catch (ExceptionInterface $e) {
            throw new TransformationFailedException('Unable to perform transformation due to PropertyAccessor error.', 0, $e);
        }

        return $values;
    }
}
