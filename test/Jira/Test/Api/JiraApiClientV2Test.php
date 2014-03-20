<?php

namespace Jira\Test\Api;

use Jira\Api\JiraApiClientV2;

class JiraApiClientV2Test extends \PHPUnit_Framework_TestCase
{
    const TEST_CLASS = 'Jira\Api\JiraApiClientV2';

    /**
     * This test will raise an exception if the class can't be loaded
     */
    public function testClientClassExists()
    {
        $class = new \ReflectionClass(self::TEST_CLASS);
        $this->assertEquals('ReflectionClass', get_class($class));
    }

    public function testClientClassCanBeInstantiated()
    {
        $class = new JiraApiClientV2();
        $this->assertEquals(self::TEST_CLASS, get_class($class));
    }
}
