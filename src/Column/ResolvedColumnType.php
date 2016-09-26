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

namespace Rollerworks\Component\Datagrid\Column;

use Rollerworks\Component\Datagrid\DatagridView;
use Rollerworks\Component\Datagrid\Exception\InvalidArgumentException;
use Rollerworks\Component\Datagrid\Exception\UnexpectedTypeException;
use Rollerworks\Component\Datagrid\Extension\Core\Type\CompoundColumnType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class ResolvedColumnType implements ResolvedColumnTypeInterface
{
    /**
     * @var ColumnTypeInterface
     */
    protected $innerType;

    /**
     * @var ResolvedColumnType
     */
    protected $parent;

    /**
     * @var bool
     */
    protected $compound = null;

    /**
     * @var ColumnTypeExtensionInterface[]
     */
    private $typeExtensions;

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
     */
    public function __construct(ColumnTypeInterface $innerType, array $typeExtensions = [], ResolvedColumnTypeInterface $parent = null)
    {
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
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return $this->innerType->getBlockPrefix();
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
    public function getInnerType(): ColumnTypeInterface
    {
        return $this->innerType;
    }

    /**
     * Returns the extensions of the wrapped column type.
     *
     * @return ColumnTypeExtensionInterface[] An array of {@link ColumnTypeExtensionInterface} instances
     */
    public function getTypeExtensions(): array
    {
        return $this->typeExtensions;
    }

    /**
     * This configures the {@link ColumnInterface}.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the column.
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
     * @param string $name
     * @param array  $options
     *
     * @return ColumnInterface
     */
    public function createColumn(string $name, array $options = []): ColumnInterface
    {
        $options = $this->getOptionsResolver()->resolve($options);

        return $this->newColumn($name, $options);
    }

    /**
     * {inheritdoc}.
     */
    public function createHeaderView(ColumnInterface $column, DatagridView $datagrid): HeaderView
    {
        return new HeaderView($column, $datagrid, $column->getOption('label'));
    }

    /**
     * {inheritdoc}.
     */
    public function createCellView(ColumnInterface $column, DatagridView $datagrid): CellView
    {
        return new CellView($column, $datagrid);
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
            $extension->buildHeaderView($view, $column, $options);
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
            $extension->buildCellView($view, $column, $options);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(ColumnInterface $column, $object)
    {
        $dataProvider = $column->getDataProvider();
        $value = $dataProvider($object);

        if (null !== ($transformer = $column->getViewTransformer())) {
            $value = $transformer->transform($value);
        }

        return $value;
    }

    /**
     * Returns the configured options resolver used for this type.
     *
     * @return OptionsResolver The options resolver
     */
    public function getOptionsResolver(): OptionsResolver
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
     * @param string $name    The name of the column
     * @param array  $options The builder options
     *
     * @return ColumnInterface The new column instance
     */
    protected function newColumn($name, array $options): ColumnInterface
    {
        // Special case of CompoundColumnType which requires that child columns
        // are set afterwards. Whenever you extend this class, make sure to honor this
        // special use-case.
        if (null === $this->compound) {
            $this->compound = $this->isCompound();
        }

        if ($this->compound) {
            return new CompoundColumn($name, $this, $options);
        }

        return new Column($name, $this, $options);
    }

    /**
     * Determines whether this type is a compound.
     *
     * @return bool
     */
    protected function isCompound(): bool
    {
        if ($this->innerType instanceof CompoundColumnType) {
            return true;
        }

        for ($type = $this->parent; null !== $type; $type = $type->getParent()) {
            if ($type->getInnerType() instanceof CompoundColumnType) {
                return true;
            }
        }

        return false;
    }
}
