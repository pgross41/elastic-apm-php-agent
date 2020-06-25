<?php


namespace Nipwaayoni\Metrics;


class MetricCollector
{
    /** @var MetricProvider */
    private $providerClass;

    private $metrics = [];

    public function __construct()
    {
        $this->providerClass = LinuxProvider::class;
    }

    public function collect(): void
    {
        $providerClass = $this->providerClass;

        /** @var MetricProvider $provider */
        $provider = new $providerClass;

        $provider->measure();

        $this->metrics = $provider->data();
    }

    public function data(): array
    {
        return $this->metrics;
    }
}