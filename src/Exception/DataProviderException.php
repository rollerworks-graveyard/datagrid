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

namespace Rollerworks\Component\Datagrid\Exception;

final class DataProviderException extends \RuntimeException implements ExceptionInterface
{
    public static function autoAccessorUnableToGetValue(string $columnName, \Exception $previous = null)
    {
        return new self(
            sprintf('Unable to get value for column "%s". Consider setting the "data_provider" option.', $columnName),
            1,
            $previous
        );
    }
}
