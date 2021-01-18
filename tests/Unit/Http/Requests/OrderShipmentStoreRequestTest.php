<?php

namespace Tests\Unit\Http\Requests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @see \App\Http\Requests\OrderShipmentStoreRequest
 */
class OrderShipmentStoreRequestTest extends TestCase
{
    /** @var \App\Http\Requests\OrderShipmentStoreRequest */
    private $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new \App\Http\Requests\OrderShipmentStoreRequest();
    }

    /**
     * @test
     */
    public function authorize()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $actual = $this->subject->authorize();

        $this->assertTrue($actual);
    }

    /**
     * @test
     */
    public function rules()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $actual = $this->subject->rules();

        $this->assertValidationRules([
            'order_id' => [
                'required',
                'numeric',
                'exists:orders,id',
            ],
            'shipping_number' => [
                'required',
                'string',
            ],
        ], $actual);
    }

    // test cases...
}