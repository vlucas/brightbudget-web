<?php
namespace Entity;
use Spot;

class Category extends Spot\Entity
{
    protected static $_datasource = 'categories';

    public static function fields()
    {
        return array(
            'id' => array('type' => 'int', 'primary' => true, 'serial' => true),
            'name' => array('type' => 'string', 'required' => true),

            'date_created' => array('type' => 'datetime', 'default' => new \DateTime()),
            'date_modified' => array('type' => 'datetime', 'default' => new \DateTime())
        );
    }
}
