<?php

namespace Tests\Unit;

use App\Models\Objects;
use PHPUnit\Framework\TestCase;

class ObjectTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function it_can_fill_and_cast_key_and_value_correctly()
    {
        // Arrange: Create the object
        $object = Objects::create([
            'key' => 'waiting',
            'value' => '{"status": "pending"}',  // JSON string to be stored as a string
        ]);

        // Act: Retrieve the object
        $retrievedObject = Objects::find($object->id);

        // Assert: Ensure the 'key' is stored as a string and the 'value' as a string (JSON)
        $this->assertEquals('waiting', $retrievedObject->key);
        $this->assertEquals('{"status": "pending"}', $retrievedObject->value);  // 'value' is stored as a string
    }
}
