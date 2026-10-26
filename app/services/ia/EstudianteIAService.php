<?php

declare(strict_types=1);

class EstudianteIAService
{
    private OpenAIService $openAI;

    public function __construct()
    {
        $this->openAI = new OpenAIService();
    }

    /**
     * Analiza automáticamente los errores de una práctica o simulacro y
     * genera un pequeño plan de refuerzo. No guarda ni modifica datos.
     */
    public function generarPlanRefuerzo(
        int $estudianteId,
        int $grado,
        string $materia,
        string $origen,
        array $errores
    ): array {
        $erroresNormalizados = [];

        foreach ($errores as $error) {
            if (!is_array($error)) {
                continue;
            }

            $erroresNormalizados[] = [
                'pregunta_id' => (int) ($error['pregunta_id'] ?? 0),
                'tema' => trim((string) ($error['tema'] ?? 'No disponible')),
                'dificultad' => trim((string) ($error['dificultad'] ?? 'No disponible')),
                'enunciado' => mb_substr(trim((string) ($error['enunciado'] ?? '')), 0, 900),
                'respuesta_estudiante' => trim((string) ($error['respuesta_estudiante'] ?? 'Sin respuesta')),
                'explicacion' => mb_substr(trim((string) ($error['explicacion'] ?? '')), 0, 700),
            ];
        }

        if ($erroresNormalizados === []) {
            return [
                'hay_errores' => false,
                'resumen' => 'No se detectaron errores en este resultado.',
                'nivel_prioridad' => 'baja',
                'fortalezas' => ['Buen desempeño en esta actividad.'],
                'debilidades' => [],
                'temas_prioritarios' => [],
                'plan' => [],
                'mensaje_motivador' => 'Excelente trabajo. Mantén la práctica para consolidar lo aprendido.',
            ];
        }

        $contexto = [
            'estudiante_id' => $estudianteId,
            'grado' => $grado > 0 ? $grado : null,
            'materia' => $materia !== '' ? $materia : 'No disponible',
            'origen' => $origen,
            'cantidad_errores' => count($erroresNormalizados),
            'errores' => $erroresNormalizados,
        ];

        $systemPrompt = PromptBuilder::planRefuerzoEstudiante();

        $userPrompt = "ANÁLISIS OFICIAL DEL BACKEND\n" .
            json_encode($contexto, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        $schema = [
            'type' => 'object',
            'additionalProperties' => false,
            'properties' => [
                'hay_errores' => ['type' => 'boolean'],
                'resumen' => ['type' => 'string'],
                'nivel_prioridad' => [
                    'type' => 'string',
                    'enum' => ['baja', 'media', 'alta'],
                ],
                'fortalezas' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                ],
                'debilidades' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                ],
                'temas_prioritarios' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'additionalProperties' => false,
                        'properties' => [
                            'tema' => ['type' => 'string'],
                            'cantidad_errores' => ['type' => 'integer'],
                            'prioridad' => [
                                'type' => 'string',
                                'enum' => ['alta', 'media', 'baja'],
                            ],
                            'razon' => ['type' => 'string'],
                            'accion' => ['type' => 'string'],
                        ],
                        'required' => ['tema', 'cantidad_errores', 'prioridad', 'razon', 'accion'],
                    ],
                ],
                'plan' => [
                    'type' => 'array',
                    'minItems' => 3,
                    'maxItems' => 5,
                    'items' => [
                        'type' => 'object',
                        'additionalProperties' => false,
                        'properties' => [
                            'dia' => ['type' => 'integer'],
                            'objetivo' => ['type' => 'string'],
                            'actividad' => ['type' => 'string'],
                            'cantidad' => ['type' => 'integer'],
                            'recomendacion' => ['type' => 'string'],
                        ],
                        'required' => ['dia', 'objetivo', 'actividad', 'cantidad', 'recomendacion'],
                    ],
                ],
                'mensaje_motivador' => ['type' => 'string'],
            ],
            'required' => [
                'hay_errores',
                'resumen',
                'nivel_prioridad',
                'fortalezas',
                'debilidades',
                'temas_prioritarios',
                'plan',
                'mensaje_motivador',
            ],
        ];

        $plan = $this->openAI->generarJSON(
            $systemPrompt,
            $userPrompt,
            $schema,
            'progressicfes_plan_refuerzo'
        );

        return $this->validarPlan($plan, count($erroresNormalizados));
    }

    private function validarPlan(array $plan, int $cantidadErrores): array
    {
        $plan['hay_errores'] = (bool) ($plan['hay_errores'] ?? true);
        $plan['resumen'] = trim((string) ($plan['resumen'] ?? ''));
        $plan['nivel_prioridad'] = in_array(
            $plan['nivel_prioridad'] ?? '',
            ['baja', 'media', 'alta'],
            true
        ) ? $plan['nivel_prioridad'] : 'media';

        $plan['fortalezas'] = is_array($plan['fortalezas'] ?? null)
            ? array_values(array_map('strval', $plan['fortalezas']))
            : [];

        $plan['debilidades'] = is_array($plan['debilidades'] ?? null)
            ? array_values(array_map('strval', $plan['debilidades']))
            : [];

        $plan['temas_prioritarios'] = is_array($plan['temas_prioritarios'] ?? null)
            ? $plan['temas_prioritarios']
            : [];

        $plan['plan'] = is_array($plan['plan'] ?? null)
            ? array_slice($plan['plan'], 0, 5)
            : [];

        if (count($plan['plan']) < 3) {
            throw new Exception('ProgressIA no generó un plan de refuerzo completo. Inténtalo nuevamente.');
        }

        foreach ($plan['temas_prioritarios'] as &$tema) {
            if (!is_array($tema)) {
                $tema = [];
            }
            $tema['tema'] = trim((string) ($tema['tema'] ?? 'Tema prioritario'));
            $tema['cantidad_errores'] = max(0, (int) ($tema['cantidad_errores'] ?? 0));
            $tema['prioridad'] = in_array($tema['prioridad'] ?? '', ['alta', 'media', 'baja'], true)
                ? $tema['prioridad']
                : 'media';
            $tema['razon'] = trim((string) ($tema['razon'] ?? ''));
            $tema['accion'] = trim((string) ($tema['accion'] ?? ''));
        }
        unset($tema);

        foreach ($plan['plan'] as &$dia) {
            if (!is_array($dia)) {
                $dia = [];
            }
            $dia['dia'] = max(1, (int) ($dia['dia'] ?? 1));
            $dia['objetivo'] = trim((string) ($dia['objetivo'] ?? ''));
            $dia['actividad'] = trim((string) ($dia['actividad'] ?? ''));
            $dia['cantidad'] = max(1, (int) ($dia['cantidad'] ?? 5));
            $dia['recomendacion'] = trim((string) ($dia['recomendacion'] ?? ''));
        }
        unset($dia);

        $plan['mensaje_motivador'] = trim((string) ($plan['mensaje_motivador'] ?? ''));

        return $plan;
    }
}
