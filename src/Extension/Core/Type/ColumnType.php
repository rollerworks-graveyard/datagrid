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

use Rollerworks\Component\Datagrid\Column\CellView;
use Rollerworks\Component\Datagrid\Column\ColumnInterface;
use Rollerworks\Component\Datagrid\Column\ColumnTypeInterface;
use Rollerworks\Component\Datagrid\Column\HeaderView;
use Rollerworks\Component\Datagrid\Exception\DataProviderException;
use Rollerworks\Component\Datagrid\Util\StringUtil;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\Exception\InvalidPropertyPathException;
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
        $resolver->setDefaults(['data_provider' => null, 'label' => null]);
        $resolver->setDefault('parent_column', null);

        $resolver->setAllowedTypes('label', ['string', 'null']);
        $resolver->setAllowedTypes('data_provider', ['Closure', 'null', 'string', PropertyPath::class]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildColumn(ColumnInterface $column, array $options)
    {
        $dataProvider = $options['data_provider'];

        if (!$dataProvider instanceof \Closure) {
            $dataProvider = function ($data) use ($column, $dataProvider) {
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
