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

namespace Rollerworks\Component\Datagrid\Extension\Core\DataTransformer;

use Rollerworks\Component\Datagrid\DataTransformerInterface;

/**
 * The ChainTransformer allows to combine multiple transformers.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
final class ChainTransformer implements DataTransformerInterface
{
    /**
     * @var DataTransformerInterface[]
     */
    private $transformers;

    /**
     * Constructor.
     *
     * @param DataTransformerInterface[] ...$transformers Transformers provided as variadic parameter
     */
    public function __construct(DataTransformerInterface ...$transformers)
    {
        $this->transformers = $transformers;
    }

    public function append(DataTransformerInterface $transformer): self
    {
        $this->transformers[] = $transformer;

        return $this;
    }

    public function prepend(DataTransformerInterface $transformer): self
    {
        array_unshift($this->transformers, $transformer);

        return $this;
    }

    /**
     * @return DataTransformerInterface[]
     */
    public function all(): array
    {
        return $this->transformers;
    }

    public function reset(): self
    {
        $this->transformers = [];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        foreach ($this->transformers as $transformer) {
            $value = $transformer->transform($value);
        }

        return $value;
    }
}
