<?php

if (!defined('ABSPATH')) {
    exit;
}

class Fastiko_SEO_AI_Gemini_Provider implements Fastiko_SEO_AI_Provider_Interface
{
    private string $api_key;

    public function __construct()
    {
        $this->api_key = (string) get_option('fastiko_gemini_key', '');
    }

    public function get_name(): string
    {
        return 'gemini';
    }

    public function generate(array $payload): array
    {
        if (empty($this->api_key)) {
            throw new Exception('Gemini API key is missing');
        }

        $prompt = $this->build_prompt($payload);

        $response = wp_remote_post(
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro:generateContent?key=' . $this->api_key,
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode([
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ]
                ]),
                'timeout' => 30
            ]
        );

        if (is_wp_error($response)) {
            throw new Exception($response->get_error_message());
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);

        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

        return json_decode($text, true) ?: [];
    }

    private function build_prompt(array $payload): string
    {
        return "Return ONLY JSON SEO data:\n" . json_encode($payload);
    }
}