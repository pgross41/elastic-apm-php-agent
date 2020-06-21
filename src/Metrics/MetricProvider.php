<?php


namespace Nipwaayoni\Metrics;


interface MetricProvider
{
    public function measure(): void;

    public function data(): array;
}