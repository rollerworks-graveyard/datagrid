<?php

namespace Rollerworks\Component\Datagrid\Exception;

final class DataProviderException extends \RuntimeException implements ExceptionInterface
{
    public static function autoAccessorUnableToGetValue($columnName, \Exception $previous = null)
    {
        return new self(
            sprintf('Unable to get value for column "%s". Consider setting the "data_provider" option.', $columnName),
            1,
            $previous
        );
    }
}
