<?php
namespace App\Console\Commands;

use App\Models\Certificate;
use App\Models\Portfolio;
use App\Models\PortfolioImage;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupStorage extends Command
{
    protected $signature = 'storage:cleanup {--force : Hapus tanpa konfirmasi}';
    protected $description = 'Hapus file gambar yatim (tidak lagi dirujuk di database)';

    public function handle(): int
    {
        $referenced = collect()
            ->merge(User::whereNotNull('profile_photo')->pluck('profile_photo'))
            ->merge(Portfolio::whereNotNull('thumbnail')->pluck('thumbnail'))
            ->merge(PortfolioImage::pluck('image'))
            ->merge(Certificate::whereNotNull('image')->pluck('image'))
            ->filter()
            ->flip();

        $files = Storage::disk('public')->allFiles();
        $orphans = array_filter($files, function ($file) use ($referenced) {
            $name = basename($file);

            // Lewati file tersembunyi seperti .gitignore
            if (str_starts_with($name, '.')) {
                return false;
            }

            return ! isset($referenced[$file]);
        });

        if (empty($orphans)) {
            $this->info('Tidak ada file yatim.');
            return self::SUCCESS;
        }

        $this->info(count($orphans).' file yatim ditemukan.');
        if (! $this->option('force') && ! $this->confirm('Hapus file-file ini?', false)) {
            return self::SUCCESS;
        }

        Storage::disk('public')->delete(array_values($orphans));
        $this->info('File yatim dihapus.');

        return self::SUCCESS;
    }
}