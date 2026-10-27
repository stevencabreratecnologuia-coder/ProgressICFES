
<?php

declare(strict_types=1);

class ConfigIA
{
    public const MAX_MENSAJES_DIA = 20;
    public const MAX_TOKENS_DIA = 15000;
    public const TIMEOUT = 30;

    public static function apiKey(): string
    {
        return getenv('OPENAI_API_KEY') ?: '';
    }

    public static function model(): string
    {
        return getenv('OPENAI_MODEL') ?: 'gpt-5';
    }

    public static function promptId(): ?string
    {
        $value = getenv('OPENAI_PROMPT_ID');

        return $value === false || trim($value) === ''
            ? null
            : trim($value);
    }

    public static function promptVersion(): ?string
    {
        $value = getenv('OPENAI_PROMPT_VERSION');

        return $value === false || trim($value) === ''
            ? null
            : trim($value);
    }
}
