<?php

namespace ManipleDoctrine;

class Config
{
    protected $_paths = array();

    protected $_types = array();

    public function addPath($path)
    {
        $this->_paths[] = $path;
        return $this;
    }

    public function getPaths()
    {
        return $this->_paths;
    }

    public function addType($name, $class)
    {
        $this->_types[$name] = $class;
        return $this;
    }

    public function getTypes()
    {
        return $this->_types;
    }
}
