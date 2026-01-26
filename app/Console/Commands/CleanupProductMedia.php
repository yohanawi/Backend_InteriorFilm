<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class CleanupProductMedia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:cleanup-media {--dry-run : Show what would change without writing} {--with-trashed : Include soft-deleted products}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Normalize product thumbnail/images fields by removing false/0 entries and invalid image paths.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $withTrashed = (bool) $this->option('with-trashed');

        $query = Product::query();
        if ($withTrashed) {
            $query->withTrashed();
        }

        $total = (int) $query->count();
        $this->info("Scanning {$total} products...");

        $changed = 0;

        $query->orderBy('id')->chunkById(200, function ($products) use ($dryRun, &$changed) {
            foreach ($products as $product) {
                $rawThumb = $product->getRawOriginal('thumbnail');
                $rawImages = $product->getRawOriginal('images');

                $newThumb = $this->normalizeThumbnailRaw($rawThumb);
                $newImages = $this->normalizeImagesRaw($rawImages);

                $thumbChanged = $this->different($rawThumb, $newThumb);
                $imagesChanged = $this->different($rawImages, $newImages);

                if (!$thumbChanged && !$imagesChanged) {
                    continue;
                }

                $changed++;

                if ($dryRun) {
                    $this->line("#{$product->id} {$product->name}");
                    if ($thumbChanged) {
                        $this->line('  thumbnail: ' . $this->stringify($rawThumb) . ' -> ' . $this->stringify($newThumb));
                    }
                    if ($imagesChanged) {
                        $this->line('  images: ' . $this->stringify($rawImages) . ' -> ' . $this->stringify($newImages));
                    }
                    continue;
                }

                // Use raw assignment to avoid accessor filtering during save.
                $product->thumbnail = $newThumb;
                $product->images = $newImages;
                $product->save();
            }
        });

        if ($dryRun) {
            $this->info("Dry-run complete. {$changed} product(s) would change.");
        } else {
            $this->info("Cleanup complete. Updated {$changed} product(s).");
        }

        return self::SUCCESS;
    }

    private function normalizeThumbnailRaw($raw)
    {
        if ($raw === null) return null;
        if ($raw === false) return null;
        if (is_int($raw) || is_float($raw)) return null;

        if (!is_string($raw)) return null;
        $t = trim($raw);
        if ($t === '' || $t === '0') return null;

        return $t;
    }

    private function normalizeImagesRaw($raw)
    {
        if ($raw === null || $raw === '') return null;

        $decoded = $raw;
        if (is_string($decoded)) {
            $tmp = json_decode($decoded, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $decoded = $tmp;
            }
        }

        if (!is_array($decoded)) {
            return null;
        }

        $out = [];
        foreach ($decoded as $item) {
            if (!is_string($item)) {
                continue;
            }
            $t = trim($item);
            if ($t === '' || $t === '0') {
                continue;
            }
            $out[] = $t;
        }

        $out = array_values(array_unique($out));
        return $out ?: null;
    }

    private function different($a, $b): bool
    {
        // Normalize to comparable JSON-ish strings.
        return $this->stringify($a) !== $this->stringify($b);
    }

    private function stringify($v): string
    {
        if ($v === null) return 'null';
        if ($v === false) return 'false';
        if ($v === true) return 'true';
        if (is_string($v)) return $v;
        if (is_int($v) || is_float($v)) return (string) $v;

        return json_encode($v, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: gettype($v);
    }
}
