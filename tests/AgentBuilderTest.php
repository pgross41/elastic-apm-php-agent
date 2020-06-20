<?php

namespace Nipwaayoni\Tests;

use Nipwaayoni\Agent;
use Nipwaayoni\AgentBuilder;
use Nipwaayoni\Config;
use PHPUnit\Framework\TestCase;

class AgentBuilderTest extends TestCase
{
    /**
     * This test is sort of pointless, but demonstrates how the fluent
     * building works.
     *
     * @throws \Nipwaayoni\Exception\MissingAppNameException
     */
    public function testCanCreateAgentWithFluentCalls(): void
    {
        $agent = (new AgentBuilder())
            ->withConfig(new Config(['appName' => 'test']))
            ->withTagData(['my-tag'])
            ->build();

        $this->assertInstanceOf(Agent::class, $agent);
    }

    public function testCreatesAgentFromConfigurationArray(): void
    {
        $agent = AgentBuilder::create(['appName' => 'Test Created App']);

        $this->assertEquals('Test Created App', $agent->getConfig()->get('appName'));
    }
}
