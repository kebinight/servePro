<?php
namespace Wpadmin\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Wpadmin\Model\Table\ActionlogTable;

/**
 * Wpadmin\Model\Table\ActionlogTable Test Case
 */
class ActionlogTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Wpadmin\Model\Table\ActionlogTable
     */
    public $Actionlog;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.wpadmin.actionlog'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Actionlog') ? [] : ['className' => 'Wpadmin\Model\Table\ActionlogTable'];
        $this->Actionlog = TableRegistry::get('Actionlog', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Actionlog);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
