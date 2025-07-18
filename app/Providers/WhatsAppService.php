<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    protected $apiUrl;
    protected $token;
    protected $phoneId;

    public function __construct()
    {
        $this->apiUrl = env('WHATSAPP_API_URL');
        $this->token = env('WHATSAPP_API_TOKEN');
        $this->phoneId = env('WHATSAPP_PHONE_ID');
    }

    public function sendMessage(string $to, string $message, string $variable1, string $variable2)
    {
		$url = $this->apiUrl . $this->phoneId . '/messages';

        $response = Http::withToken($this->token)->post($url, [
			'messaging_product' => 'whatsapp',
			'to' => $to,
			'type' => 'template',
			'template' => [
				'name' => 'thank_you',
				'language' => [
					'code' => 'en',
				],
				 'components' => [
					[
						'type' => 'body',
						'parameters' => [
							[
								'type' => 'text',
								'text' => $variable1,
							],
							[
								'type' => 'text',
								'text' => $variable2,
							],
						],
					],
				],
			],
		]);

        if ($response->successful()) {
            return $response->json();
        }

        return [
            'error' => $response->json(),
            'status' => $response->status(),
        ];
    }
}
