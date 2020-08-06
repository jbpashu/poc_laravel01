<?php

namespace Tests\Feature\SmokeTest;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    /**
     * A basic request test.
     *
     * @return void
     */
    public function testBasicClientRequestTest()
    {
        $response = $this->get('/api/clients');
        $response->assertStatus(200);
    }

    /**
     * A basic request test.
     *
     * @return void
     */
    public function testBasicItemRequestTest()
    {
        $response = $this->get('/api/items');
        $response->assertStatus(200);
    }

    /**
     * A basic request test.
     *
     * @return void
     */
    public function testBasicSupplierRequestTest()
    {
        $response = $this->get('/api/suppliers');
        $response->assertStatus(200);
    }

    /**
     * A basic request test.
     *
     * @return void
     */
    public function testBasicOrderRequestTest()
    {
        $response = $this->get('/api/orders');
        $response->assertStatus(200);
    }

}
