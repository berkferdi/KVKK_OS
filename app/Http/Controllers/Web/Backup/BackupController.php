<?php

namespace App\Http\Controllers\Web\Backup;

use App\Application\Services\Backup\BackupService;
use App\Domain\Backup\Models\Backup;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class BackupController extends Controller
{
    public function __construct(
        private readonly BackupService $backups,
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', Backup::class);

        return view('backups.index', [
            'backups' => $this->backups->paginate(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Backup::class);

        try {
            $backup = $this->backups->run(
                actor: $request->user(),
                includeStorage: $request->boolean('include_storage'),
            );
        } catch (Throwable $e) {
            return redirect()
                ->route('backups.index')
                ->with('error', 'Yedekleme başarısız: '.$e->getMessage());
        }

        return redirect()
            ->route('backups.index')
            ->with('success', 'Yedek oluşturuldu: '.$backup->path);
    }

    public function download(Backup $backup): BinaryFileResponse
    {
        $this->authorize('download', $backup);

        $path = $this->backups->absolutePath($backup);
        $name = basename((string) $backup->path);

        return response()->download($path, $name);
    }

    public function destroy(Backup $backup): RedirectResponse
    {
        $this->authorize('delete', $backup);
        $this->backups->delete($backup);

        return redirect()
            ->route('backups.index')
            ->with('success', 'Yedek silindi.');
    }
}
