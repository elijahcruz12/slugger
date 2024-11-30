<?php

namespace App\Services;

class Slugger
{
    protected string $original_filename;

    protected string $slug;

    protected bool $would_overwrite = false;

    protected bool $allow_caps = false;

    public function __construct(string $name, bool $allow_caps = false)
    {
        // Let's get the original filename (including extension, but no path)
        $this->original_filename = pathinfo($name, PATHINFO_BASENAME);
        $this->allow_caps = $allow_caps;
        $this->createSlug();
    }

    public function getOriginal(): string
    {
        return $this->original_filename;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    private function createSlug(): void
    {
        // First, let's check for double dots in the filename
        $has_double_dots = str_contains($this->original_filename, '..');

        while($has_double_dots) {
            $this->original_filename = str_replace('..', '.', $this->original_filename);
            $has_double_dots = str_contains($this->original_filename, '..');
        }

        // Now let's check for any non-alphanumeric characters (except for periods)
        $this->slug = preg_replace('/[^a-zA-Z0-9.]/', '-', $this->original_filename);

        // Now let's check for double dashes
        $has_double_dashes = str_contains($this->slug, '--');

        while($has_double_dashes) {
            $this->slug = str_replace('--', '-', $this->slug);
            $has_double_dashes = str_contains($this->slug, '--');
        }

        // If the slug contains a dash after a dot, let's remove the dash
        $this->slug = str_replace('.-', '.', $this->slug);

        // If the string starts or ends with a dash, let's remove it
        $this->slug = trim($this->slug, '-');

        // Finally, let's make sure the slug is lowercase
        if(!$this->allow_caps){
            $this->slug = strtolower($this->slug);
        }

        // If the slug is empty, let's just use the original filename
        if(empty($this->slug)) {
            $this->slug = $this->original_filename;
        }

        if($this->original_filename !== $this->slug) {
            $this->would_overwrite = true;
        }
    }

    public function wouldOverwrite(): bool
    {
        return $this->would_overwrite;
    }
}
