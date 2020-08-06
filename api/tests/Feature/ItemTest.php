<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ItemTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGetItemListingRequest()
    {
        $response = $this->get('/api/items?page=1');

        $response->assertStatus(200);
    }

    public function testGetItemLookupRequest()
    {
        $response = $this->get('/api/items/1');

        $response->assertStatus(200);
        $response->assertJson(['data'=> ['id' => 1, 'client_id' => 1,]]);
    }
}
