<?php

namespace Nipwaayoni\Exception;

class ConfigurationFileNotFoundException extends ElasticApmException
{
    public function __construct(string $file, int $code = 0, \Throwable $previous = null)
    {
        parent::__construct(sprintf('The specified configuration file could not be found: %s', $file), $code, $previous);
    }
}
