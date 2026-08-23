<?php

namespace Codatsoft\Codatbase\Database;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

trait Translates
{
    /**
     * Return the translations relationship name.
     * Override in the model if you use a different relation name.
     */
    protected function translationsRelationName(): string
    {
        return 'translations';
    }

    /**
     * Get a single translation for the given locale (with optional fallback).
     */
    public function translated(?string $locale = null, ?string $fallback = null): ?Model
    {
        $relation = $this->translationsRelationName();

        // This will:
        // - return the already eager-loaded relation, OR
        // - lazy-load it once if not loaded yet.
        $translations = $this->{$relation};

        if (! $translations instanceof Collection) {
            return null;
        }

        $locale   = $locale   ?? app()->getLocale();
        $fallback = $fallback ?? config('app.fallback_locale', 'en');

        // 1) Try requested locale
        $t = $translations->firstWhere('locale', $locale);
        if ($t) {
            return $t;
        }

        // 2) Try fallback
        if ($fallback && $fallback !== $locale) {
            $t = $translations->firstWhere('locale', $fallback);
            if ($t) {
                return $t;
            }
        }

        return null;
    }

    /**
     * Convenience helper: get a field from the translated row.
     * Example: $pack->translatedField('name')
     */
    public function translatedField(string $field, ?string $locale = null, ?string $fallback = null): mixed
    {
        $t = $this->translated($locale, $fallback);

        return $t ? ($t->{$field} ?? null) : null;
    }

    /**
     * Resolve the best available translation, in order:
     *
     *   requested locale -> app fallback locale -> translationFallbackLocales() -> any row
     *
     * The last two steps are opt-in per model, so models that want the strict
     * requested-or-fallback behaviour of translated() keep it by doing nothing.
     * Use this where a null translation is not an acceptable answer — i.e. where the
     * text is the only copy that exists, not an optional localisation of something
     * already held elsewhere.
     */
    public function translatedResolved(?string $locale = null, ?string $fallback = null): ?Model
    {
        $found = $this->translated($locale, $fallback);
        if (!is_null($found))
        {
            return $found;
        }

        $translations = $this->{$this->translationsRelationName()};

        if (! $translations instanceof Collection)
        {
            return null;
        }

        foreach ($this->translationFallbackLocales() as $extra)
        {
            if (is_null($extra))
            {
                continue;
            }

            $t = $translations->firstWhere('locale', $extra);
            if ($t)
            {
                return $t;
            }
        }

        if ($this->translationAllowsAnyLocale())
        {
            return $translations->first();
        }

        return null;
    }

    /**
     * Extra locales to try after the app fallback locale. Override per model.
     * Nulls are skipped, so returning an unset column is safe.
     */
    protected function translationFallbackLocales(): array
    {
        return [];
    }

    /**
     * Whether any remaining translation may be used as a last resort. Override per model.
     */
    protected function translationAllowsAnyLocale(): bool
    {
        return false;
    }
}
