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

use Symfony\Component\PropertyAccess\Exception\InvalidPropertyPathException;
use Symfony\Component\PropertyAccess\PropertyPath;

final class DataProviderException extends \RuntimeException implements ExceptionInterface
{
    public static function autoAccessorUnableToGetValue(string $columnName)
    {
        return new self(
            sprintf('Unable to get value for column "%s". Consider setting the "data_provider" option.', $columnName)
        );
    }

    public static function pathAccessorUnableToGetValue(string $columnName, PropertyPath $propertyPath)
    {
        return new self(
            sprintf('Unable to get value for column "%s" with property-path "%s".', $columnName, (string) $propertyPath)
        );
    }

    public static function invalidPropertyPath(string $columnName, InvalidPropertyPathException $e)
    {
        return new self(
            sprintf('Invalid property-path for column "%s" with message: %s', $columnName, $e->getMessage()),
            1,
            $e
        );
    }
}
