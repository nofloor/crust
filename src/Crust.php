<?php

namespace Crust;

use Crust\Factory\Objects\Meta;
use Crust\Factory\Objects\Page;
use Crust\Factory\Objects\PostType;
use Crust\Factory\Objects\Taxonomy;
use Crust\Factory\Objects\Theme;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class Crust {

    const THEME_NAME = 'Crust';
    const WP_DIR = '.';
    const SETTINGS_DIR = './.crust';
    const TEMP_DIR = './.crust/tmp';

    public $fs;
    public $output;
    public $input;
    public $renderer;
    public $theme;

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->fs = new Filesystem();
        $this->input = $input;
        $this->output = $output;
        $this->setOutputFormatterStyles();
        $this->setRenderEngine();
    }

    static $extensions = array(
        'stylus' => 'styl',
        'sass' => 'scss'
    );

    private function setOutputFormatterStyles()
    {
        $style = new OutputFormatterStyle('blue', null, ['bold']);
        $this->output->getFormatter()->setStyle('title', $style);

        $style = new OutputFormatterStyle('green');
        $this->output->getFormatter()->setStyle('success', $style);

        $style = new OutputFormatterStyle('red');
        $this->output->getFormatter()->setStyle('error', $style);
    }

    private function setRenderEngine()
    {
        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/Factory/Templates');
        $this->renderer = new \Twig_Environment($loader);
        $var = new \Twig_Filter('var', function ($value) {
            if (is_bool($value)) {
                return $value ? 'true' : 'false';
            }
            elseif (is_string($value)) {
                return "'$value'";
            }
            elseif (is_array($value)) {
                return "['". implode("', '", $value) . "']";
            }
            return $value;
        });
        $this->renderer->addFilter($var);
    }

    public function theme($name = null, $settings = [])
    {
        $name = $name ? $name : self::THEME_NAME;
        $this->theme = new Theme($this, $name, $settings);
        return $this->theme;
    }

    public function taxonomy($name, $options = [])
    {
        $taxonomy = new Taxonomy($name, $options);
        return $taxonomy;
    }

    public function postType($name, $settings = [])
    {
        $postType = new PostType($name, $settings);
        return $postType;
    }

    public function meta($name, $settings = [])
    {
        $meta = new Meta($name, $settings);
        return $meta;
    }

    public function page($name, $settings = [])
    {
        $page = new Page($name, $settings);
        return $page;
    }

    public function progressBar()
    {
        $progressBar = new ProgressBar($this->output, 100);
        $progressBar->setFormat('[%bar%] %percent%% %message%');
        $progressBar->setBarCharacter('■');
        $progressBar->setEmptyBarCharacter('⁃');
        $progressBar->setProgressCharacter('■');
        $progressBar->setBarWidth(50);
        return $progressBar;
    }
}