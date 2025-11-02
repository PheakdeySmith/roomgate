<?php

namespace App\Services\Notification;

use App\Models\User;
use App\Models\Invoice;
use App\Models\Contract;
use App\Models\Payment;
use App\Models\NotificationLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    /**
     * Notification channels
     */
    const CHANNEL_EMAIL = 'email';
    const CHANNEL_SMS = 'sms';
    const CHANNEL_DATABASE = 'database';
    const CHANNEL_PUSH = 'push';

    /**
     * Notification types
     */
    const TYPE_INVOICE_CREATED = 'invoice_created';
    const TYPE_INVOICE_REMINDER = 'invoice_reminder';
    const TYPE_INVOICE_OVERDUE = 'invoice_overdue';
    const TYPE_PAYMENT_RECEIVED = 'payment_received';
    const TYPE_CONTRACT_EXPIRING = 'contract_expiring';
    const TYPE_CONTRACT_EXPIRED = 'contract_expired';
    const TYPE_CONTRACT_RENEWED = 'contract_renewed';
    const TYPE_MAINTENANCE_UPDATE = 'maintenance_update';
    const TYPE_WELCOME = 'welcome';
    const TYPE_SUBSCRIPTION_EXPIRING = 'subscription_expiring';

    /**
     * Send invoice created notification
     */
    public function sendInvoiceCreatedNotification(Invoice $invoice, array $channels = ['email']): bool
    {
        try {
            $tenant = $invoice->contract->tenant;
            $data = $this->prepareInvoiceData($invoice);

            foreach ($channels as $channel) {
                $this->sendNotification(
                    $tenant,
                    self::TYPE_INVOICE_CREATED,
                    $channel,
                    'New Invoice Available',
                    $this->getInvoiceCreatedMessage($invoice),
                    $data
                );
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send invoice created notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send invoice reminder notification
     */
    public function sendInvoiceReminderNotification(Invoice $invoice, array $channels = ['email']): bool
    {
        try {
            $tenant = $invoice->contract->tenant;
            $daysUntilDue = now()->diffInDays($invoice->due_date, false);
            $data = $this->prepareInvoiceData($invoice);

            foreach ($channels as $channel) {
                $this->sendNotification(
                    $tenant,
                    self::TYPE_INVOICE_REMINDER,
                    $channel,
                    'Invoice Payment Reminder',
                    $this->getInvoiceReminderMessage($invoice, $daysUntilDue),
                    $data
                );
            }

            // Update reminder count
            $invoice->update([
                'last_reminder_date' => now(),
                'reminder_count' => ($invoice->reminder_count ?? 0) + 1,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send invoice reminder: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send overdue invoice notification
     */
    public function sendOverdueInvoiceNotification(Invoice $invoice, array $channels = ['email']): bool
    {
        try {
            $tenant = $invoice->contract->tenant;
            $daysOverdue = now()->diffInDays($invoice->due_date);
            $data = $this->prepareInvoiceData($invoice);

            foreach ($channels as $channel) {
                $this->sendNotification(
                    $tenant,
                    self::TYPE_INVOICE_OVERDUE,
                    $channel,
                    'Overdue Invoice Notice',
                    $this->getOverdueInvoiceMessage($invoice, $daysOverdue),
                    $data
                );
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send overdue invoice notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send payment received notification
     */
    public function sendPaymentReceivedNotification(Payment $payment, array $channels = ['email']): bool
    {
        try {
            $tenant = $payment->invoice->contract->tenant;
            $data = $this->preparePaymentData($payment);

            foreach ($channels as $channel) {
                $this->sendNotification(
                    $tenant,
                    self::TYPE_PAYMENT_RECEIVED,
                    $channel,
                    'Payment Received',
                    $this->getPaymentReceivedMessage($payment),
                    $data
                );
            }

            // Also notify landlord
            $landlord = $payment->invoice->contract->room->property->landlord;
            $this->sendNotification(
                $landlord,
                self::TYPE_PAYMENT_RECEIVED,
                self::CHANNEL_DATABASE,
                'Payment Received',
                $this->getPaymentReceivedMessageForLandlord($payment),
                $data
            );

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send payment received notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send contract expiring notification
     */
    public function sendContractExpiringNotification(Contract $contract, int $daysUntilExpiry, array $channels = ['email']): bool
    {
        try {
            $tenant = $contract->tenant;
            $data = $this->prepareContractData($contract);

            foreach ($channels as $channel) {
                $this->sendNotification(
                    $tenant,
                    self::TYPE_CONTRACT_EXPIRING,
                    $channel,
                    'Contract Expiring Soon',
                    $this->getContractExpiringMessage($contract, $daysUntilExpiry),
                    $data
                );
            }

            // Also notify landlord
            $landlord = $contract->room->property->landlord;
            $this->sendNotification(
                $landlord,
                self::TYPE_CONTRACT_EXPIRING,
                self::CHANNEL_DATABASE,
                'Tenant Contract Expiring',
                $this->getContractExpiringMessageForLandlord($contract, $daysUntilExpiry),
                $data
            );

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send contract expiring notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send welcome notification to new tenant
     */
    public function sendWelcomeTenantNotification(User $tenant, Contract $contract, array $channels = ['email']): bool
    {
        try {
            $data = [
                'tenant_name' => $tenant->name,
                'property' => $contract->room->property->name,
                'room' => $contract->room->room_number,
                'start_date' => $contract->start_date->format('Y-m-d'),
                'monthly_rent' => $contract->monthly_rent,
            ];

            foreach ($channels as $channel) {
                $this->sendNotification(
                    $tenant,
                    self::TYPE_WELCOME,
                    $channel,
                    'Welcome to RoomGate',
                    $this->getWelcomeMessage($tenant, $contract),
                    $data
                );
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send welcome notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Process scheduled notifications
     */
    public function processScheduledNotifications(): int
    {
        $processed = 0;

        // Invoice reminders (3 days before due)
        $this->processInvoiceReminders();

        // Overdue invoice notifications
        $this->processOverdueInvoices();

        // Contract expiring notifications (30 days before)
        $this->processExpiringContracts();

        // Subscription expiring notifications
        $this->processExpiringSubscriptions();

        return $processed;
    }

    /**
     * Process invoice reminders
     */
    protected function processInvoiceReminders(): int
    {
        $processed = 0;

        $invoices = Invoice::where('status', '!=', 'paid')
            ->where('status', '!=', 'void')
            ->whereBetween('due_date', [now()->addDays(1), now()->addDays(3)])
            ->whereNull('last_reminder_date')
            ->orWhere('last_reminder_date', '<', now()->subDays(3))
            ->get();

        foreach ($invoices as $invoice) {
            if ($this->sendInvoiceReminderNotification($invoice)) {
                $processed++;
            }
        }

        Log::info("Processed {$processed} invoice reminders");
        return $processed;
    }

    /**
     * Process overdue invoices
     */
    protected function processOverdueInvoices(): int
    {
        $processed = 0;

        $invoices = Invoice::where('status', '!=', 'paid')
            ->where('status', '!=', 'void')
            ->where('due_date', '<', now())
            ->where(function ($query) {
                $query->whereNull('last_overdue_notification')
                    ->orWhere('last_overdue_notification', '<', now()->subDays(7));
            })
            ->get();

        foreach ($invoices as $invoice) {
            if ($this->sendOverdueInvoiceNotification($invoice)) {
                $invoice->update(['last_overdue_notification' => now()]);
                $processed++;
            }
        }

        Log::info("Processed {$processed} overdue invoice notifications");
        return $processed;
    }

    /**
     * Process expiring contracts
     */
    protected function processExpiringContracts(): int
    {
        $processed = 0;

        // 30 days before expiry
        $contracts = Contract::where('status', 'active')
            ->whereBetween('end_date', [now()->addDays(29), now()->addDays(31)])
            ->where(function ($query) {
                $query->whereNull('expiry_notification_sent')
                    ->orWhere('expiry_notification_sent', false);
            })
            ->get();

        foreach ($contracts as $contract) {
            if ($this->sendContractExpiringNotification($contract, 30)) {
                $contract->update(['expiry_notification_sent' => true]);
                $processed++;
            }
        }

        // 7 days before expiry
        $urgentContracts = Contract::where('status', 'active')
            ->whereBetween('end_date', [now()->addDays(6), now()->addDays(8)])
            ->get();

        foreach ($urgentContracts as $contract) {
            if ($this->sendContractExpiringNotification($contract, 7, ['email', 'sms'])) {
                $processed++;
            }
        }

        Log::info("Processed {$processed} contract expiring notifications");
        return $processed;
    }

    /**
     * Process expiring subscriptions
     */
    protected function processExpiringSubscriptions(): int
    {
        $processed = 0;

        $subscriptions = DB::table('user_subscriptions')
            ->where('is_active', true)
            ->whereBetween('end_date', [now(), now()->addDays(7)])
            ->get();

        foreach ($subscriptions as $subscription) {
            $user = User::find($subscription->user_id);
            if ($user) {
                $this->sendSubscriptionExpiringNotification($user, $subscription);
                $processed++;
            }
        }

        Log::info("Processed {$processed} subscription expiring notifications");
        return $processed;
    }

    /**
     * Send subscription expiring notification
     */
    protected function sendSubscriptionExpiringNotification(User $landlord, $subscription): bool
    {
        try {
            $daysRemaining = now()->diffInDays(Carbon::parse($subscription->end_date));

            $this->sendNotification(
                $landlord,
                self::TYPE_SUBSCRIPTION_EXPIRING,
                self::CHANNEL_EMAIL,
                'Subscription Expiring Soon',
                "Your subscription will expire in {$daysRemaining} days. Please renew to continue using RoomGate services.",
                [
                    'subscription_id' => $subscription->id,
                    'end_date' => $subscription->end_date,
                    'days_remaining' => $daysRemaining,
                ]
            );

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send subscription expiring notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Core notification sending method
     */
    protected function sendNotification(
        User $recipient,
        string $type,
        string $channel,
        string $subject,
        string $message,
        array $data = []
    ): void {
        switch ($channel) {
            case self::CHANNEL_EMAIL:
                $this->sendEmailNotification($recipient, $subject, $message, $data);
                break;

            case self::CHANNEL_SMS:
                $this->sendSmsNotification($recipient, $message);
                break;

            case self::CHANNEL_DATABASE:
                $this->saveDatabaseNotification($recipient, $type, $subject, $message, $data);
                break;

            case self::CHANNEL_PUSH:
                $this->sendPushNotification($recipient, $subject, $message, $data);
                break;
        }

        // Log notification
        $this->logNotification($recipient, $type, $channel, $subject);
    }

    /**
     * Send email notification
     */
    protected function sendEmailNotification(User $recipient, string $subject, string $message, array $data = []): void
    {
        // This would integrate with your mail configuration
        // For now, just log the action
        Log::info("Email notification sent to {$recipient->email}: {$subject}");
    }

    /**
     * Send SMS notification
     */
    protected function sendSmsNotification(User $recipient, string $message): void
    {
        if (!$recipient->phone) {
            return;
        }

        // This would integrate with SMS provider (Twilio, etc.)
        // For now, just log the action
        Log::info("SMS notification sent to {$recipient->phone}: {$message}");
    }

    /**
     * Save database notification
     */
    protected function saveDatabaseNotification(
        User $recipient,
        string $type,
        string $subject,
        string $message,
        array $data = []
    ): void {
        DB::table('notifications')->insert([
            'id' => \Str::uuid(),
            'type' => 'App\\Notifications\\' . ucfirst($type),
            'notifiable_type' => get_class($recipient),
            'notifiable_id' => $recipient->id,
            'data' => json_encode([
                'subject' => $subject,
                'message' => $message,
                'details' => $data,
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Send push notification
     */
    protected function sendPushNotification(User $recipient, string $subject, string $message, array $data = []): void
    {
        // This would integrate with push notification service (Firebase, etc.)
        // For now, just log the action
        Log::info("Push notification sent to user {$recipient->id}: {$subject}");
    }

    /**
     * Log notification
     */
    protected function logNotification(User $recipient, string $type, string $channel, string $subject): void
    {
        if (DB::schema()->hasTable('notification_logs')) {
            DB::table('notification_logs')->insert([
                'user_id' => $recipient->id,
                'type' => $type,
                'channel' => $channel,
                'subject' => $subject,
                'sent_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Prepare invoice data for notification
     */
    protected function prepareInvoiceData(Invoice $invoice): array
    {
        return [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'amount' => $invoice->total_amount,
            'due_date' => $invoice->due_date->format('Y-m-d'),
            'tenant_name' => $invoice->contract->tenant->name,
            'property' => $invoice->contract->room->property->name,
            'room' => $invoice->contract->room->room_number,
        ];
    }

    /**
     * Prepare payment data for notification
     */
    protected function preparePaymentData(Payment $payment): array
    {
        return [
            'payment_id' => $payment->id,
            'amount' => $payment->amount,
            'payment_date' => $payment->payment_date,
            'payment_method' => $payment->payment_method,
            'invoice_number' => $payment->invoice->invoice_number,
            'balance' => $payment->invoice->total_amount - $payment->invoice->paid_amount,
        ];
    }

    /**
     * Prepare contract data for notification
     */
    protected function prepareContractData(Contract $contract): array
    {
        return [
            'contract_id' => $contract->id,
            'tenant_name' => $contract->tenant->name,
            'property' => $contract->room->property->name,
            'room' => $contract->room->room_number,
            'end_date' => $contract->end_date->format('Y-m-d'),
            'monthly_rent' => $contract->monthly_rent,
        ];
    }

    /**
     * Get invoice created message
     */
    protected function getInvoiceCreatedMessage(Invoice $invoice): string
    {
        return "A new invoice #{$invoice->invoice_number} for {$invoice->total_amount} has been generated. " .
               "Payment is due by {$invoice->due_date->format('Y-m-d')}.";
    }

    /**
     * Get invoice reminder message
     */
    protected function getInvoiceReminderMessage(Invoice $invoice, int $daysUntilDue): string
    {
        return "Reminder: Invoice #{$invoice->invoice_number} for {$invoice->total_amount} " .
               "is due in {$daysUntilDue} days ({$invoice->due_date->format('Y-m-d')}).";
    }

    /**
     * Get overdue invoice message
     */
    protected function getOverdueInvoiceMessage(Invoice $invoice, int $daysOverdue): string
    {
        $balance = $invoice->total_amount - $invoice->paid_amount;
        return "Invoice #{$invoice->invoice_number} is {$daysOverdue} days overdue. " .
               "Outstanding balance: {$balance}. Please make payment immediately.";
    }

    /**
     * Get payment received message
     */
    protected function getPaymentReceivedMessage(Payment $payment): string
    {
        $balance = $payment->invoice->total_amount - $payment->invoice->paid_amount;
        $status = $balance <= 0 ? 'fully paid' : 'partially paid';

        return "Payment of {$payment->amount} received for invoice #{$payment->invoice->invoice_number}. " .
               "Invoice is now {$status}. " .
               ($balance > 0 ? "Remaining balance: {$balance}" : "Thank you for your payment!");
    }

    /**
     * Get payment received message for landlord
     */
    protected function getPaymentReceivedMessageForLandlord(Payment $payment): string
    {
        $tenant = $payment->invoice->contract->tenant;
        return "Payment of {$payment->amount} received from {$tenant->name} " .
               "for invoice #{$payment->invoice->invoice_number}.";
    }

    /**
     * Get contract expiring message
     */
    protected function getContractExpiringMessage(Contract $contract, int $daysUntilExpiry): string
    {
        return "Your rental contract for {$contract->room->property->name} - Room {$contract->room->room_number} " .
               "will expire in {$daysUntilExpiry} days ({$contract->end_date->format('Y-m-d')}). " .
               "Please contact your landlord to discuss renewal options.";
    }

    /**
     * Get contract expiring message for landlord
     */
    protected function getContractExpiringMessageForLandlord(Contract $contract, int $daysUntilExpiry): string
    {
        return "Contract for {$contract->tenant->name} in {$contract->room->property->name} - " .
               "Room {$contract->room->room_number} will expire in {$daysUntilExpiry} days.";
    }

    /**
     * Get welcome message
     */
    protected function getWelcomeMessage(User $tenant, Contract $contract): string
    {
        return "Welcome to RoomGate, {$tenant->name}! Your rental at {$contract->room->property->name} - " .
               "Room {$contract->room->room_number} is now active. " .
               "Monthly rent: {$contract->monthly_rent}. " .
               "We're here to make your rental experience smooth and convenient.";
    }

    /**
     * Get user notifications
     */
    public function getUserNotifications(User $user, bool $unreadOnly = false): Collection
    {
        $query = DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->orderBy('created_at', 'desc');

        if ($unreadOnly) {
            $query->whereNull('read_at');
        }

        return collect($query->get());
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(string $notificationId): bool
    {
        return DB::table('notifications')
            ->where('id', $notificationId)
            ->update(['read_at' => now()]) > 0;
    }

    /**
     * Mark all notifications as read for user
     */
    public function markAllAsRead(User $user): int
    {
        return DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}