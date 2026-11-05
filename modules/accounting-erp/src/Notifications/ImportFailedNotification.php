<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Liberu\Accounting\Erp\Models\BankStatement;
use Liberu\Accounting\Erp\Notifications\Concerns\ResolvesChannels;

class ImportFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use ResolvesChannels;

    public function __construct(
        public BankStatement $statement,
        public string $error
    ) {}

    /**
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return $this->channelsFor($notifiable);
    }

    public function toMail($notifiable): MailMessage
    {
        $id = (string) $this->statement->getKey();

        return (new MailMessage())
            ->subject('Bank statement import failed')
            ->line("The import for bank statement #{$id} failed.")
            ->line("Error: {$this->error}");
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray($notifiable): array
    {
        return [
            'statement_id' => $this->statement->getKey(),
            'error' => $this->error,
        ];
    }
}
