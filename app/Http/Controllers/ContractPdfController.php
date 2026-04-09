<?php

namespace App\Http\Controllers;

use App\Models\ContractClause;
use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Gate;

class ContractPdfController extends Controller
{
    public function __invoke(Project $project)
    {
        Gate::authorize('view', $project);

        $project->load(['client', 'projectManager', 'phases', 'roadmapItems']);

        $clauses = ContractClause::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $pdf = Pdf::loadView('pdf.contract', [
            'project' => $project,
            'clauses' => $clauses,
        ])->setPaper('a4', 'portrait');

        return $pdf->download("contrato-{$project->code}.pdf");
    }
}
