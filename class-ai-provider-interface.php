<?php

if (!defined('ABSPATH')) {
    exit;
}

interface Fastiko_SEO_AI_Provider_Interface
{
    /**
     * Generate SEO data from payload
     *
     * @param array $payload
     * @return array
     */
    public function generate(array $payload): array;

    /**
     * Provider name
     */
    public function get_name(): string;
}