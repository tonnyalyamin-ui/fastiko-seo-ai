<?php

if (!defined('ABSPATH')) {
    exit;
}

interface Fastiko_SEO_AI_Provider_Interface
{
    public function generate(array $payload): array;

    public function get_name(): string;
}