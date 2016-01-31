<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\Extension\Core\ColumnType;

use Rollerworks\Component\Datagrid\Column\CellView;
use Rollerworks\Component\Datagrid\Column\ColumnInterface;
use Rollerworks\Component\Datagrid\Column\ColumnTypeInterface;
use Rollerworks\Component\Datagrid\Column\HeaderView;
use Rollerworks\Component\Datagrid\Exception\DataProviderException;
use Rollerworks\Component\Datagrid\Util\StringUtil;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyPath;

class ColumnType implements ColumnTypeInterface
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
        $resolver->setRequired(['label']);
        $resolver->setDefaults(['data_provider' => null]);

        $resolver->setAllowedTypes('label', 'string');
        $resolver->setAllowedTypes('data_provider', ['callable', 'null']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildColumn(ColumnInterface $column, array $options)
    {
        $dataProvider = $options['data_provider'];

        if (null === $dataProvider) {
            $dataProvider = $this->createDataProvider($column);
        }

        $column->setDataProvider($dataProvider);
    }

    /**
     * {@inheritdoc}
     */
    public function buildCellView(CellView $view, ColumnInterface $column, array $options)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildHeaderView(HeaderView $view, ColumnInterface $column, array $options)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * @return string The prefix of the template block name.
     */
    public function getBlockPrefix()
    {
        return StringUtil::fqcnToBlockPrefix(get_class($this));
    }

    private function createDataProvider(ColumnInterface $column)
    {
        return function ($data) use ($column) {
            $path = null;

            if (null === $path) {
                $name = $column->getName();

                if (!$this->propertyAccessor->isReadable($data, $path = new PropertyPath(sprintf('[%s]', $name))) &&
                    !$this->propertyAccessor->isReadable($data, $path = new PropertyPath($name))
                ) {
                    throw DataProviderException::autoAccessorUnableToGetValue($column->getName());
                }
            }

            return $this->propertyAccessor->getValue($data, $path);
        };
    }
}
