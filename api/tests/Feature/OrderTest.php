<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGetOrderListingRequest()
    {
        $response = $this->get('/api/orders?page=1');

        $response->assertStatus(200);
    }

    public function testGetOrderLookupRequest()
    {
        $response = $this->get('/api/orders/1');

        $response->assertStatus(200);
        $response->assertJson(['data'=> ['id' => 1, 'client_id' => 1,]]);
    }
}
