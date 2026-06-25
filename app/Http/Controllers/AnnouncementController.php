<?php

namespace App\Http\Controllers;

use App\Mail\AnnouncementMail;
use App\Models\Announcement;
use App\Models\Villa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index()
    {
        return view('announcements.index', [
            'announcements' => Announcement::when(request('q'), function ($query, $term) {
                $query->where(function ($query) use ($term) {
                    $query->where('title', 'like', '%' . $term . '%')
                        ->orWhere('destinataire', 'like', '%' . $term . '%')
                        ->orWhere('message', 'like', '%' . $term . '%')
                        ->orWhere('channel', 'like', '%' . $term . '%');
                });
            })
                ->when(request('date_debut'), fn ($query, $date) => $query->whereDate('sent_at', '>=', $date))
                ->when(request('date_fin'), fn ($query, $date) => $query->whereDate('sent_at', '<=', $date))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
            'villas' => Villa::select(['id', 'street_id', 'number', 'owner_name', 'owner_email', 'owner_phone'])
                ->with('street:id,name')
                ->orderBy('owner_name')
                ->paginate(20)
                ->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('announcements.create');
    }

    public function store(Request $request)
    {
        try {
            $data = $this->validated($request);
        } catch (ValidationException $exception) {
            return $this->modalValidationResponse('announcements.create', $exception);
        }

        $announcement = Announcement::create($data);

        return $this->turboRedirect(
            route('announcements.send', $announcement),
            'Vous avez créé une nouvelle annonce. Vous pouvez maintenant l\'envoyer.'
        );
    }

    public function edit(Announcement $announcement)
    {
        if ($announcement->sent_at) {
            return redirect()
                ->route('announcements.show', $announcement)
                ->with('success', 'Cette annonce a déjà été envoyée et ne peut plus être modifiée.');
        }

        return view('announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        if ($announcement->sent_at) {
            return redirect()
                ->route('announcements.show', $announcement)
                ->with('success', 'Cette annonce a déjà été envoyée et ne peut plus être modifiée.');
        }

        try {
            $data = $this->validated($request);
        } catch (ValidationException $exception) {
            return $this->modalValidationResponse('announcements.edit', $exception, compact('announcement'));
        }

        $announcement->update($data);

        return $this->turboRedirect(
            route('announcements.send', $announcement),
            'Annonce modifiée.'
        );
    }

    public function destroy(Announcement $announcement)
    {
        if ($announcement->sent_at) {
            return redirect()
                ->route('announcements.index')
                ->with('success', 'Impossible de supprimer une annonce déjà envoyée.');
        }

        $announcement->delete();

        return $this->turboRedirect(
            route('announcements.index'),
            'Annonce supprimée.'
        );
    }

    public function show(Announcement $announcement)
    {
        return view('announcements.show', compact('announcement'));
    }

    public function send(Announcement $announcement)
    {
        return view('announcements.send', [
            'announcement' => $announcement,
            'villas' => Villa::select(['id', 'street_id', 'number', 'owner_name', 'owner_email', 'owner_phone'])
                ->with('street:id,name')
                ->orderBy('owner_name')
                ->paginate(20)
                ->withQueryString(),
            'emailRecipients' => Villa::whereNotNull('owner_email')
                ->where('owner_email', '<>', '')
                ->distinct()
                ->pluck('owner_email')
                ->filter()
                ->values()
                ->all(),
        ]);
    }

    public function pdf(Announcement $announcement)
    {
        return Pdf::loadView('announcements.pdf', [
            'announcement' => $announcement,
        ])->download('annonce-' . $announcement->id . '.pdf');
    }

    public function dispatch(Announcement $announcement)
    {
        if (! $announcement->sent_at) {
            $markedAsSent = false;

            if ($announcement->channel === 'email') {
                $emails = Villa::whereNotNull('owner_email')
                    ->where('owner_email', '<>', '')
                    ->distinct()
                    ->pluck('owner_email')
                    ->filter()
                    ->values()
                    ->all();

                if (count($emails) > 0) {
                    $pdf = Pdf::loadView('announcements.pdf', [
                        'announcement' => $announcement,
                    ])->output();

                    Mail::to($emails[0])
                        ->bcc(array_slice($emails, 1))
                        ->send((new AnnouncementMail($announcement))
                            ->attachData($pdf, 'annonce-' . $announcement->id . '.pdf', [
                                'mime' => 'application/pdf',
                            ])
                        );

                    $markedAsSent = true;
                }
            } else {
                $markedAsSent = true;
            }

            if ($markedAsSent) {
                $announcement->update(['sent_at' => now()]);
            }
        }

        $message = $announcement->fresh()->sent_at
            ? 'Annonce envoyée à tous les propriétaires.'
            : 'Aucun email disponible : l\'annonce reste en attente.';

        return redirect()
            ->route('announcements.send', $announcement)
            ->with('success', $message);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'destinataire' => ['nullable', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:10000'],
            'channel' => ['required', 'in:email,phone,whatsapp'],
            'target' => ['nullable', 'in:all'],
        ]);

        $data['destinataire'] = trim($data['destinataire'] ?? '') ?: 'Tous les proprietaires';
        $data['target'] = $data['target'] ?? 'all';

        return $data;
    }

    private function turboRedirect(string $url, ?string $message = null): RedirectResponse
    {
        $redirect = redirect()->to($url);

        if ($message) {
            $redirect->with('success', $message);
        }

        return $redirect->header('Turbo-Location', $url);
    }

    private function modalValidationResponse(string $view, ValidationException $exception, array $data = [])
    {
        if (request()->header('Turbo-Frame') === 'modal') {
            return response(
                view($view, $data)->withErrors($exception->validator)->withInput(),
                422
            );
        }

        throw $exception;
    }
}
