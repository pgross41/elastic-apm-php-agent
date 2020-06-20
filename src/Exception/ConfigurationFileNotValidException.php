<?php

namespace Nipwaayoni\Exception;

class ConfigurationFileNotValidException extends ElasticApmException
{
    public function __construct(string $message, int $code = 0, \Throwable $previous = null)
    {
        parent::__construct(sprintf('The specified configuration file could not be required: %s', $message), $code, $previous);
    }
}
