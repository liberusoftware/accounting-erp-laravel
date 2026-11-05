<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\LaravelSettings\Factories\SettingsRepositoryFactory;

return new class() extends Migration
{
    public function up(): void
    {
        $repository = SettingsRepositoryFactory::create();
        $names = [
            'site_name', 'site_email', 'site_phone', 'site_address', 'site_country',
            'site_currency', 'site_default_language', 'facebook_url', 'twitter_url',
            'github_url', 'youtube_url', 'footer_copyright',
        ];
        $legacy = array_intersect_key($repository->getPropertiesInGroup('general'), array_flip($names));

        foreach ($legacy as $name => $value) {
            if ($repository->checkIfPropertyExists('site', $name)) {
                $repository->updatePropertiesPayload('site', [$name => $value]);
            } else {
                $repository->createProperty('site', $name, $value);
            }
        }

        $locked = array_intersect(array_keys($legacy), $repository->getLockedProperties('general'));
        if ($locked !== []) {
            $repository->lockProperties('site', $locked);
        }
    }

    public function down(): void
    {
        // Keep configured site values and the original general rows on rollback.
        // Restoring older site values would discard settings edited since migration.
    }
};
