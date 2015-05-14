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
use Rollerworks\Component\Datagrid\DataMapper\DataMapperInterface;
use Rollerworks\Component\Datagrid\DataTransformerInterface;
use Rollerworks\Component\Datagrid\Exception\DataMappingException;
use Rollerworks\Component\Datagrid\Exception\TransformationFailedException;

/**
 * Transforms the Model to an array.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author FSi sp. z o.o. <info@fsi.pl>
 */
class ModelToArrayTransformer implements DataTransformerInterface
{
    /**
     * @var DataMapperInterface
     */
    private $dataMapper;

    /**
     * @var string[]
     */
    private $fields;

    /**
     * Constructor.
     *
     * @param DataMapperInterface $dataMapper
     * @param array               $fields
     */
    public function __construct(DataMapperInterface $dataMapper, array $fields)
    {
        $this->dataMapper = $dataMapper;
        $this->fields = $fields;
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
                        $objectValues[$field] = $this->dataMapper->getData($field, $object);
                    }

                    $values[] = $objectValues;
                }
            } else {
                foreach ($this->fields as $field) {
                    $objectValues[$field] = null !== $value ? $this->dataMapper->getData($field, $value) : null;
                }

                $values[] = $objectValues;
            }
        } catch (DataMappingException $e) {
            throw new TransformationFailedException('Unable to perform transformation due to DataMapper error.', 0, $e);
        }

        return $values;
    }
}
