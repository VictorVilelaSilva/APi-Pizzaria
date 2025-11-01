<?php

namespace Tests;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

abstract class TestCase extends CIUnitTestCase
{
    use DatabaseTestTrait;

    /**
     * Should run migration on setUp
     *
     * @var bool
     */
    protected $migrate = true;

    /**
     * Should run seeder on setUp
     *
     * @var bool|string
     */
    protected $seed = '';

    /**
     * The namespace(s) to help us find the migration classes
     *
     * @var array|string|null
     */
    protected $namespace = 'App';

    /**
     * Should the database be refreshed before test?
     *
     * @var bool
     */
    protected $refresh = true;

    /**
     * The name of the database group to connect to
     *
     * @var string
     */
    protected $DBGroup = 'tests';

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
