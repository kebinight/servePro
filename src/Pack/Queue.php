<?php

/**
* Encoding     :   UTF-8
* Created on   :   2016-11-29 10:15:24 by caowenpeng , caowenpeng1990@126.com
*/

namespace App\Pack;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface {

    public function onClose(ConnectionInterface $conn) {
        
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        
    }

    public function onOpen(ConnectionInterface $conn) {
        echo "New connection! ({$conn->resourceId})\n";
    }

}