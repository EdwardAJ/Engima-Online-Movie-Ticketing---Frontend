<?php
class View
{
    public function __construct()
    {
        $this->render();
    }

    public function __destruct()
    {
    }

    protected function render_navbar()
    {
        $navbar_path = root().'/html/navbar.html';
        if (file_exists($navbar_path)) {
            return file_get_contents($navbar_path);
        }

        return 'HTML for navbar is not found!';
    }

    public function render()
    {
        $view_html_filepath = root().'/html/'.substr(get_class($this), 0, -4).'.html';
        $view_html_filepath = strtolower($view_html_filepath);
        if (file_exists($view_html_filepath)) {
            $view_html = file_get_contents($view_html_filepath);
            $view_html = str_replace('<!-- NAVBAR -->', $this->render_navbar(), $view_html);
            echo $view_html;
        } else {
            echo 'HTML for this view is not found!';
        }
    }
}
