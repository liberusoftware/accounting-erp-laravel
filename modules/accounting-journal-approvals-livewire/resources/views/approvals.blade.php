<div>
    <select wire:model.live="status">
        <option value="">All statuses</option>
        @foreach (\Liberu\Accounting\JournalApprovals\Enums\ApprovalStatus::cases() as $approvalStatus)
            <option value="{{ $approvalStatus->value }}">{{ $approvalStatus->name }}</option>
        @endforeach
    </select>
    <table>
        <tbody>
            @foreach ($approvals as $approval)
                <tr><td>{{ $approval->approval_ref }}</td><td>{{ $approval->journal_ref }}</td><td>{{ $approval->amount }} {{ $approval->currency }}</td><td>{{ $approval->status->value }}</td></tr>
            @endforeach
        </tbody>
    </table>
    {{ $approvals->links() }}
</div>
