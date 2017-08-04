<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SmenuTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SmenuTable Test Case
 */
class SmenuTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\SmenuTable
     */
    public $Smenu;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.smenu'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Smenu') ? [] : ['className' => 'App\Model\Table\SmenuTable'];
        $this->Smenu = TableRegistry::get('Smenu', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Smenu);

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
