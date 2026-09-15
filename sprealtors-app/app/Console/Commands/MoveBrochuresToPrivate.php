<?php

namespace App\Console\Commands;

use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MoveBrochuresToPrivate extends Command
{
    protected $signature = 'brochures:move-to-private';

    protected $description = 'Move brochures uploaded to the public disk onto the private disk, so downloads stay behind the lead form';

    public function handle(): int
    {
        $public = Storage::disk('public');
        $private = Storage::disk('local');
        $moved = 0;

        foreach (Project::whereNotNull('brochure')->get() as $project) {
            $path = $project->brochure;

            if ($private->exists($path)) {
                $this->line("  already private: {$project->name}");

                continue;
            }

            if (! $public->exists($path)) {
                $this->warn("  file missing for: {$project->name} ({$path})");

                continue;
            }

            $private->put($path, $public->get($path));
            $public->delete($path);
            $moved++;

            $this->info("  moved: {$project->name}");
        }

        $this->newLine();
        $this->info("Done. {$moved} brochure(s) moved to private storage.");

        return self::SUCCESS;
    }
}
