<?php

namespace baseapi\base;

use yii\web\Controller as YiiWebController;
use baseapi\helpers\BaseApiHelper as Helper;
use BaseApi;

class Controller extends YiiWebController {

    private $twig;

    private $data = [];

    /**
     * Checks to see if this has been set to app to use templates from the templates folder at the root of the project.
     * If not, it uses the base templates under the src director.
     *
     * @var string
     *
     */
    protected $template_dir = "base";

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);

        $baseapi_path = BaseApi::getAlias("@baseapi");
        $this->data['site_url'] = BaseApi::getAlias("@site_url") . "/";

        if (getenv("ENVIRONMENT") == "dev") {
            $this->data['devmode'] = true;
        } else {
            $this->data['devmode'] = false;
        }

        if (Helper::getUser() == "") {
            $this->data['loggedIn'] = false;
        } else {
            $this->data['loggedIn'] = true;
        }

        $this->data['site_name'] = getenv("SITE_NAME");

        if ($this->template_dir == "app") {
            $loader = new \Twig\Loader\FilesystemLoader(APP_TEMPLATES);
        } else {
            $loader = new \Twig\Loader\FilesystemLoader($baseapi_path . '/templates');
        }

        $this->twig = new \Twig\Environment($loader, [
            'cache' => false
        ]);
    }

    /**
     * @param string $template
     * @param array $data
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function renderTemplate($template="", $data=[])
    {
        $data = array_merge($this->data, $data);

        return $this->twig->render($template, $data);
    }

}