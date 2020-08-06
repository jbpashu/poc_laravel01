<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SupplierTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGetSupplierListingRequest()
    {
        $response = $this->get('/api/suppliers?page=1');

        $response->assertStatus(200);
    }

    public function testGetSupplierLookupRequest()
    {
        $response = $this->get('/api/suppliers/1');

        $response->assertStatus(200);
        $response->assertJson(['data'=> ['id' => 1, 'client_id' => 1,]]);
    }
}
