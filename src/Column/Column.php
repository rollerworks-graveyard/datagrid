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
use Rollerworks\Component\Datagrid\DataTransformerInterface;
use Rollerworks\Component\Datagrid\Exception\BadMethodCallException;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class Column implements ColumnInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var ResolvedColumnTypeInterface
     */
    private $type;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var bool
     */
    protected $locked = false;

    /**
     * @var array
     */
    private $viewTransformers = [];

    /**
     * @var callable
     */
    private $dataProvider;

    /**
     * Constructor.
     *
     * @param string                      $name
     * @param ResolvedColumnTypeInterface $type
     * @param array                       $options
     *
     * @throws \InvalidArgumentException when the name is invalid
     */
    public function __construct(
        $name,
        ResolvedColumnTypeInterface $type,
        array $options = []
    ) {
        if ('' === $name) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The name "%s" contains illegal characters. Names should start with a letter, digit or underscore '.
                    'and only contain letters, digits, numbers, underscores ("_"), hyphens ("-") and colons (":").',
                    $name
                )
            );
        }

        $this->name = $name;
        $this->type = $type;
        $this->options = $options;
        $this->locked = false;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): ResolvedColumnTypeInterface
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function addViewTransformer(DataTransformerInterface $viewTransformer, $forcePrepend = false)
    {
        if ($this->locked) {
            throw new BadMethodCallException('Column setter methods cannot be accessed anymore once the data is locked.');
        }

        if ($forcePrepend) {
            array_unshift($this->viewTransformers, $viewTransformer);
        } else {
            $this->viewTransformers[] = $viewTransformer;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function resetViewTransformers()
    {
        if ($this->locked) {
            throw new BadMethodCallException('Column setter methods cannot be accessed anymore once the data is locked.');
        }

        $this->viewTransformers = [];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getViewTransformers(): array
    {
        return $this->viewTransformers;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function getOption($name, $default = null)
    {
        if (array_key_exists($name, $this->options)) {
            return $this->options[$name];
        }

        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function hasOption($name): bool
    {
        return array_key_exists($name, $this->options);
    }

    /**
     * {@inheritdoc}
     */
    public function createHeaderView(DatagridView $datagrid): HeaderView
    {
        // The methods createHeaderView(), buildHeaderView() are called
        // explicitly here in order to be able to override either of them
        // in a custom resolved column type.

        $view = $this->type->createHeaderView($this, $datagrid);
        $this->type->buildHeaderView($view, $this, $this->options);

        return $view;
    }

    /**
     * {@inheritdoc}
     */
    public function createCellView(DatagridView $datagrid, $object, $index): CellView
    {
        // The methods createCellView(), buildCellView() are called
        // explicitly here in order to be able to override either of them
        // in a custom resolved column type.

        $view = $this->type->createCellView($this, $datagrid);

        $view->attributes['row'] = $index;
        $view->value = $this->type->getValue($this, $object);
        $view->source = $object;

        $this->type->buildCellView($view, $this, $this->options);

        return $view;
    }

    /**
     * Set the data-provider for the column.
     *
     * @param callable $dataProvider
     */
    public function setDataProvider(callable $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    /**
     * Get data-provider for this column.
     *
     * @return callable
     */
    public function getDataProvider(): callable
    {
        return $this->dataProvider;
    }
}
