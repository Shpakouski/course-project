<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class CollectionCustomAttribute extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'Collection of attributes should contain no more than "{{ max }}" of type "{{ type }}". It contains "{{ count }}".';

    public int $maxItemPerType;

    public function __construct(mixed $options = null, ?array $groups = null, mixed $payload = null, int $maxItemPerType = 3)
    {
        parent::__construct($options, $groups, $payload);
        $this->maxItemPerType = $maxItemPerType;
    }
}
