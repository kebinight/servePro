<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SroleTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SroleTable Test Case
 */
class SroleTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\SroleTable
     */
    public $Srole;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.srole',
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
        $config = TableRegistry::exists('Srole') ? [] : ['className' => 'App\Model\Table\SroleTable'];
        $this->Srole = TableRegistry::get('Srole', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Srole);

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
