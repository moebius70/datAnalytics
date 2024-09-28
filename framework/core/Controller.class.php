<?php

class Controller {

    protected function __construct() {
        // Initialization logic can be added here if needed
    }

    protected function render($view, $data = []) {
        // $viewPath = "..\\".__DIR__. "\Views\\$view.html";
        // $viewPath = '../../Views/'.$view.'.html';
        $viewPath = "C:\Users\User\Documents\Code\PHP\\tradingtoolstd\application\Views\\$view.html";

        if (file_exists($viewPath)) {
            extract($data);
            include $viewPath;
        } else {
            // Handle the error, e.g., log it or show a default error view
            $this->renderError("View '$viewPath' not found.");
        }
    }

    protected function renderError($message) {
        // Define a simple error view or handle it as needed
        echo "<h1>Error</h1><p>$message</p>";
    }
}

?>
