<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\Column;

use Rollerworks\Component\Datagrid\DatagridInterface;
use Rollerworks\Component\Datagrid\DatagridViewInterface;
use Rollerworks\Component\Datagrid\Exception\DatagridColumnException;
use Rollerworks\Component\Datagrid\Exception\InvalidArgumentException;
use Rollerworks\Component\Datagrid\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class ResolvedColumnType implements ResolvedColumnTypeInterface
{
    /**
     * @var ColumnTypeInterface
     */
    private $innerType;

    /**
     * @var ColumnTypeExtensionInterface[]
     */
    private $typeExtensions;

    /**
     * @var ResolvedColumnType
     */
    private $parent;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * Constructor.
     *
     * @param ColumnTypeInterface            $innerType
     * @param ColumnTypeExtensionInterface[] $typeExtensions
     * @param ResolvedColumnTypeInterface    $parent
     *
     * @throws UnexpectedTypeException  When one of the given extensions is not an ColumnTypeExtensionInterface
     * @throws InvalidArgumentException When the Inner Fieldname is invalid
     */
    public function __construct(ColumnTypeInterface $innerType, array $typeExtensions = [], ResolvedColumnTypeInterface $parent = null)
    {
        if (!preg_match('/^[a-z0-9_]*$/i', $innerType->getName())) {
            throw new InvalidArgumentException(sprintf(
                'The "%s" column type name ("%s") is not valid. Names must only contain letters, numbers, and "_".',
                get_class($innerType),
                $innerType->getName()
            ));
        }

        foreach ($typeExtensions as $extension) {
            if (!$extension instanceof ColumnTypeExtensionInterface) {
                throw new UnexpectedTypeException($extension, 'Rollerworks\Component\Datagrid\Column\ColumnTypeExtensionInterface');
            }
        }

        $this->innerType = $innerType;
        $this->typeExtensions = $typeExtensions;
        $this->parent = $parent;
    }

    /**
     * Returns the name of the type.
     *
     * @return string The type name
     */
    public function getName()
    {
        return $this->innerType->getName();
    }

    /**
     * Returns the parent type.
     *
     * @return ResolvedColumnTypeInterface|null The parent type or null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Returns the wrapped column type.
     *
     * @return ColumnTypeInterface The wrapped column type
     */
    public function getInnerType()
    {
        return $this->innerType;
    }

    /**
     * Returns the extensions of the wrapped column type.
     *
     * @return ColumnTypeExtensionInterface[] An array of {@link ColumnTypeExtensionInterface} instances
     */
    public function getTypeExtensions()
    {
        return $this->typeExtensions;
    }

    /**
     * This configures the {@link ColumnInterface}.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the field.
     *
     * @param ColumnInterface $config
     * @param array           $options
     */
    public function buildType(ColumnInterface $config, array $options)
    {
        if (null !== $this->parent) {
            $this->parent->buildType($config, $options);
        }

        $this->innerType->buildColumn($config, $options);

        foreach ($this->typeExtensions as $extension) {
            $extension->buildColumn($config, $options);
        }
    }

    /**
     * Returns a new ColumnInterface instance.
     *
     * @param string            $name
     * @param DatagridInterface $datagrid
     * @param array             $options
     *
     * @return ColumnInterface
     */
    public function createColumn($name, DatagridInterface $datagrid, array $options = [])
    {
        $options = $this->getOptionsResolver()->resolve($options);
        $builder = $this->newColumn($name, $datagrid, $options);

        return $builder;
    }

    /**
     * {inheritdoc}.
     */
    public function createHeaderView(ColumnInterface $column, DatagridViewInterface $datagrid)
    {
        $view = new HeaderView($column, $datagrid, $column->getOption('label'));

        return $view;
    }

    /**
     * {inheritdoc}.
     */
    public function createCellView(ColumnInterface $column, DatagridViewInterface $datagrid)
    {
        $view = new CellView($column, $datagrid);

        return $view;
    }

    /**
     * Configures a header view for the type hierarchy.
     *
     * @param HeaderView      $view
     * @param ColumnInterface $column
     * @param array           $options
     */
    public function buildHeaderView(HeaderView $view, ColumnInterface $column, array $options)
    {
        if (null !== $this->parent) {
            $this->parent->buildHeaderView($view, $column, $options);
        }

        $this->innerType->buildHeaderView($view, $column, $options);

        foreach ($this->typeExtensions as $extension) {
            $extension->buildHeaderView($column, $view);
        }
    }

    /**
     * Configures a cell view for the type hierarchy.
     *
     * @param CellView        $view
     * @param ColumnInterface $column
     * @param array           $options
     */
    public function buildCellView(CellView $view, ColumnInterface $column, array $options)
    {
        if (null !== $this->parent) {
            $this->parent->buildCellView($view, $column, $options);
        }

        $this->innerType->buildCellView($view, $column, $options);

        foreach ($this->typeExtensions as $extension) {
            $extension->buildCellView($column, $view);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(ColumnInterface $column, $object, $useTransformers = true)
    {
        if (!$column->hasOption('field_mapping') || !count($column->getOption('field_mapping'))) {
            throw new DatagridColumnException(
                sprintf('"field_mapping" option is missing in column "%s"', $this->getName())
            );
        }

        $dataMapper = $column->getDatagrid()->getDataMapper();

        $values = [];

        foreach ($column->getOption('field_mapping') as $field) {

            // Ignore null and boolean as these fields-names are always illegal
            // CompoundColumnType sometimes has one key with a boolean value
            if (null === $field || is_bool($field)) {
                continue;
            }

            $values[$field] = $dataMapper->getData($field, $object);
        }

        if ($useTransformers) {
            $values = $this->normToView($column, $values);
        }

        return $values;
    }

    /**
     * Returns the configured options resolver used for this type.
     *
     * @return OptionsResolver The options resolver
     */
    public function getOptionsResolver()
    {
        if (null === $this->optionsResolver) {
            if (null !== $this->parent) {
                $this->optionsResolver = clone $this->parent->getOptionsResolver();
            } else {
                $this->optionsResolver = new OptionsResolver();
            }

            $this->innerType->configureOptions($this->optionsResolver);

            foreach ($this->typeExtensions as $extension) {
                $extension->configureOptions($this->optionsResolver);
            }
        }

        return $this->optionsResolver;
    }

    /**
     * Creates a new Column instance.
     *
     * Override this method if you want to customize the Column class.
     *
     * @param string            $name     The name of the column
     * @param DatagridInterface $datagrid The name of the column
     * @param array             $options  The builder options
     *
     * @return Column The new field instance
     */
    protected function newColumn($name, DatagridInterface $datagrid, array $options)
    {
        return new Column($name, $this, new EventDispatcher(), $datagrid, $options);
    }

    /**
     * Transforms the value if a value transformer is set.
     *
     * @param ColumnInterface $column
     * @param mixed           $value  The value to transform
     *
     * @return mixed
     */
    protected function normToView(ColumnInterface $column, $value)
    {
        // Scalar values should be converted to strings to
        // facilitate differentiation between empty ("") and zero (0).
        if (!$column->getViewTransformers()) {
            return null === $value || is_scalar($value) ? (string) $value : $value;
        }

        foreach ($column->getViewTransformers() as $transformer) {
            $value = $transformer->transform($value);
        }

        return $value;
    }
}
