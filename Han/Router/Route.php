<?php

namespace Han\Router;

/**
 * Class Route
 * @package Han\Router
 */
class Route
{
    /**
     * @var
     */
    private $name;
    /**
     * @var
     */
    private $path;
    /**
     * @var
     */
    private $routeParts;
    /**
     * @var
     */
    private $class;
    /**
     * @var
     */
    private $method;
    /**
     * @var
     */
    private $type;

    /**
     * @param $name
     * @param $path
     * @param $class
     * @param $method
     * @param $type
     * @return Route
     * @throws \Exception
     */
    public static function create($name, $path, $class, $method, $type)
    {
        if (empty($name) || empty($path) || empty($class) || empty($method) || empty($type)) {
            throw new \Exception("Route {$name} ({$path}) is incomplete");
        }

        if (!class_exists($class)) {
            throw new \Exception("Class {$class} does not exist");
        }

        if (method_exists(new $class, $method)) {
            //throw new \Exception( "Class {$class} has no method {$method}" );
        }

        // todo: if path name or path/type exist throw error

        $route = new Route();
        $route->setName($name);
        $route->setPath($path);
        $route->class = $class;
        $route->method = $method;
        $route->type = trim(strtoupper($type));

        return $route;
    }

    /**
     * @param $path
     * @return $this
     */
    public function setPath($path)
    {
        // strip leading and trailing slashes
        $path = trim($path);
        $path = rtrim($path, '/');
        $path = ltrim($path, '/');
        $this->path = $path;

        // also set the splitpart cause its linked hardwired
        $this->setRouteparts($path);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getRouteParts()
    {
        return $this->routeParts;
    }

    /**
     * @param $path
     * @return $this
     */
    protected function setRouteparts($path)
    {

        $parts = explode('/', $path);

        $routeParts = [];
        foreach ($parts as $part) {
            $pathItem = new Routepart();
            $pathItem->setPart($part);
            $pathItem->setIsWildcard($this->isWildcard($part));
            $routeParts[] = $pathItem;
        }

        $this->routeParts = $routeParts;

        return $this;
    }

    /**
     * @param mixed $name
     *
     * @return Route
     */
    public function setName($name)
    {
        $this->name = trim($name);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @param $string
     * @return bool
     */
    private function isWildcard($string)
    {
        if (strpos($string, '{') !== false && strpos($string, '}') !== false) {
            return true;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }
}