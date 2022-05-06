<?php
declare(strict_types=1);
namespace FieldReports;

/**
 * PHP Bridgeで発生する例外
 * 
 */
class ReportsException extends \Exception
{
    public function __construct(\Throwable $previous, string $message = "") {
        parent::__construct(sprintf("%s: %s.", $message, $previous->getMessage()), $previous->getCode(), $previous);
    }
}
