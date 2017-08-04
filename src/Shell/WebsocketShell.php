<?php

namespace App\Shell;

use Cake\Console\Shell;
// Your shell script
use Ratchet\WebSocket\WsServer;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use App\Pack\Chat;

/**
 * Websocket shell command.
 */
class WebsocketShell extends Shell {

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

    /**
     * main() method.
     *
     * @return bool|int Success or error code.
     */
    public function main() {
        $this->out($this->OptionParser->help());
    }

    public function run() {
        $ws = new WsServer(new Chat);
        $ws->disableVersion(0); // old, bad, protocol version
        // Make sure you're running this as root
        $server = IoServer::factory(new HttpServer($ws),8088);
        $server->run();
    }
    

}
