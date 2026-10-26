<?php

use Liberu\Foundation\Webhooks\Support\WebhookUrlPolicy;

beforeEach(function (): void {
    $this->webhookPolicy = new class() extends WebhookUrlPolicy
    {
        public array $resolved = ['8.8.8.8'];

        protected function addresses(string $host): array
        {
            return $this->resolved;
        }
    };
});

it('rejects internal and ambiguous webhook destinations', function (string $url): void {
    $this->webhookPolicy->options($url);
})->with([
    'http://127.0.0.1/hooks',
    'http://10.0.0.1/hooks',
    'http://172.16.0.1/hooks',
    'http://192.168.1.1/hooks',
    'http://169.254.169.254/latest/meta-data/',
    'http://100.64.0.1/hooks',
    'http://[::1]/hooks',
    'http://[fd00::1]/hooks',
    'http://[::ffff:127.0.0.1]/hooks',
    'https://user:password@example.com/hooks',
    'file:///etc/passwd',
])->throws(InvalidArgumentException::class);

it('rejects private DNS answers even when the host also has a public address', function (): void {
    $this->webhookPolicy->resolved = ['8.8.8.8', '127.0.0.1'];
    $this->webhookPolicy->options('https://example.com/hooks');
})->throws(InvalidArgumentException::class);

it('fails closed when webhook DNS resolution fails', function (): void {
    $this->webhookPolicy->resolved = [];
    $this->webhookPolicy->options('https://example.com/hooks');
})->throws(InvalidArgumentException::class);

it('pins validated addresses while disabling redirects and proxy resolution', function (): void {
    $options = $this->webhookPolicy->options('https://example.com:8443/hooks');

    expect($options['allow_redirects'])->toBeFalse()
        ->and($options['proxy'])->toBe('')
        ->and($options['curl'][CURLOPT_RESOLVE])->toBe(['example.com:8443:8.8.8.8']);
});
