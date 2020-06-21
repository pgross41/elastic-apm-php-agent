<?php


namespace Nipwaayoni\Metrics;


class MetricCollector
{
    /** @var array */
    private $registeredMetrics = [];

    private $metrics = [];

    public function collect(): void
    {
        $collected = [];
        foreach ($this->registeredMetrics as $metric) {
            /** @var MetricProvider $metric */
            $metric = new $metric();

            $metric->measure();

            $collected[] = $metric->data();
        }

        $this->metrics = array_merge($collected);
    }
}