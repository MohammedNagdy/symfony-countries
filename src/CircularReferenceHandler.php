<?php 

namespace App\Serializer;

class CircularReferenceHandler
{
    public function handle($object)
    {
        return $object->getId(); // Or any other field you want to return when a circular reference is detected
    }
}
