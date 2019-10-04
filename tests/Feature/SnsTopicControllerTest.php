<?php

namespace Tests\Feature;

use App\Http\Controllers\SnsTopicController;
use App\User as User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SnsTopicControllerTest extends TestCase
{
    public function test_if_aws_credentials_are_set()
    {
        $this->assertNotEmpty(env('AWS_ACCESS_KEY_ID'), 'AWS_ACCESS_KEY_ID is not set');
        $this->assertNotEmpty(env('AWS_SECRET_ACCESS_KEY'), 'AWS_SECRET_ACCESS_KEY is not set');
        $this->assertNotEmpty(env('AWS_REGION'), 'AWS_REGION is not set');
        $this->assertNotEmpty(env('AWS_USER_CODE'), 'AWS_USER_CODE is not set');
    }

    public function test_topic_create_subscribe_delete()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $snsClient = new SnsTopicController("testTopic");

        $this->assertTrue(
            $snsClient->create_user_topic(),
            "Could not create topic"
        );

        $this->assertTrue(
            $snsClient->subscribe_to_user_topic('https://phpunit.test.subscription.url'),
            "Could not subscribe to topic"
        );

        $this->assertTrue(
            $snsClient->publish_message("This is test message"),
            "Could not publish message"
        );

        $this->assertTrue(
            $snsClient->delete_user_topic(),
            "Could not delete topic"
        );

    }
}