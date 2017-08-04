<?php

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\View;

/**
 * Lm helper
 */
class LmHelper extends Helper {

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];
    public $helpers = ['Glide'];

    public function makeEdit($title, $url) {
        // Use the HTML helper to output
        // Formatted data:

        $link = $this->Html->link($title, $url, ['class' => 'edit']);

        return '<div class="editOuter">' . $link . '</div>';
    }

}
