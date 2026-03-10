<?php

namespace Hostnet\Component\AccessorGenerator\Attribute;

#[Attribute]
class Generate extends \Hostnet\Component\AccessorGenerator\Annotation\Generate
{
    public static function fromString(string $value): self|null
    {
        if (false === strstr($value, '\Generate(')) {
            return null;
        }

        $after_start_bracket = \explode('(', $value, 2)[1];
        $before_end_bracket = \explode(')', $after_start_bracket, 2)[0];

        $properties = \explode(',', $before_end_bracket);

        $attribute = new self();
        foreach ($properties as $property) {
            $parts = explode(':', $property);
            $name = trim($parts[0]);
            $value = trim($parts[1]);


            $attribute->$name = $value;
        }

        return $attribute;
    }
}