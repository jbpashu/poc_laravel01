<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ClientTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGetClientListingRequest()
    {
        $response = $this->get('/api/clients?page=1');

        $response->assertStatus(200);
        $response->assertJson([
          'meta'=> [
              'current_page' => 1,
              'from' => 1,
              'last_page' => 1,
              'path' => config('app.url') . 'api/clients',
              'per_page' => 15,
              'to' => 3,
              'total' => 3
          ]
        ]);
    }

    public function testGetClientLookupRequest()
    {
        $response = $this->get('/api/clients/1');

        $response->assertStatus(200);
        $response->assertJson(['data'=> ['id' => 1, 'account_id' => 1,]]);
    }
}
