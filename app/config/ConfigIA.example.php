<?php

declare(strict_types=1);

class ConfigIA
{
    /**
     * API Key de OpenAI
     */
    public const API_KEY = 'TU_API_KEY_AQUI';

    /**
     * Modelo a utilizar
     */
    public const MODEL = 'gpt-5';

    /**
     * Máximo de mensajes por día
     */
    public const MAX_MENSAJES_DIA = 20;

    /**
     * Máximo de tokens por día
     */
    public const MAX_TOKENS_DIA = 15000;

    /**
     * Tiempo máximo de espera (segundos)
     */
    public const TIMEOUT = 30;
}
