<?php

namespace Cruster\Factory\Objects;

use Doctrine\Common\Inflector\Inflector;
use Stringy\StaticStringy as Stringy;

class PostType
{

    public $id;
    public $name;
    public $slug;
    public $fileName;
    public $theme;

    protected $metas = [];
    protected $taxonomies = [];
    protected $settings;
    protected $labels;

    function __construct($name, $settings)
    {
        $this->id = Stringy::slugify($name, '_');
        $this->name = Stringy::toTitleCase($name);
        $this->slug = Stringy::slugify($this->name);

        $this->fileName = 'post-type-' . $this->slug . '.php';

        $this->initSettings($settings);
    }

    public function supports($supports)
    {
        $current = $this->settings['supports'];

        if (!is_array($supports)) {
            $supports = [$supports];
        }

        $this->settings['supports'] = array_merge($current, $supports);
        return $this;
    }

    public function settings()
    {
        return $this->settings;
    }

    public function labels()
    {
        return $this->labels;
    }

    public function metas()
    {
        return $this->metas;
    }

    public function addMeta(Meta $meta)
    {
        $this->metas[] = $meta;
        return $this;
    }

    public function hasMeta()
    {
        return count($this->metas) > 0;
    }

    public function hasMetaInColumns()
    {
        foreach ($this->metas as $meta) {
            if ($meta->hasColumn()) return true;
        }
        return false;
    }

    public function taxonomies()
    {
        return $this->taxonomies;
    }

    public function addTaxonomy(Taxonomy $taxonomy)
    {
        $this->taxonomies[$taxonomy->id] = $taxonomy;
        $taxonomy->addPostType($this);
        return $this;
    }

    private function initSettings($settings)
    {
        $defaultSettings = [
            'menu_position' => 5,
            'supports' => array('title', 'editor', 'thumbnail'),
            'has_archive' => true,
            'public' => true,
            'rewrite' => array('slug' => $this->slug, 'with_front' => false),
            'capability_type' => 'post'
        ];

        $this->settings = array_merge($defaultSettings, $settings);
        $this->generateLabels();
    }

    private function generateLabels()
    {
        $pluralName = Inflector::pluralize($this->name);

        $this->labels = [
            'name' => $pluralName,
            'singular_name' => $this->name,
            'add_new' => 'Add New',
            'add_new_item' => 'Add New ' . $this->name,
            'edit_item' => 'Edit ' . $this->name,
            'new_item' => 'New ' . $this->name,
            'view_item' => 'View ' . $this->name,
            'view_items' => 'View ' . $pluralName,
            'search_items' => 'Search ' . $pluralName,
            'not_found' => 'No ' . $pluralName . ' found',
            'not_found_in_trash' => 'No ' . $pluralName . ' found in Trash',
            'parent_item_colon' => 'Parent ' . $this->name . ':',
            'all_items' => 'All ' . $pluralName,
            'archives' => $this->name . ' Archives',
            'attributes' => $this->name . ' Attributes',
            'insert_into_item' => 'Insert into ' . $this->name,
            'uploaded_to_this_item' => 'Uploaded to this ' . $this->name,
            'menu_name' => $pluralName
        ];
    }
}