<?php

namespace Han\Router;

/**
 * Class Routepart
 * @package Han\Router
 */
class Routepart
{
    /**
     * @var
     */
    private $part;
    /**
     * @var
     */
    private $isWildcard;

    /**
     * @return mixed
     */
    public function getIsWildcard()
    {
        return $this->isWildcard;
    }

    /**
     * @param mixed $isWildcard
     */
    public function setIsWildcard($isWildcard)
    {
        $this->isWildcard = $isWildcard;
    }

    /**
     * @return mixed
     */
    public function getPart()
    {
        return $this->part;
    }

    /**
     * @param mixed $part
     */
    public function setPart($part)
    {
        $part = ltrim($part, '{');
        $part = rtrim($part, '}');
        $this->part = $part;
    }
}