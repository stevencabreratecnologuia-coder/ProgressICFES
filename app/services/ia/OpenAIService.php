<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/ConfigIA.php';

class OpenAIService
{
    private string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->apiKey = ConfigIA::apiKey();
        $this->model = ConfigIA::model();

        if (trim($this->apiKey) === '') {
            throw new Exception('La API Key de OpenAI no está configurada en .env.');
        }

        if (trim($this->model) === '') {
            throw new Exception('El modelo de OpenAI no está configurado.');
        }
    }

    public function preguntar(string $mensaje): string
    {
        return $this->preguntarConHistorial([
            ['role' => 'system', 'content' => PromptBuilder::estudiante()],
            ['role' => 'user', 'content' => $mensaje],
        ]);
    }

    public function preguntarConHistorial(array $historial): string
    {
        return $this->request([
            'model' => $this->model,
            'input' => $this->normalizarHistorial($historial),
        ]);
    }

    /**
     * Genera una respuesta JSON validada por Structured Outputs.
     * OpenAI documenta Structured Outputs para forzar el cumplimiento de un JSON Schema.
     */
    public function generarJSON(string $systemPrompt, string $userPrompt, array $schema, string $schemaName = 'progressicfes_resultado'): array
    {
        $payload = [
            'model' => $this->model,
            'input' => [
                [
                    'role' => 'system',
                    'content' => $systemPrompt,
                ],
                [
                    'role' => 'user',
                    'content' => $userPrompt,
                ],
            ],
            'text' => [
                'format' => [
                    'type' => 'json_schema',
                    'name' => $schemaName,
                    'strict' => true,
                    'schema' => $schema,
                ],
            ],
        ];

        $promptId = ConfigIA::promptId();
        if ($promptId !== '') {
            $prompt = ['id' => $promptId];
            $version = ConfigIA::promptVersion();
            if ($version !== '') {
                $prompt['version'] = $version;
            }
            $payload['prompt'] = $prompt;
        }

        $texto = $this->request($payload);
        $resultado = json_decode($texto, true);

        if (!is_array($resultado)) {
            throw new Exception('OpenAI devolvió un JSON inválido.');
        }

        return $resultado;
    }

    private function normalizarHistorial(array $historial): array
    {
        $input = [];

        foreach ($historial as $mensaje) {
            $role = (string) ($mensaje['role'] ?? 'user');
            $content = trim((string) ($mensaje['content'] ?? ''));

            if ($content === '') {
                continue;
            }

            if (!in_array($role, ['system', 'developer', 'user', 'assistant'], true)) {
                $role = 'user';
            }

            $input[] = [
                'role' => $role,
                'content' => $content,
            ];
        }

        if ($input === []) {
            throw new Exception('No hay mensajes para enviar a OpenAI.');
        }

        return $input;
    }

    private function request(array $payload): string
    {
        $jsonPayload = json_encode(
            $payload,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
        );

        $ch = curl_init('https://api.openai.com/v1/responses');

        if ($ch === false) {
            throw new Exception('No fue posible iniciar cURL.');
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_TIMEOUT => ConfigIA::TIMEOUT,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->apiKey,
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            CURLOPT_POSTFIELDS => $jsonPayload,
        ]);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response === false) {
            $error = curl_error($ch);
            $errorNumber = curl_errno($ch);
            curl_close($ch);
            throw new Exception('Error cURL [' . $errorNumber . ']: ' . $error);
        }

        curl_close($ch);

        $json = json_decode($response, true);

        if (!is_array($json)) {
            throw new Exception('OpenAI devolvió una respuesta JSON inválida.');
        }

        if ($httpCode >= 400) {
            $message = $json['error']['message'] ?? 'Error desconocido de OpenAI.';
            throw new Exception('OpenAI HTTP ' . $httpCode . ': ' . $message);
        }

        if (isset($json['output_text']) && is_string($json['output_text'])) {
            $texto = trim($json['output_text']);
            if ($texto !== '') {
                return $texto;
            }
        }

        if (isset($json['output']) && is_array($json['output'])) {
            foreach ($json['output'] as $output) {
                if (!isset($output['content']) || !is_array($output['content'])) {
                    continue;
                }

                foreach ($output['content'] as $content) {
                    if (
                        isset($content['type'], $content['text'])
                        && $content['type'] === 'output_text'
                        && is_string($content['text'])
                    ) {
                        $texto = trim($content['text']);
                        if ($texto !== '') {
                            return $texto;
                        }
                    }
                }
            }
        }

        throw new Exception('OpenAI no devolvió texto en la respuesta.');
    }
}
