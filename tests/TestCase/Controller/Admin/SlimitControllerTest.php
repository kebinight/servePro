<?php
namespace App\Test\TestCase\Controller\Admin;

use App\Controller\Admin\SlimitController;
use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\Admin\SlimitController Test Case
 */
class SlimitControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.slimit',
        'app.admin',
        'app.roles',
        'app.slimits',
        'app.s_role_limit',
        'app.role',
        'app.s_user_role'
    ];

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
