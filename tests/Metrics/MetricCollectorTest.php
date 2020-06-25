<?php

namespace Nipwaayoni\Tests\Metrics;

use Nipwaayoni\Metrics\MetricCollector;
use Nipwaayoni\Tests\TestCase;

class MetricCollectorTest extends TestCase
{
    public function testCollectLinuxMetrics(): void
    {
        $collector = new MetricCollector();

        $collector->collect();

        print_r($collector->data());
    }
}
