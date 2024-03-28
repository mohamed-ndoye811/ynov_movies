<?php
namespace App\Attribute;

use Doctrine\Common\Annotations\Annotation\Attribute;

#[\Doctrine\Common\Annotations\Annotation\Attribute] #This is the important part to make this class an attribute
class HateoasLink
{
    public string $name;
    public string $method;
    public string $route;

    public function __construct(string $name, string $method, string $route)
    {
        $this->name = $name;
        $this->method = $method;
        $this->route = $route;
    }
}