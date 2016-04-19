<?php

/**
 * Class BaseTemplate
 */
abstract class BaseTemplate
{
    protected $template;

    abstract public function render($template, $args);
}


/**
 * Class TwigTemplate
 */
class TwigTemplate extends BaseTemplate
{
    public function __construct()
    {
        $this->template = new Twig_Environment(
            new Twig_Loader_Filesystem(VIEW_DIR),
            array('autoescape' => true)
        );
    }

    public function render($template, $args)
    {
        return $this->template->render($template, $args);
    }
}

/**
 * Class Template
 */
class Template
{
    private static $instance = null;

    /**
     * テンプレートエンジンの取得
     *
     * @return TwigTemplate
     */
    public static function factory()
    {
        if (is_null(self::$instance)) {
            self::$instance = new TwigTemplate();
        }
        return self::$instance;
    }
}
