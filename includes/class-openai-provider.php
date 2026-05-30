<?php

if (!defined('ABSPATH')) {
    exit;
}

class Fastiko_SEO_AI_OpenAI_Provider implements Fastiko_SEO_AI_Provider_Interface
{
    private string $api_key;

    public function __construct()
    {
        $this->api_key = (string) get_option('fastiko_openai_key', '');
    }

    public function get_name(): string
    {
        return 'openai';
    }

    public function generate(array $payload): array
    {
        if (empty($this->api_key)) {
            throw new Exception('OpenAI API key is missing');
        }

        $response = $this->request($payload);

        return $this->parse($response);
    }

    private function request(array $payload): string
    {
        $prompt = $this->build_prompt($payload);

        $body = [
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are an SEO expert. Return ONLY valid JSON.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.7
        ];

        $response = wp_remote_post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $this->api_key,
            ],
            'body' => json_encode($body),
            'timeout' => 30
        ]);

        if (is_wp_error($response)) {
            throw new Exception($response->get_error_message());
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);

        return $data['choices'][0]['message']['content'] ?? '';
    }

    private function build_prompt(array $payload): string
{
    return json_encode([
        'role' => 'SEO EXPERT + PROGRAMMATIC SEO ENGINE',
        'task' => 'Generate high-ranking city landing page',
        'city' => $payload['city'] ?? '',
        'requirements' => [
            'unique content (no templates)',
            'SEO optimized for Google',
            'use LSI keywords',
            'include FAQ section',
            'include internal linking suggestions',
            'write natural human-like content'
        ],
        'output_format' => [
            'title',
            'meta_description',
            'h1',
            'intro_html',
            'sections_html',
            'faq_array',
            'schema_json'
        ]
    ], JSON_UNESCAPED_UNICODE);
}

    private function parse(string $response): array
    {
        $json = json_decode($response, true);

        if (!$json) {
            return [
                'title' => '',
                'meta_description' => '',
                'h1' => '',
                'faq' => [],
                'schema' => []
            ];
        }

        return $json;
    }
}