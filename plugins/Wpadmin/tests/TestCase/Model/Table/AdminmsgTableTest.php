<?php
namespace Wpadmin\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Wpadmin\Model\Table\AdminmsgTable;

/**
 * Wpadmin\Model\Table\AdminmsgTable Test Case
 */
class AdminmsgTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Wpadmin\Model\Table\AdminmsgTable
     */
    public $Adminmsg;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.wpadmin.adminmsg'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Adminmsg') ? [] : ['className' => 'Wpadmin\Model\Table\AdminmsgTable'];
        $this->Adminmsg = TableRegistry::get('Adminmsg', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Adminmsg);

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
