<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        $createdAt = Carbon::parse($this->created_at);

        return [
            'id' => $this->id,
            'type' => $this->type,
            'notifiable_type' => class_basename($this->notifiable_type),
            'notifiable_id' => $this->notifiable_id,
            'data' => $this->data,
            'title' => $this->data['title'] ?? $this->getDefaultTitle(),
            'message' => $this->data['message'] ?? $this->getDefaultMessage(),
            'action_url' => $this->data['action_url'] ?? null,
            'action_text' => $this->data['action_text'] ?? null,
            'icon' => $this->data['icon'] ?? $this->getDefaultIcon(),
            'priority' => $this->data['priority'] ?? 'normal',
            'category' => $this->getCategory(),
            'read_at' => $this->read_at,
            'is_read' => !is_null($this->read_at),
            'time_ago' => $createdAt->diffForHumans(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Get default title based on notification type
     *
     * @return string
     */
    private function getDefaultTitle(): string
    {
        return match($this->type) {
            'App\\Notifications\\InvoiceCreated' => 'New Invoice',
            'App\\Notifications\\PaymentReceived' => 'Payment Received',
            'App\\Notifications\\ContractExpiring' => 'Contract Expiring Soon',
            'App\\Notifications\\ContractRenewed' => 'Contract Renewed',
            'App\\Notifications\\MaintenanceRequest' => 'Maintenance Request',
            'App\\Notifications\\RentReminder' => 'Rent Payment Reminder',
            'App\\Notifications\\OverdueNotice' => 'Overdue Payment Notice',
            'App\\Notifications\\SubscriptionExpiring' => 'Subscription Expiring',
            'App\\Notifications\\WelcomeTenant' => 'Welcome',
            'App\\Notifications\\DocumentUploaded' => 'New Document',
            default => 'Notification',
        };
    }

    /**
     * Get default message based on notification type
     *
     * @return string
     */
    private function getDefaultMessage(): string
    {
        return match($this->type) {
            'App\\Notifications\\InvoiceCreated' => 'A new invoice has been generated for your account.',
            'App\\Notifications\\PaymentReceived' => 'Your payment has been received and processed.',
            'App\\Notifications\\ContractExpiring' => 'Your rental contract is expiring soon.',
            'App\\Notifications\\ContractRenewed' => 'Your rental contract has been renewed.',
            'App\\Notifications\\MaintenanceRequest' => 'A maintenance request requires your attention.',
            'App\\Notifications\\RentReminder' => 'Your rent payment is due soon.',
            'App\\Notifications\\OverdueNotice' => 'You have an overdue payment.',
            'App\\Notifications\\SubscriptionExpiring' => 'Your subscription is expiring soon.',
            'App\\Notifications\\WelcomeTenant' => 'Welcome to your new home!',
            'App\\Notifications\\DocumentUploaded' => 'A new document has been uploaded.',
            default => 'You have a new notification.',
        };
    }

    /**
     * Get default icon based on notification type
     *
     * @return string
     */
    private function getDefaultIcon(): string
    {
        return match($this->type) {
            'App\\Notifications\\InvoiceCreated' => 'receipt',
            'App\\Notifications\\PaymentReceived' => 'check-circle',
            'App\\Notifications\\ContractExpiring' => 'clock',
            'App\\Notifications\\ContractRenewed' => 'refresh',
            'App\\Notifications\\MaintenanceRequest' => 'wrench',
            'App\\Notifications\\RentReminder' => 'bell',
            'App\\Notifications\\OverdueNotice' => 'exclamation-triangle',
            'App\\Notifications\\SubscriptionExpiring' => 'credit-card',
            'App\\Notifications\\WelcomeTenant' => 'home',
            'App\\Notifications\\DocumentUploaded' => 'document',
            default => 'info-circle',
        };
    }

    /**
     * Get notification category
     *
     * @return string
     */
    private function getCategory(): string
    {
        return match($this->type) {
            'App\\Notifications\\InvoiceCreated',
            'App\\Notifications\\PaymentReceived',
            'App\\Notifications\\RentReminder',
            'App\\Notifications\\OverdueNotice' => 'billing',

            'App\\Notifications\\ContractExpiring',
            'App\\Notifications\\ContractRenewed' => 'contract',

            'App\\Notifications\\MaintenanceRequest' => 'maintenance',

            'App\\Notifications\\SubscriptionExpiring' => 'subscription',

            'App\\Notifications\\WelcomeTenant',
            'App\\Notifications\\DocumentUploaded' => 'general',

            default => 'other',
        };
    }
}