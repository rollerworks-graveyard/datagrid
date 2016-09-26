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

namespace Rollerworks\Component\Datagrid\Extension\Core\Type;

use Rollerworks\Component\Datagrid\Column\ColumnInterface;
use Rollerworks\Component\Datagrid\Exception\DataProviderException;
use Rollerworks\Component\Datagrid\Util\StringUtil;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\Exception\InvalidPropertyPathException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyPath;

class ColumnType extends BaseType
{
    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    /**
     * ColumnType constructor.
     *
     * @param PropertyAccessor|null $propertyAccessor
     */
    public function __construct(PropertyAccessor $propertyAccessor = null)
    {
        if (null === $propertyAccessor) {
            $propertyAccessor = PropertyAccess::createPropertyAccessor();
        }

        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('parent_data_provider', null);
        $resolver->setAllowedTypes('parent_data_provider', ['Closure', 'null']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildColumn(ColumnInterface $column, array $options)
    {
        $dataProvider = $options['data_provider'];
        $parentDataProvider = $options['parent_data_provider'];

        if (!$dataProvider instanceof \Closure) {
            $dataProvider = $parentDataProvider ?: function ($data) use ($column, $dataProvider) {
                static $path;

                if (null === $path) {
                    $path = $this->createDataProviderPath($column, $data, $dataProvider);
                }

                return $this->propertyAccessor->getValue($data, $path);
            };
        }

        $column->setDataProvider($dataProvider);
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix(): string
    {
        return StringUtil::fqcnToBlockPrefix(get_class($this));
    }

    private function createDataProviderPath(ColumnInterface $column, $data, $customPath): PropertyPath
    {
        try {
            if (null === $customPath) {
                $name = $column->getName();

                if (!$this->propertyAccessor->isReadable($data, $path = new PropertyPath(sprintf('[%s]', $name))) &&
                    !$this->propertyAccessor->isReadable($data, $path = new PropertyPath($name))
                ) {
                    throw DataProviderException::autoAccessorUnableToGetValue($name);
                }

                return $path;
            }

            if (!$this->propertyAccessor->isReadable($data, $path = new PropertyPath($customPath))) {
                throw DataProviderException::pathAccessorUnableToGetValue($column->getName(), $path);
            }

            return $path;
        } catch (InvalidPropertyPathException $e) {
            throw DataProviderException::invalidPropertyPath($column->getName(), $e);
        }
    }
}
