<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Exception;

class EmailController extends Controller
{
    /**
     * Send an email to target user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendEmail(Request $request): JsonResponse
    {
        // Decode request content (either JSON or standard POST parameters)
        $data = $request->json()->all();
        if (empty($data)) {
            $data = $request->all();
        }

        $to = $data['to'] ?? null;
        $subject = $data['subject'] ?? 'No Subject';
        $body = $data['body'] ?? 'No Content';

        // Check if recipient is provided
        if (!$to) {
            return response()->json(['error' => 'Recipient email is required'], 400);
        }

        // Validate that recipient is a valid email
        $validator = Validator::make(['email' => $to], [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid email address format'], 400);
        }

        try {
            // Send the email using Laravel's Mail facade and GenericEmail Mailable
            Mail::to($to)->send(new \App\Mail\GenericEmail($subject, $body));

            return response()->json(['status' => 'Email sent successfully']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to send email: ' . $e->getMessage()], 500);
        }
    }
}
