<?php
namespace Riari\Forum\Libraries;

use Decoda\Loader\AbstractLoader;

class DecodaLaravelConfigLoader extends AbstractLoader {

    /**
     * Config-string to load.
     *
     * @type string
     */
    protected $_config;

    /**
     * Take $config as parameter and save it in object
     *
     * @param array $config
     */
    public function __construct($config) {
        $this->_config = $config;
    }

    /**
     * Load the requested config.
     *
     * @return array
     */
    public function load() {

        return config($this->_config);
    }
}