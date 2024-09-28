<?php

class Router {
    protected $routes = [];

    public function map($url, $controllerAction) {
        $this->routes[$url] = $controllerAction;
    }

    public function dispatch($url) {
        try {
            $url = trim($url);
            $url = str_ireplace(BASE_URL, "", $url);
            $urlParts = parse_url($url);
            $path = $urlParts['path'];
            parse_str($urlParts['query'] ?? '', $queryParams);

            if (array_key_exists($path, $this->routes)) {
                $controllerAction = $this->routes[$path];
                list($controller, $action) = explode('@', $controllerAction);

                if (!class_exists($controller)) {
                    throw new ControllerNotFoundException("Controller $controller not found.");
                }

                $controllerInstance = new $controller();

                if (!method_exists($controllerInstance, $action)) {
                    throw new ActionNotFoundException("Action $action not found in controller $controller.");
                }

                $reflector = new ReflectionMethod($controllerInstance, $action);

                // Get parameters in correct order
                $parameters = [];
                foreach ($reflector->getParameters() as $param) {
                    $paramName = $param->getName();
                    $parameters[] = $queryParams[$paramName] ?? $param->getDefaultValue();
                }
                call_user_func_array([$controllerInstance, $action], $parameters);
            } else {
                throw new PageNotFoundException("Page not found for URI: $path");
            }
        } catch (PageNotFoundException $e) {
            $this->show404Error($e->getMessage());
        } catch (ControllerNotFoundException $e) {
            $this->show404Error($e->getMessage());
        } catch (ActionNotFoundException $e) {
            $this->show404Error($e->getMessage());
        } catch (Exception $e) {
            $this->show500Error("An unexpected error occurred: " . $e->getMessage());
        }
    }

    protected function show404Error($message) {
        header("HTTP/1.0 404 Not Found");
        call_user_func_array([new NotFound(), 'index'], []);
        // echo "<h1>404 Not Found</h1>";
        echo "<p>$message</p>";
    }

    protected function show500Error($message) {
        header("HTTP/1.0 500 Internal Server Error");
        echo "<h1>500 Internal Server Error</h1>";
        echo "<p>$message</p>";
    }

    protected function notFound() {
        header("HTTP/1.0 404 Not Found");
        call_user_func_array([new NotFound(), 'index'], []);
    }
}

class PageNotFoundException extends Exception {}
class ControllerNotFoundException extends Exception {}
class ActionNotFoundException extends Exception {}

