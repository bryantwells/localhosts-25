<?php

use Symfony\Component\Yaml\Yaml;

class Site {

    public $path;
    public $title;
    public $structure;
    public $entries;

    function __construct($path) {
        
        $this->path = $path;
        $this->init();
    }

    function init() {
        
        $meta = Yaml::parseFile($this->path . '/_global.yml');
        $this->parseMeta($meta);
        $this->parseContent();
    }

    function parseMeta($meta) {

        // add each meta item as a property on the collection object
        foreach($meta as $metaItemKey => $metaItemValue) {
            $this->$metaItemKey = $metaItemValue;
        }
    }

    function parseContent() {

        // get all subdirectories in content folder
        $dirList = glob($this->path . '/[0-9]*', GLOB_ONLYDIR);

        // parse each folder based on the enclosed YAML file
        foreach($dirList as $dir) {
            $this->parseFolder($dir);
        }
    }

    function parseFolder($dir) {

        // get directory name
        $segments = explode('/', $dir);
        $dirname = end($segments);

        // check to see if the folder is registered in the _global structure
        if ($this->structure[$dirname]) {

            // get structure and title of section
            $structure = $this->structure[$dirname]; 

            // id from folder name
            $segments = explode('/', $dir);
            $id = end($segments);

            // create entry
            $this->entries[$id] = new Entry($id, $dir, $structure, '');

            // register route
            Router::registerRoute($this->entries[$id]);
        }
    }

}