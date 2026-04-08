<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Gate;

class QuotePdfController extends Controller
{
    public function __invoke(Quote $quote)
    {
        Gate::authorize('view', $quote);

        $pdf = Pdf::loadView('pdf.quote', [
            'quote' => $quote->load([
                'client', 'lead', 'phases.items.service', 'creator'
            ]),
        ])->setPaper('a4', 'portrait');

        return $pdf->download("orcamento-{$quote->number}.pdf");
    }
}
