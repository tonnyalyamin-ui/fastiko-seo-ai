<?php

if (!defined('ABSPATH')) {
    exit;
}

class Fastiko_SEO_AI_OpenAI_Provider
implements Fastiko_SEO_AI_Provider_Interface
{
    private string $api_key;

    public function __construct()
    {
        $this->api_key = (string) get_option(
            'fastiko_openai_key',
            ''
        );
    }

    public function get_name(): string
    {
        return 'openai';
    }

    public function generate(array $payload): array
    {
        if (empty($this->api_key)) {
            throw new Exception(
                'OpenAI API key missing.'
            );
        }

        return $this->request($payload);
    }

    private function request(array $payload): array
    {
        // Реальный запрос добавим после создания
        // AI Generator класса

        return [];
    }
}