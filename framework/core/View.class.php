<?php

class View {
    protected $data = [];

    public function __construct() {
        // Constructor logic, if any
    }

    // Modify the render function to accept an optional $data parameter
    public function render($template, $data = []) {
        // Merge any additional data passed to this method with the existing data
        $this->data = array_merge($this->data, $data);

        // Check if the template file exists
        $viewFilePath = "Views/$template.html";
        if (file_exists($viewFilePath)) {
            // Extract data to make it accessible in the template
            extract($this->data);
            
            // Include the template file
            include($viewFilePath);
        } else {
            // Handle error if the template file doesn't exist
            echo "Error: Template file '$template' not found.";
        }
    }

    public function setData($key, $value) {
        // Set data to be passed to the view
        $this->data[$key] = $value;
    }

    public function getData($key) {
        // Get data from the view
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }
}


?>