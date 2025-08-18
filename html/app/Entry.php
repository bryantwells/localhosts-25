<?php

use Symfony\Component\Yaml\Yaml;

class Entry {

    public $id;
    public $path;
    public $uri;
    public $parentUri;
    public $template;
    public $meta;
    public $contents;
    public $children;

    function __construct($id, $path, $structure, $parentUri) {
        $this->id = $id;
        $this->path = $path;
        $this->parentUri = $parentUri;
        $this->init($structure);
    }

    function init($structure = []) {

        // get schema of the entry
        $schema = array_key_exists('schema', $structure)
            ? $structure['schema']
            : [];

        // get template of the entry
        $this->template = array_key_exists('template', $structure)
            ? $structure['template']
            : '';

        // if the entry has any meta information...
        if (file_exists($this->path . '/_meta.yml')) {
            $meta = Yaml::parseFile($this->path . '/_meta.yml');
            $this->parseMeta($meta);
        }

        // set the uri for the entry
        $this->uri = (isset($this->meta->slug))
            ? $this->parentUri . '/' . $this->meta->slug
            : $this->parentUri . '/' . $this->id;

        // parse the files in the entry
        $this->parseEntry($schema);

        // get list of child directories and schema
        $childDirList = glob($this->path . '/[0-9]*', GLOB_ONLYDIR);
        $childStructure = array_key_exists('children', $structure)
            ? $structure['children']
            : [];

        // if the entry has children
        if ($childDirList && count($childDirList) > 0) {
            $this->parseChildren($childDirList, $childStructure);
        }
    }

    function parseMeta($meta) {

        // add each meta item as a property on the collection object
        $this->meta = new stdClass();
        foreach($meta as $metaItemKey => $metaItemValue) {
            $this->meta->$metaItemKey = $metaItemValue;
        }

    }

    function parseEntry($schema) {

        // parse entry based on given schema
        foreach($schema as $schemaItemKey => $schemaItemValue) {

            // get file paths based on the item's associated file extensions
            // e.g.: images: ['jpg', 'jpeg']
            $fileList = [];
            foreach($schemaItemValue as $extension) {
                $fileList = array_merge($fileList, glob($this->path . '/[!_]*.' . $extension));
            }
            sort($fileList);
            $this->$schemaItemKey = $fileList;

            // parse files
            foreach($this->$schemaItemKey as $fileKey => $filePath) {
                $filename = pathinfo($filePath)['filename'];
                $this->$schemaItemKey[$fileKey] = new File($filePath);
            }
        }
    }

    function parseChildren($dirList, $structure = []) {

        // add a new Entry object for each subdirectory
        foreach($dirList as $dir) {
            
            // id from folder name
            $segments = explode('/', $dir);
            $id = end($segments);

            if (substr($id, 0, 1) !== '_') {
				
                // create new entry
                $this->children[$id] = new Entry($id, $dir, $structure, $this->uri);

                // register route
                Router::registerRoute($this->children[$id]);
            }            
        }
    }

}
