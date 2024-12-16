<?php
// tests/Unit/ObjectControllerTest.php
namespace Tests\Feature;

use App\Models\Objects;
use Faker\Core\DateTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ObjectControllerTest extends TestCase
{
    protected $md = "";
    protected function setUp(): void
    {
        $this->md = time();
        parent::setUp();
    }

    public function test_it_returns_404_if_object_not_found_with_timestamp()
    {
        $response = $this->getJson('/objects/nonexistentkey');

        $response->assertStatus(404);
    }

    public function test_it_can_save_object_without_timestamp()
    {
        $key = "test_a".$this->md;
        // SAVE
        $data = [
            'JSON' => '{"'.$key.'": "36udVf0sjR"}',
        ];

        $response = $this->postJson('/object', $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('objects', [
            'key' => $key,
            'value' => '36udVf0sjR',
            'timestamp' => null,
        ]);
        // GET
        $response = $this->getJson('/object/' . $key);
        $response->assertStatus(200);
        $response->assertJsonFragment(['result' => '36udVf0sjR']);
    }

    public function test_it_can_save_object_with_json_and_timestamp()
    {
        $key = "test_a".$this->md;
        // SAVE
        $data = [
            'JSON' => '{"'.$key.'": "fEzLHALvTc"}',
            'time' => '2024-12-13 00:00:00'
        ];

        $response = $this->postJson('/object', $data);
        $response->assertStatus(200);
        $this->assertDatabaseHas('objects', [
            'key' => $key,
            'value' => 'fEzLHALvTc',
            'timestamp' => \DateTime::createFromFormat('Y-m-d H:i:s','2024-12-13 00:00:00')->getTimestamp()
        ]);
        // GET
        $key = "test_a".$this->md;
        $response = $this->getJson('/object/' . $key . '?timestamp=2024-12-13 12:00:00');
        $response->assertStatus(200);
        $response->assertJsonFragment(['result' => 'fEzLHALvTc']);

        // SAVE NEW TIME
        $data = [
            'JSON' => '{"'.$key.'": "7gnHmTcnKX"}',
            'time' => '2024-12-14 00:00:00'
        ];

        $response = $this->postJson('/object', $data);
        $response->assertStatus(200);
        $this->assertDatabaseHas('objects', [
            'key' => $key,
            'value' => '7gnHmTcnKX',
            'timestamp' => \DateTime::createFromFormat('Y-m-d H:i:s','2024-12-14 00:00:00')->getTimestamp()
        ]);
        // GET
        $key = "test_a".$this->md;
        $response = $this->getJson('/object/' . $key . '?timestamp=2024-12-14 12:00:00');
        $response->assertStatus(200);
        $response->assertJsonFragment(['result' => '7gnHmTcnKX']);

        // GET OLD TIME
        $key = "test_a".$this->md;
        $response = $this->getJson('/object/' . $key . '?timestamp=2024-12-13 12:00:00');
        $response->assertStatus(200);
        $response->assertJsonFragment(['result' => 'fEzLHALvTc']);
    }

    public function test_it_can_retrieve_data_from_database_correctly()
    {
        $key = "test_c".$this->md;
        // Arrange: Create an object
        $object = Objects::create([
            'key' => $key,
            'value' => '{"status": "pending"}',
        ]);

        // Act: Check if the object exists in the database
        $this->assertDatabaseHas('objects', [
            'key' => $key,
            'value' => '{"status": "pending"}',
        ]);

    }

    public function test_it_can_get_all_records()
    {
        $keya = "test_a".$this->md;
        $data = [
            'JSON' => '{"'.$keya.'": "LOoZGNRR0W"}',
            'time' => '2024-12-11 00:00:00'
        ];
        $response = $this->postJson('/object', $data);
        $keyb = "test_b".$this->md;
        $data = [
            'JSON' => '{"'.$keyb.'": "djgBd2ZbYf"}',
            'time' => '2024-12-12 00:00:00'
        ];
        $response = $this->postJson('/object', $data);
        $keyc = "test_c".$this->md;
        $data = [
            'JSON' => '{"'.$keyc.'": "zSrGArg0JB"}',
            'time' => '2024-12-13 00:00:00'
        ];
        $response = $this->postJson('/object', $data);


        // Act: Send a GET request to fetch all records
        $response = $this->getJson('/object/get_all_records');

        // Assert: Ensure the response contains all the records
        $response->assertStatus(200);
        $response->assertJsonFragment(['key'=>$keya,'value' => 'LOoZGNRR0W']);
        $response->assertJsonFragment(['key'=>$keyb,'value' => 'djgBd2ZbYf']);
        $response->assertJsonFragment(['key'=>$keyc,'value' => 'zSrGArg0JB']);



    }

    protected function tearDown(): void
    {
        Objects::where('key', 'test_a'.$this->md)->delete();
        Objects::where('key', 'test_b'.$this->md)->delete();
        Objects::where('key', 'test_c'.$this->md)->delete();
        parent::tearDown();
    }
}
