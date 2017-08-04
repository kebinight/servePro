<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SlimitTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SlimitTable Test Case
 */
class SlimitTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\SlimitTable
     */
    public $Slimit;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.slimit',
        'app.users'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Slimit') ? [] : ['className' => 'App\Model\Table\SlimitTable'];
        $this->Slimit = TableRegistry::get('Slimit', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Slimit);

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

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
