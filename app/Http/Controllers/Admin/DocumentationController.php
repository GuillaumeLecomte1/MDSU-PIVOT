<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DocumentationService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DocumentationController extends Controller
{
    public function __construct(
        private readonly DocumentationService $documentationService
    ) {
    }

    public function index(): Response
    {
        return Inertia::render('Admin/Documentation/Index', [
            'documents' => $this->documentationService->getAllDocuments(),
        ]);
    }

    public function show(string $filename): Response
    {
        $content = $this->documentationService->getDocument($filename);

        if ($content === null) {
            abort(404);
        }

        return Inertia::render('Admin/Documentation/Show', [
            'document' => [
                'filename' => $filename,
                'content' => $content,
            ],
        ]);
    }
} 