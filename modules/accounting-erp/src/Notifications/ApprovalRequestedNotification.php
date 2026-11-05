<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Liberu\Accounting\Erp\Models\ApprovalStep;
use Liberu\Accounting\Erp\Models\User;
use Liberu\Accounting\Erp\Notifications\Concerns\ResolvesChannels;

class ApprovalRequestedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use ResolvesChannels;

    public function __construct(
        protected ApprovalStep $step
    ) {}

    /**
     * Notify every user holding $step->role on team $teamId.
     */
    public static function dispatchToRole(ApprovalStep $step, int $teamId): void
    {
        $users = User::role($step->role)
            ->where(function ($query) use ($teamId): void {
                $query->where('current_team_id', $teamId)
                    ->orWhereHas('teams', function ($teams) use ($teamId): void {
                        $teams->where('teams.id', $teamId);
                    });
            })
            ->get();

        NotificationFacade::send($users, new self($step));
    }

    public function via($notifiable): array
    {
        return $this->channelsFor($notifiable);
    }

    public function toSms($notifiable): string
    {
        return "Approval required for {$this->step->request->approvable_type} #{$this->step->request->approvable_id}.";
    }

    public function toMail($notifiable): MailMessage
    {
        $subject = "Approval required for {$this->step->request->approvable_type} #{$this->step->request->approvable_id}";

        return (new MailMessage())
            ->subject($subject)
            ->greeting("Hello {$notifiable->name}")
            ->line($subject);
    }

    public function toArray($notifiable): array
    {
        return [
            'approval_step_id' => $this->step->getKey(),
            'approvable_type' => $this->step->request->approvable_type,
            'approvable_id' => $this->step->request->approvable_id,
            'role' => $this->step->role,
        ];
    }
}
