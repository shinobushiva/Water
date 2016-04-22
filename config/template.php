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
    /**
     * コンストラクタ
     * @param Twig_SimpleFunction[] $functions
     */
    public function __construct(array $functions = [])
    {
        $this->template = new Twig_Environment(
            new Twig_Loader_Filesystem(VIEW_DIR),
            array('autoescape' => true)
        );

        foreach($functions as $func) {
            $this->template->addFunction($func);
        }
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
            self::$instance = new TwigTemplate([
                new Twig_SimpleFunction('csrfToken', 'csrfToken')
            ]);
        }
        return self::$instance;
    }
}
