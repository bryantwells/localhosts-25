<?php

use Symfony\Component\Yaml\Yaml;
use League\CommonMark\CommonMarkConverter;

$converter = new CommonMarkConverter([
        'html_input' => 'strip',
        'allow_unsafe_links' => false,
    ]);

class File {

    public $path;
    public $dirname;
    public $basename;
    public $extension;
    public $filename;
    public $parsed;
    public $raw;
    public $type;
    public $meta;
    public $url;

    private $converter;

    function __construct($filePath) {
        $this->converter = new CommonMarkConverter([
            'html_input' => 'allow',
            'allow_unsafe_links' => false,
        ]);
        $this->init($filePath);
    }

    function init($filePath) {

        // meta
        $this->path = $filePath;
        $this->dirname = pathinfo($filePath)['dirname'];
        $this->basename = pathinfo($filePath)['basename'];
        $this->extension = strtolower(pathinfo($filePath)['extension']);
        $this->filename = pathinfo($filePath)['filename'];
        
        if ($this->extension == 'md') {

            // markdown
            $contents = file_get_contents($filePath);
            $html = $this->converter->convert($contents)->getContent();
            $this->parsed = $html;
            $this->raw = $contents;

        } else if ($this->extension == 'txt') {

            // text file
            $contents = file_get_contents($filePath);
            $this->parsed = $contents;
            $this->raw = $contents;
        } else if ($this->extension == 'url') {

            // text file
            $contents = file_get_contents($filePath);
            $this->raw = $contents;
            $this->url = substr($contents, strpos($contents, "=") + 1);
        }

        // set file type
        if (in_array($this->extension, ['txt','md'])) {
            $this->type = 'text';
        } else if (in_array($this->extension, ['jpg','jpeg','png','webp','gif'])) {
            $this->type = 'image';
        } else if (in_array($this->extension, ['mov', 'mp4'])) {
            $this->type = 'video';
        } else {
            $this->type = $this->extension;
        }

        if (file_exists($this->dirname . '/' . $this->filename . '.yml')) {

            // additional meta from yml
            $meta = Yaml::parseFile($this->dirname . '/' . $this->filename . '.yml');
            $this->parseMeta($meta);
        }
    }

    function parseMeta($meta) {
        
        // add each meta item as a property on the collection object
        $this->meta = new stdClass();
        foreach($meta as $metaItemKey => $metaItemValue) {
            $this->meta->$metaItemKey = $metaItemValue;
        }
    }
}