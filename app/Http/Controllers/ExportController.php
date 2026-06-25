<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Contribution;
use App\Models\Expense;
use App\Models\Street;
use App\Models\Villa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ExportController extends Controller
{
    public function __invoke(Request $request, string $resource, string $format)
    {
        [$title, $headers, $rows] = $this->dataset($request, $resource);

        return match ($format) {
            'excel' => $this->excel($title, $headers, $rows),
            'pdf' => $this->pdf($title, $headers, $rows, $resource),
            default => abort(404),
        };
    }

    private function dataset(Request $request, string $resource): array
    {
        return match ($resource) {
            'streets' => $this->streets($request),
            'villas' => $this->villas($request),
            'contributions' => $this->contributions($request),
            'expenses' => $this->expenses($request),
            'announcements' => $this->announcements($request),
            default => abort(404),
        };
    }

    private function streets(Request $request): array
    {
        $streets = Street::withCount('villas')
            ->when($request->q, fn (Builder $query, string $term) => $query->where('name', 'like', '%' . $term . '%'))
            ->when($request->date_debut, fn (Builder $query, string $date) => $query->whereDate('created_at', '>=', $date))
            ->when($request->date_fin, fn (Builder $query, string $date) => $query->whereDate('created_at', '<=', $date))
            ->latest()
            ->get();

        return [
            'Rues',
            ['Nom', 'Description', 'Villas', 'Cree le'],
            $streets->map(fn (Street $street) => [
                $street->name,
                $street->description,
                $street->villas_count,
                $street->created_at->format('d/m/Y H:i'),
            ]),
        ];
    }

    private function villas(Request $request): array
    {
        $villas = Villa::with('street')
            ->withSum('contributions', 'amount')
            ->when($request->q, function (Builder $query, string $term) {
                $query->where(function (Builder $query) use ($term) {
                    $query->where('number', 'like', '%' . $term . '%')
                        ->orWhere('owner_name', 'like', '%' . $term . '%')
                        ->orWhere('owner_email', 'like', '%' . $term . '%')
                        ->orWhere('owner_phone', 'like', '%' . $term . '%')
                        ->orWhereHas('street', fn (Builder $streetQuery) => $streetQuery->where('name', 'like', '%' . $term . '%'));
                });
            })
            ->when($request->date_debut, fn (Builder $query, string $date) => $query->whereDate('created_at', '>=', $date))
            ->when($request->date_fin, fn (Builder $query, string $date) => $query->whereDate('created_at', '<=', $date))
            ->latest()
            ->get();

        return [
            'Villas',
            ['Villa', 'Rue', 'Proprietaire', 'Email', 'Telephone', 'Cotisations', 'Cree le'],
            $villas->map(fn (Villa $villa) => [
                $villa->number,
                $villa->street->name,
                $villa->owner_name,
                $villa->owner_email,
                $villa->owner_phone,
                number_format($villa->contributions_sum_amount ?? 0, 0, ',', ' '),
                $villa->created_at->format('d/m/Y H:i'),
            ]),
        ];
    }

    private function contributions(Request $request): array
    {
        $contributions = Contribution::with('villa.street')
            ->when($request->q, function (Builder $query, string $term) {
                $query->where(function (Builder $query) use ($term) {
                    $query->where('payment_method', 'like', '%' . $term . '%')
                        ->orWhere('reference', 'like', '%' . $term . '%')
                        ->orWhere('notes', 'like', '%' . $term . '%')
                        ->orWhereHas('villa', function (Builder $villaQuery) use ($term) {
                            $villaQuery->where('number', 'like', '%' . $term . '%')
                                ->orWhere('owner_name', 'like', '%' . $term . '%')
                                ->orWhere('owner_email', 'like', '%' . $term . '%')
                                ->orWhere('owner_phone', 'like', '%' . $term . '%')
                                ->orWhereHas('street', fn (Builder $streetQuery) => $streetQuery->where('name', 'like', '%' . $term . '%'));
                        });
                });
            })
            ->when($request->date_debut, fn (Builder $query, string $date) => $query->whereDate('month', '>=', $date))
            ->when($request->date_fin, fn (Builder $query, string $date) => $query->whereDate('month', '<=', $date))
            ->when($request->paiement_debut, fn (Builder $query, string $date) => $query->whereDate('paid_at', '>=', $date))
            ->when($request->paiement_fin, fn (Builder $query, string $date) => $query->whereDate('paid_at', '<=', $date))
            ->latest('month')
            ->get();

        return [
            'Cotisations',
            ['Mois', 'Villa', 'Rue', 'Proprietaire', 'Date paiement', 'Montant', 'Methode', 'Reference', 'Cree le'],
            $contributions->map(fn (Contribution $contribution) => [
                $contribution->month->format('m/Y'),
                $contribution->villa->number,
                $contribution->villa->street?->name,
                $contribution->villa->owner_name,
                $contribution->paid_at->format('d/m/Y'),
                number_format($contribution->amount, 0, ',', ' '),
                $contribution->payment_method,
                $contribution->reference,
                $contribution->created_at->format('d/m/Y H:i'),
            ]),
        ];
    }

    private function expenses(Request $request): array
    {
        $expenses = Expense::when($request->q, function (Builder $query, string $term) {
            $query->where(function (Builder $query) use ($term) {
                $query->where('title', 'like', '%' . $term . '%')
                    ->orWhere('category', 'like', '%' . $term . '%')
                    ->orWhere('description', 'like', '%' . $term . '%');
            });
        })
            ->when($request->date_debut, fn (Builder $query, string $date) => $query->whereDate('spent_at', '>=', $date))
            ->when($request->date_fin, fn (Builder $query, string $date) => $query->whereDate('spent_at', '<=', $date))
            ->latest('spent_at')
            ->get();

        return [
            'Depenses',
            ['Date', 'Depense', 'Categorie', 'Montant', 'Description', 'Cree le'],
            $expenses->map(fn (Expense $expense) => [
                $expense->spent_at->format('d/m/Y'),
                $expense->title,
                $expense->category,
                number_format($expense->amount, 0, ',', ' '),
                $expense->description,
                $expense->created_at->format('d/m/Y H:i'),
            ]),
        ];
    }

    private function announcements(Request $request): array
    {
        $announcements = Announcement::when($request->q, function (Builder $query, string $term) {
            $query->where(function (Builder $query) use ($term) {
                $query->where('title', 'like', '%' . $term . '%')
                    ->orWhere('message', 'like', '%' . $term . '%')
                    ->orWhere('channel', 'like', '%' . $term . '%');
            });
        })
            ->when($request->date_debut, fn (Builder $query, string $date) => $query->whereDate('sent_at', '>=', $date))
            ->when($request->date_fin, fn (Builder $query, string $date) => $query->whereDate('sent_at', '<=', $date))
            ->latest()
            ->get();

        return [
            'Annonces',
            ['Titre', 'Message', 'Canal', 'Date envoi', 'Cree le'],
            $announcements->map(fn (Announcement $announcement) => [
                $announcement->title,
                $announcement->message,
                ucfirst($announcement->channel),
                $announcement->sent_at?->format('d/m/Y H:i'),
                $announcement->created_at->format('d/m/Y H:i'),
            ]),
        ];
    }

    private function excel(string $title, array $headers, Collection $rows)
    {
        $html = view('exports.excel', compact('title', 'headers', 'rows'))->render();

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . str($title)->slug() . '.xls"',
        ]);
    }

    private function pdf(string $title, array $headers, Collection $rows, string $resource)
    {
        if ($resource === 'contributions') {
            [$headers, $rows] = $this->contributionsPdfColumns($headers, $rows);
        }

        @ini_set('memory_limit', '256M');
        set_time_limit(120);

        return Pdf::loadView('exports.pdf', compact('title', 'headers', 'rows'))
            ->setPaper('a4', 'landscape')
            ->download(str($title)->slug() . '.pdf');
    }

    /**
     * DomPDF struggles with wide tables; keep essential columns for PDF export.
     */
    private function contributionsPdfColumns(array $headers, Collection $rows): array
    {
        $keep = [0, 1, 2, 3, 4, 5, 6];

        $headers = array_map(fn (int $index) => $headers[$index], $keep);
        $rows = $rows->map(fn (array $row) => array_map(fn (int $index) => $row[$index], $keep));

        return [$headers, $rows];
    }
}
