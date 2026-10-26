<?php

declare(strict_types=1);

class ProfesorIAService
{
    private OpenAIService $openAI;
    private Usuario $usuarios;
    private Materia $materias;
    private Tema $temas;
    private TipoPregunta $tipos;
    private Pregunta $preguntas;
    private Progreso $progreso;

    public function __construct()
    {
        $this->openAI = new OpenAIService();
        $this->usuarios = new Usuario();
        $this->materias = new Materia();
        $this->temas = new Tema();
        $this->tipos = new TipoPregunta();
        $this->preguntas = new Pregunta();
        $this->progreso = new Progreso();
    }

    public function obtenerContextoProfesor(int $profesorId): array
    {
        $usuario = $this->usuarios->buscarPorId($profesorId);

        if ($usuario === null || ($usuario['rol'] ?? '') !== 'profesor') {
            throw new Exception('El usuario no es un profesor válido.');
        }

        $materiaId = (int) ($usuario['materia_id'] ?? 0);
        if ($materiaId <= 0) {
            throw new Exception('El profesor todavía no tiene una materia asignada.');
        }

        $materia = $this->materias->obtenerPorId($materiaId);
        $temas = $this->temas->obtenerPorMateria($materiaId);
        $tipos = $this->tipos->obtenerTodos();
        $estudiantes = $this->progreso->obtenerEstudiantesMateria($materiaId);
        $rendimientoPractica = $this->progreso->obtenerRendimientoMateria($materiaId, 'practica');
        $rendimientoSimulacro = $this->progreso->obtenerRendimientoMateria($materiaId, 'simulacro');
        $rendimientoTemas = $this->progreso->obtenerRendimientoTemas($materiaId);
        $preguntasFalladas = $this->progreso->obtenerPreguntasMasFalladas($materiaId, 10);

        $grados = [];
        foreach ($estudiantes as $estudiante) {
            $grado = (int) ($estudiante['grado'] ?? 0);
            if ($grado < 6 || $grado > 11) {
                continue;
            }

            if (!isset($grados[$grado])) {
                $grados[$grado] = [
                    'grado' => $grado,
                    'estudiantes' => 0,
                    'promedio_practica' => 0,
                ];
            }

            $grados[$grado]['estudiantes']++;
        }

        foreach ($grados as $grado => &$grupo) {
            $valores = [];
            foreach ($estudiantes as $estudiante) {
                if ((int) ($estudiante['grado'] ?? 0) === (int) $grado) {
                    $valores[] = (int) ($estudiante['porcentaje_practica'] ?? 0);
                }
            }

            $grupo['promedio_practica'] = $valores !== []
                ? (int) round(array_sum($valores) / count($valores))
                : 0;
        }
        unset($grupo);
        ksort($grados);

        $preguntasExistentes = $this->preguntas->obtenerTodas($materiaId, $profesorId);
        $muestras = [];
        foreach (array_slice($preguntasExistentes, 0, 20) as $pregunta) {
            $muestras[] = [
                'id' => (int) $pregunta['id'],
                'tema_id' => (int) $pregunta['tema_id'],
                'tema' => (string) ($pregunta['tema_nombre'] ?? ''),
                'dificultad' => (string) ($pregunta['dificultad'] ?? ''),
                'enunciado' => mb_substr((string) $pregunta['enunciado'], 0, 220),
            ];
        }

        return [
            'profesor' => [
                'id' => $profesorId,
                'rol' => 'profesor',
            ],
            'materia' => $materia !== null
                ? [
                    'id' => (int) $materia['id'],
                    'nombre' => (string) $materia['nombre'],
                ]
                : null,
            'temas_autorizados' => array_map(
                static fn(array $tema): array => [
                    'id' => (int) $tema['id'],
                    'nombre' => (string) $tema['nombre'],
                ],
                $temas
            ),
            'tipos_pregunta_autorizados' => array_map(
                static fn(array $tipo): array => [
                    'id' => (int) $tipo['id'],
                    'nombre' => (string) $tipo['nombre'],
                ],
                $tipos
            ),
            'grupos_por_grado' => array_values($grados),
            'rendimiento' => [
                'practica' => $rendimientoPractica,
                'simulacro' => $rendimientoSimulacro,
                'temas' => $rendimientoTemas,
                'preguntas_mas_falladas' => $preguntasFalladas,
            ],
            'muestras_preguntas_existentes' => $muestras,
        ];
    }

    public function generar(int $profesorId, array $opciones): array
    {
        $contexto = $this->obtenerContextoProfesor($profesorId);
        $materiaId = (int) $contexto['materia']['id'];

        $cantidad = max(1, min(20, (int) ($opciones['cantidad'] ?? 10)));
        $grado = (int) ($opciones['grado'] ?? 0);
        $temaId = (int) ($opciones['tema_id'] ?? 0);
        $dificultad = (string) ($opciones['dificultad'] ?? 'auto');
        $modo = (string) ($opciones['modo'] ?? 'simulacro');
        $tipoSimulacro = (string) ($opciones['tipo_simulacro'] ?? 'refuerzo');
        $instruccion = trim((string) ($opciones['instruccion'] ?? ''));

        if ($grado !== 0 && ($grado < 6 || $grado > 11)) {
            throw new Exception('El grado debe estar entre 6 y 11.');
        }

        if ($dificultad !== 'auto' && !in_array($dificultad, ['facil', 'media', 'dificil'], true)) {
            throw new Exception('La dificultad seleccionada no es válida.');
        }

        if (!in_array($modo, ['practica', 'simulacro', 'autonomo'], true)) {
            throw new Exception('El modo de generación no es válido.');
        }

        if (!in_array($tipoSimulacro, ['diagnostico', 'refuerzo', 'icfes', 'recuperacion', 'avanzado'], true)) {
            throw new Exception('El tipo de simulacro no es válido.');
        }

        if ($temaId > 0) {
            $tema = $this->temas->obtenerPorId($temaId);
            if ($tema === null || (int) $tema['materia_id'] !== $materiaId) {
                throw new Exception('El tema seleccionado no pertenece a la materia del profesor.');
            }
        }

        $modoTexto = match ($modo) {
            'practica' => 'Genera un conjunto de preguntas para PRÁCTICA docente. No necesitas crear una simulación publicable.',
            'autonomo' => 'Genera una SIMULACIÓN AUTÓNOMA basada prioritariamente en el rendimiento real recibido. Identifica necesidades, selecciona temas y distribuye la dificultad sin que el profesor tenga que elegir cada pregunta.',
            default => 'Genera una SIMULACIÓN completa para revisión del profesor.',
        };

        $gradoTexto = $grado > 0
            ? 'El contenido está dirigido específicamente al grado ' . $grado . '°.'
            : 'El grado no fue fijado. Usa los grupos por grado del contexto para tomar una decisión conservadora y explícala en el título.';

        $temaTexto = $temaId > 0
            ? 'El tema obligatorio es el tema con ID ' . $temaId . '. No uses otro tema.'
            : 'No hay un tema obligatorio. Si existen datos de rendimiento, prioriza los temas con menor porcentaje y/o mayor cantidad de fallos.';

        $dificultadTexto = $dificultad === 'auto'
            ? 'La dificultad es automática. Distribúyela según el grado y el rendimiento real.'
            : 'La dificultad solicitada es: ' . $dificultad . '.';

        $userPrompt = "GENERA CONTENIDO PARA PROGRESSICFES.\n\n";
        $userPrompt .= "MODO: {$modo}\n";
        $userPrompt .= "{$modoTexto}\n";
        $userPrompt .= "{$gradoTexto}\n";
        $userPrompt .= "{$temaTexto}\n";
        $userPrompt .= "{$dificultadTexto}\n";
        $userPrompt .= "Tipo de simulación, si aplica: {$tipoSimulacro}.\n";
        $userPrompt .= "Cantidad exacta de preguntas: {$cantidad}.\n";
        $userPrompt .= "Tiempo sugerido: calcula un tiempo razonable según la cantidad y dificultad.\n";
        $userPrompt .= "Instrucción adicional del profesor: " . ($instruccion !== '' ? $instruccion : 'ninguna') . "\n\n";
        $userPrompt .= "REGLAS DE BACKEND:\n";
        $userPrompt .= "- Materia autorizada: ID {$materiaId}.\n";
        $userPrompt .= "- Usa únicamente IDs de temas y tipos que aparezcan en el contexto.\n";
        $userPrompt .= "- No inventes resultados ni estudiantes.\n";
        $userPrompt .= "- El resultado será un BORRADOR que el profesor revisará antes de publicar.\n\n";
        $userPrompt .= "CONTEXTO REAL DEL BACKEND:\n";
        $userPrompt .= json_encode($contexto, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);

        $schema = [
            'type' => 'object',
            'additionalProperties' => false,
            'properties' => [
                'titulo' => ['type' => 'string'],
                'tipo_simulacro' => [
                    'type' => 'string',
                    'enum' => ['diagnostico', 'refuerzo', 'icfes', 'recuperacion', 'avanzado'],
                ],
                'tiempo_limite_min' => ['type' => 'integer'],
                'preguntas' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'additionalProperties' => false,
                        'properties' => [
                            'tema_id' => ['type' => 'integer'],
                            'tipo_pregunta_id' => ['type' => 'integer'],
                            'enunciado' => ['type' => 'string'],
                            'opcion_a' => ['type' => 'string'],
                            'opcion_b' => ['type' => 'string'],
                            'opcion_c' => ['type' => 'string'],
                            'opcion_d' => ['type' => 'string'],
                            'respuesta_correcta' => ['type' => 'string', 'enum' => ['a', 'b', 'c', 'd']],
                            'explicacion' => ['type' => 'string'],
                            'dificultad' => ['type' => 'string', 'enum' => ['facil', 'media', 'dificil']],
                            'area' => ['type' => 'string'],
                            'competencia' => ['type' => 'string'],
                        ],
                        'required' => [
                            'tema_id',
                            'tipo_pregunta_id',
                            'enunciado',
                            'opcion_a',
                            'opcion_b',
                            'opcion_c',
                            'opcion_d',
                            'respuesta_correcta',
                            'explicacion',
                            'dificultad',
                            'area',
                            'competencia',
                        ],
                    ],
                ],
            ],
            'required' => ['titulo', 'tipo_simulacro', 'tiempo_limite_min', 'preguntas'],
        ];

        $resultado = $this->openAI->generarJSON(
            PromptBuilder::profesorGenerador(),
            $userPrompt,
            $schema,
            'progressicfes_profesor_generacion'
        );

        $this->validarResultado($resultado, $contexto, $cantidad, $modo);

        $resultado['_meta'] = [
            'estado' => 'DRAFT',
            'modo' => $modo,
            'grado' => $grado,
            'materia_id' => $materiaId,
            'materia_nombre' => (string) ($contexto['materia']['nombre'] ?? ''),
            'profesor_id' => $profesorId,
        ];

        return $resultado;
    }

    private function validarResultado(array $resultado, array $contexto, int $cantidad, string $modo): void
    {
        if (trim((string) ($resultado['titulo'] ?? '')) === '') {
            throw new Exception('La IA no generó un título válido.');
        }

        $preguntas = $resultado['preguntas'] ?? null;
        if (!is_array($preguntas) || count($preguntas) !== $cantidad) {
            throw new Exception('La IA no generó exactamente la cantidad solicitada de preguntas.');
        }

        $temasPermitidos = [];
        foreach ($contexto['temas_autorizados'] as $tema) {
            $temasPermitidos[(int) $tema['id']] = true;
        }

        $tiposPermitidos = [];
        foreach ($contexto['tipos_pregunta_autorizados'] as $tipo) {
            $tiposPermitidos[(int) $tipo['id']] = true;
        }

        foreach ($preguntas as $indice => $pregunta) {
            if (!is_array($pregunta)) {
                throw new Exception('La pregunta ' . ($indice + 1) . ' no tiene una estructura válida.');
            }

            $temaId = (int) ($pregunta['tema_id'] ?? 0);
            $tipoId = (int) ($pregunta['tipo_pregunta_id'] ?? 0);

            if (!isset($temasPermitidos[$temaId])) {
                throw new Exception('La pregunta ' . ($indice + 1) . ' usa un tema no autorizado.');
            }

            if (!isset($tiposPermitidos[$tipoId])) {
                throw new Exception('La pregunta ' . ($indice + 1) . ' usa un tipo de pregunta no autorizado.');
            }

            $opciones = [
                trim((string) ($pregunta['opcion_a'] ?? '')),
                trim((string) ($pregunta['opcion_b'] ?? '')),
                trim((string) ($pregunta['opcion_c'] ?? '')),
                trim((string) ($pregunta['opcion_d'] ?? '')),
            ];

            if (trim((string) ($pregunta['enunciado'] ?? '')) === '' || count(array_filter($opciones)) !== 4) {
                throw new Exception('La pregunta ' . ($indice + 1) . ' está incompleta.');
            }

            if (count(array_unique(array_map('mb_strtolower', $opciones))) !== 4) {
                throw new Exception('La pregunta ' . ($indice + 1) . ' tiene opciones repetidas.');
            }

            if (!in_array((string) ($pregunta['respuesta_correcta'] ?? ''), ['a', 'b', 'c', 'd'], true)) {
                throw new Exception('La respuesta correcta de la pregunta ' . ($indice + 1) . ' no es válida.');
            }
        }

        if ($modo !== 'practica' && (int) ($resultado['tiempo_limite_min'] ?? 0) <= 0) {
            throw new Exception('La IA no generó un tiempo válido para el simulacro.');
        }
    }
}
