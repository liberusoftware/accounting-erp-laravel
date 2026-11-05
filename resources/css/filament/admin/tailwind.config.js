import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './modules/accounting-erp-filament/src/Filament/**/*.php',
        './resources/views/**/*.blade.php',
        './modules/accounting-erp-filament/resources/views/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
    ],
}
