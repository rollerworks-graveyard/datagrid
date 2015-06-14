<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\Test;

/**
 * Base class for performance tests.
 *
 * Copied from Doctrine 2's OrmPerformanceTestCase.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
abstract class DatagridPerformanceTestCase extends DatagridIntegrationTestCase
{
    /**
     * @var int
     */
    protected $maxRunningTime = 0;

    /**
     * {@inheritdoc}
     */
    protected function runTest()
    {
        $s = microtime(true);
        parent::runTest();
        $time = microtime(true) - $s;

        if ($this->maxRunningTime !== 0 && $time > $this->maxRunningTime) {
            $this->fail(
                sprintf(
                    'expected running time: <= %s but was: %s',

                    $this->maxRunningTime,
                    $time
                )
            );
        }
    }

    /**
     * @param int $maxRunningTime
     *
     * @throws \InvalidArgumentException
     */
    public function setMaxRunningTime($maxRunningTime)
    {
        if (is_int($maxRunningTime) && $maxRunningTime >= 0) {
            $this->maxRunningTime = $maxRunningTime;
        } else {
            throw new \InvalidArgumentException();
        }
    }

    /**
     * @return int
     */
    public function getMaxRunningTime()
    {
        return $this->maxRunningTime;
    }
}
