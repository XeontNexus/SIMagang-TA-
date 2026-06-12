<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Mail;

class EmailApiTest extends TestCase
{
    /**
     * Test successful email sending via API with valid API Key.
     */
    public function test_send_email_successfully(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/send-email', [
            'to' => 'user@example.com',
            'subject' => 'Test Email',
            'body' => '<h1>Hello from Laravel Mailer!</h1>'
        ], [
            'X-API-KEY' => 'rahasia_api_key_anda'
        ]);

        $response->assertStatus(200)
                 ->assertJson(['status' => 'Email sent successfully']);

        // Assert that mail was sent to the correct recipient
        Mail::assertSent(\App\Mail\GenericEmail::class, function ($mail) {
            return $mail->hasTo('user@example.com');
        });
    }

    /**
     * Test missing recipient email returns 400.
     */
    public function test_send_email_missing_recipient(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/send-email', [
            'subject' => 'Test Email',
            'body' => '<h1>Hello!</h1>'
        ], [
            'X-API-KEY' => 'rahasia_api_key_anda'
        ]);

        $response->assertStatus(400)
                 ->assertJson(['error' => 'Recipient email is required']);

        Mail::assertNothingSent();
    }

    /**
     * Test invalid recipient email format returns 400.
     */
    public function test_send_email_invalid_format(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/send-email', [
            'to' => 'not-an-email',
            'subject' => 'Test Email',
            'body' => '<h1>Hello!</h1>'
        ], [
            'X-API-KEY' => 'rahasia_api_key_anda'
        ]);

        $response->assertStatus(400)
                 ->assertJson(['error' => 'Invalid email address format']);

        Mail::assertNothingSent();
    }

    /**
     * Test request without API Key returns 401.
     */
    public function test_send_email_unauthorized_missing_key(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/send-email', [
            'to' => 'user@example.com',
            'subject' => 'Test Email',
            'body' => '<h1>Hello!</h1>'
        ]); // No X-API-KEY header

        $response->assertStatus(401)
                 ->assertJson(['error' => 'Unauthorized']);

        Mail::assertNothingSent();
    }

    /**
     * Test request with invalid API Key returns 401.
     */
    public function test_send_email_unauthorized_invalid_key(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/send-email', [
            'to' => 'user@example.com',
            'subject' => 'Test Email',
            'body' => '<h1>Hello!</h1>'
        ], [
            'X-API-KEY' => 'wrong_api_key'
        ]);

        $response->assertStatus(401)
                 ->assertJson(['error' => 'Unauthorized']);

        Mail::assertNothingSent();
    }
}
