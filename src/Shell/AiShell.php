<?php

namespace App\Shell;

use App\Pack\Aitask;
use Cake\Console\Shell;

/**
 * 机器人干扰脚本
 * Ai shell command.
 */
class AiShell extends Shell {
    protected $aiTask;
    /**
     * Manage the available sub-commands along with their arguments and help
     *
     * @see http://book.cakephp.org/3.0/en/console-and-shells.html#configuring-options-and-generating-help
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser() {
        $parser = parent::getOptionParser();
        return $parser;
    }

    public function __construct(\Cake\Console\ConsoleIo $io = null) {
        $this->aiTask = new Aitask();
        parent::__construct($io);
    }

    /**
     * main() method.
     *
     * @return bool|int Success or error code.
     */
    public function main() {
        $this->out($this->OptionParser->help());
    }


    /**
     * 执行机器人任务
     */
    public function runTask() {
        $this->aiTask->execTask();
    }

}
