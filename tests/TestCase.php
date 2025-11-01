<?php

namespace Tests;

use CodeIgniter\Test\CIUnitTestCase;

abstract class TestCase extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
