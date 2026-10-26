<?php

declare(strict_types=1);

class AyudaController extends Controller
{
    public function index(): void
    {
        $this->render('ayuda/index', [
            'pageTitle' => 'Ayuda | ProgressICFES',
            'activePage' => 'ayuda',
        ]);
    }
}
