<?php

if (!defined('ABSPATH')) {
    exit;
}

class Fastiko_SEO_AI_Gemini_Provider
implements Fastiko_SEO_AI_Provider_Interface
{
    private string $api_key;

    public function __construct()
    {
        $this->api_key = (string) get_option(
            'fastiko_gemini_key',
            ''
        );
    }

    public function get_name(): string
    {
        return 'gemini';
    }

    public function generate(array $payload): array
    {
        if (empty($this->api_key)) {
            throw new Exception(
                'Gemini API key missing.'
            );
        }

        return [];
    }
}