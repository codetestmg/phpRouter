<?php

namespace Han\Router;

/**
 * Class Router
 * @package App\Router
 */
/**
 * Class Router
 * @package Han\Router
 */
class Router
{
    /**
     * @var array
     */
    private $routes;

    /**
     * Router constructor.
     */
    public function __construct()
    {
        $this->routes = [];
    }

    /**
     * @return mixed
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param        $name
     * @param        $path
     * @param        $class
     * @param        $method
     * @param string $type
     */
    public function addRoute($name, $path, $class, $method, $type = 'GET')
    {
        $route = Route::create($name, $path, $class, $method, $type);
        if ($route) {
            $this->routes[] = $route;
        }
    }

    /**
     * @param $currentPath
     * @return bool
     */
    public function route($currentPath)
    {
        $currentPath = trim($currentPath);
        $currentPath = ltrim($currentPath, '/');
        $currentPath = rtrim($currentPath, '/');

        // check for exact matches
        foreach ($this->routes as $route) {
            if (!$this->isRouteMethod($route->getType())) {
                continue;
            }

            if ($currentPath === $route->getPath()) {
                // start routing here....
                echo $this->routeTo($route);
                return true;
            }
        }

        $currentPathParts = explode('/', $currentPath);
        $params = [];
        // if still no hit, check for wildcard routes
        foreach ($this->routes as $route) {
            // loop all routeparts
            $partI = 0;
            $isRoute = true;
            /**
             * @var $route Route
             */

            // check route type
            if (!$this->isRouteMethod($route->getType())) {
                continue;
            }
            if (count($route->getRouteParts()) !== count($currentPathParts)) {
                $isRoute = false;
            } else {
                foreach ($route->getRouteParts() as $routePart) {
                    /**
                     * @var $routePart Routepart
                     */
                    if (!isset($currentPathParts[$partI])) {
                        $isRoute = false;
                        break;
                    } elseif ($routePart->getIsWildcard()) {
                        // the value in the currentPathPart is gonna be the value for the param
                        // the routepart->part is gonna be the
                        $params[$routePart->getPart()] = $currentPathParts[$partI];
                    } elseif (!$routePart->getIsWildcard()) {
                        // if the part is not a wildcard just check to see if it matches... if not just break...
                        if ($routePart->getPart() !== $currentPathParts[$partI]) {
                            $isRoute = false;
                            break;
                        }
                    }

                    $partI++;
                }
            }

            if ($isRoute === true) {
                // if this is the route return it
                echo $this->routeTo($route, $params);
                return true;
            }
        }

        echo '404';
        return false;
    }

    /**
     * @param string $method
     * @return bool
     */
    private function isRouteMethod(string $method): bool
    {
        $method = trim(strtoupper($method));

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['_method']) && $_POST['_method'] === $method)) {
            return true;
        } elseif ($method === $_SERVER['REQUEST_METHOD'] && (!isset($_POST['_method']) || $_POST['_method'] === $method)) {
            return true;
        }
        return false;
    }

    /**
     * @param Route $route
     * @param array $params
     * @return mixed
     * @throws \Exception
     */
    private function routeTo(Route $route, array $params = [])
    {
        $class = $route->getClass();
        $method = $route->getMethod();

        $methodParams = $this->getParamNames($class, $method);

        $sortedParams = $this->sortParams($params, $methodParams);

        $return_value = call_user_func_array([$class, $method], $sortedParams);

        if (gettype($return_value) !== 'string') {
            throw new \Exception("controller {$method} in {$class} did not return a string ");
        }

        Router::clearSessionParams();

        return $return_value;
    }

    /**
     * @param $class
     * @param $method
     * @return array
     */
    private function getParamNames($class, $method)
    {
        $params = [];
        $reflection = new \ReflectionMethod($class, $method);
        foreach ($reflection->getParameters() as $parameter) {
            $params[] = $parameter->getName();
        }
        return $params;
    }

    /**
     * @param $routeParams
     * @param $methodParams
     * @return array
     */
    private function sortParams($routeParams, $methodParams)
    {

        $sorted = [];
        foreach ($methodParams as $methodParam) {
            foreach ($routeParams as $routeParamName => $routeParamValue) {
                if ($methodParam === $routeParamName) {
                    $sorted[] = $routeParamValue;
                }
            }
        }
        return $sorted;
    }

    /**
     * @param $to
     * @param array $params
     * @return bool
     */
    public static function redirect($to, array $params)
    {
        $url = '';
        if (gettype($to === 'string')) {
            $url = $to;
        } elseif (isset($to['name'])) {
            // todo: redirect op name
            // dit is een static, kent de routes dus niet
        }

        // set params to session
        Router::addSessionParams($params);

        // redirect
        $urlFull = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$url";
        header("Location: {$urlFull}");
        return true;
    }

    /**
     * @param array $params
     * @return bool
     */
    public static function addSessionParams(array $params)
    {
        if (isset($_SESSION)) {
            foreach ($params as $key => $value) {
                $_SESSION['hw_router_params'][$key] = $value;
            }
            $_SESSION['hw_router_params']['used'] = 0;
            return true;
        }
        return false;
    }

    /**
     * @return null
     */
    public static function getSessionParams()
    {
        if (isset($_SESSION)) {
            return $_SESSION['hw_router_params'];
        }
        return null;
    }

    /**
     * @return bool|null
     */
    public static function clearSessionParams()
    {
        if (isset($_SESSION) && isset($_SESSION['hw_router_params']['used'])) {
            $used = (int)$_SESSION['hw_router_params']['used'];
            if($used === 1) {
                $_SESSION['hw_router_params'] = [];
                return true;
            } else {
                $_SESSION['hw_router_params']['used'] = 1;
            }
        }
        return null;
    }
}