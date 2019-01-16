<?php

class ApiTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_root_url()
    {
        $this->json('GET', $this->test_api_url . '/');
        $this->assertResponseOk();
        $this->seeJsonStructure(
            [
                'message',
            ]
        );

    }
     public function test_404()
    {
        $this->json('GET', $this->test_api_url . '/no/url');
        $this->seeStatusCode(404);

    }
    public function test_can_create_event()
    {
        $data = [
            'email' => 'admin@brave.com',
            'password' => 'admin',
            'promo_code_expiry' => '2019-1-21 10:00',
            'promo_code' => 'TEST'.rand(100,999),
            'promo_code_radius' => '500',
            'promo_code_value' => '1000',
            'event_name' => 'Test Event Name'.rand(100,999),
            'event_description' => 'an event for kenya sasa',
            'event_date' => '2019-1-20 10:00',
            'lat' => '1.987098079',
            'long' => '8.987098721',
        ];

        $this->json('POST', $this->test_api_url . '/event/create', $data);

        $this->assertResponseOk();
        $this->seeJsonStructure(
            [
                'message',
            ]
        );

        // Check if events has been saved to database
        $this->seeInDatabase('events', [
            'name' => $data['event_name'],
        ]);
        // check if promo code has been added to database
        $this->seeInDatabase('promo_codes', [
            'code' => $data['promo_code'],
        ]);
    }
    public function test_can_deactivate_promo_code()
    {
        $data = [
            'email' => 'admin@brave.com',
            'password' => 'admin',
            'promo_code_id' => 1,
        ];
        $this->json('POST', $this->test_api_url . '/promocode/deactivate', $data);

        $this->assertResponseOk();
        $this->seeJsonStructure(
            [
                'message',
            ]
        );
    }
    public function test_can_update_promo_code_radius()
    {
        $data = [
            'email' => 'admin@brave.com',
            'password' => 'admin',
            'promo_code_id' => '2',
            'promo_code_radius' => '200',
        ];
        $this->json('POST', $this->test_api_url . '/promocode/update/radius', $data);

        $this->assertResponseOk();
        $this->seeJsonStructure(
            [
                'message',
            ]
        );
    }

    public function test_can_get_all_promo_codes()
    {
        $this->json('GET', $this->test_api_url . '/promocode/all');

        $this->assertResponseOk();
        $this->seeJsonStructure(
            [
                'message',
                'data',
            ]
        );
    }

    public function test_can_get_active_promo_codes()
    {
        $this->json('GET', $this->test_api_url . '/promocode/active');

        $this->assertResponseOk();
        $this->seeJsonStructure(
            [
                'message',
                'data',
            ]
        );
    }
}
