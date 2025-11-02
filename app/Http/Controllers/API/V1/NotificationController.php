<?php

namespace App\Http\Controllers\API\V1;

use App\Services\Notification\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotificationController extends BaseController
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get all notifications for the authenticated user
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'per_page' => 'nullable|integer|min:1|max:100',
            'type' => 'nullable|string',
            'unread_only' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $user = Auth::user();
            $perPage = $request->input('per_page', 20);
            $unreadOnly = $request->input('unread_only', false);

            $query = DB::table('notifications')
                ->where('notifiable_id', $user->id)
                ->where('notifiable_type', get_class($user))
                ->when($unreadOnly, function ($q) {
                    $q->whereNull('read_at');
                })
                ->when($request->has('type'), function ($q) use ($request) {
                    $q->where('type', 'like', '%' . $request->type . '%');
                })
                ->orderBy('created_at', 'desc');

            $notifications = $query->paginate($perPage);

            // Transform notifications
            $notifications->getCollection()->transform(function ($notification) {
                $data = json_decode($notification->data, true);
                return [
                    'id' => $notification->id,
                    'type' => $this->getNotificationType($notification->type),
                    'subject' => $data['subject'] ?? '',
                    'message' => $data['message'] ?? '',
                    'details' => $data['details'] ?? [],
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at,
                    'is_read' => $notification->read_at !== null,
                ];
            });

            return $this->sendPaginatedResponse($notifications, 'Notifications retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve notifications', [$e->getMessage()], 500);
        }
    }

    /**
     * Get unread notifications
     */
    public function getUnread()
    {
        try {
            $user = Auth::user();

            $notifications = $this->notificationService->getUserNotifications($user, true);

            $data = $notifications->map(function ($notification) {
                $notificationData = json_decode($notification->data, true);
                return [
                    'id' => $notification->id,
                    'type' => $this->getNotificationType($notification->type),
                    'subject' => $notificationData['subject'] ?? '',
                    'message' => $notificationData['message'] ?? '',
                    'details' => $notificationData['details'] ?? [],
                    'created_at' => $notification->created_at,
                ];
            });

            $response = [
                'notifications' => $data,
                'unread_count' => $data->count(),
            ];

            return $this->sendResponse($response, 'Unread notifications retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve unread notifications', [$e->getMessage()], 500);
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        try {
            $user = Auth::user();

            // Verify notification belongs to user
            $notification = DB::table('notifications')
                ->where('id', $id)
                ->where('notifiable_id', $user->id)
                ->where('notifiable_type', get_class($user))
                ->first();

            if (!$notification) {
                return $this->sendError('Notification not found', [], 404);
            }

            $result = $this->notificationService->markAsRead($id);

            if ($result) {
                return $this->sendResponse(null, 'Notification marked as read');
            }

            return $this->sendError('Failed to mark notification as read', [], 500);
        } catch (\Exception $e) {
            return $this->sendError('Failed to mark notification as read', [$e->getMessage()], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        try {
            $user = Auth::user();
            $count = $this->notificationService->markAllAsRead($user);

            return $this->sendResponse(['marked_count' => $count], 'All notifications marked as read');
        } catch (\Exception $e) {
            return $this->sendError('Failed to mark notifications as read', [$e->getMessage()], 500);
        }
    }

    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();

            // Verify notification belongs to user
            $notification = DB::table('notifications')
                ->where('id', $id)
                ->where('notifiable_id', $user->id)
                ->where('notifiable_type', get_class($user))
                ->first();

            if (!$notification) {
                return $this->sendError('Notification not found', [], 404);
            }

            DB::table('notifications')->where('id', $id)->delete();

            return $this->sendResponse(null, 'Notification deleted successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to delete notification', [$e->getMessage()], 500);
        }
    }

    /**
     * Get notification preferences
     */
    public function getPreferences()
    {
        try {
            $user = Auth::user();

            // Get or create default preferences
            $preferences = DB::table('notification_preferences')
                ->where('user_id', $user->id)
                ->first();

            if (!$preferences) {
                // Create default preferences
                $preferences = $this->createDefaultPreferences($user->id);
            }

            $data = [
                'email_notifications' => [
                    'enabled' => (bool) $preferences->email_enabled,
                    'invoice_created' => (bool) $preferences->email_invoice_created,
                    'invoice_reminder' => (bool) $preferences->email_invoice_reminder,
                    'invoice_overdue' => (bool) $preferences->email_invoice_overdue,
                    'payment_received' => (bool) $preferences->email_payment_received,
                    'contract_expiring' => (bool) $preferences->email_contract_expiring,
                    'maintenance_update' => (bool) $preferences->email_maintenance_update,
                ],
                'sms_notifications' => [
                    'enabled' => (bool) $preferences->sms_enabled,
                    'invoice_reminder' => (bool) $preferences->sms_invoice_reminder,
                    'invoice_overdue' => (bool) $preferences->sms_invoice_overdue,
                    'payment_received' => (bool) $preferences->sms_payment_received,
                    'contract_expiring' => (bool) $preferences->sms_contract_expiring,
                ],
                'push_notifications' => [
                    'enabled' => (bool) $preferences->push_enabled,
                    'all_notifications' => (bool) $preferences->push_all,
                ],
                'quiet_hours' => [
                    'enabled' => (bool) $preferences->quiet_hours_enabled,
                    'start_time' => $preferences->quiet_hours_start,
                    'end_time' => $preferences->quiet_hours_end,
                ],
                'frequency' => [
                    'invoice_reminders' => $preferences->reminder_frequency ?? 'weekly',
                    'digest' => $preferences->digest_frequency ?? 'daily',
                ],
            ];

            return $this->sendResponse($data, 'Notification preferences retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve preferences', [$e->getMessage()], 500);
        }
    }

    /**
     * Update notification preferences
     */
    public function updatePreferences(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email_notifications' => 'nullable|array',
            'email_notifications.enabled' => 'nullable|boolean',
            'email_notifications.invoice_created' => 'nullable|boolean',
            'email_notifications.invoice_reminder' => 'nullable|boolean',
            'email_notifications.invoice_overdue' => 'nullable|boolean',
            'email_notifications.payment_received' => 'nullable|boolean',
            'email_notifications.contract_expiring' => 'nullable|boolean',
            'email_notifications.maintenance_update' => 'nullable|boolean',

            'sms_notifications' => 'nullable|array',
            'sms_notifications.enabled' => 'nullable|boolean',
            'sms_notifications.invoice_reminder' => 'nullable|boolean',
            'sms_notifications.invoice_overdue' => 'nullable|boolean',
            'sms_notifications.payment_received' => 'nullable|boolean',
            'sms_notifications.contract_expiring' => 'nullable|boolean',

            'push_notifications' => 'nullable|array',
            'push_notifications.enabled' => 'nullable|boolean',
            'push_notifications.all_notifications' => 'nullable|boolean',

            'quiet_hours' => 'nullable|array',
            'quiet_hours.enabled' => 'nullable|boolean',
            'quiet_hours.start_time' => 'nullable|date_format:H:i',
            'quiet_hours.end_time' => 'nullable|date_format:H:i',

            'frequency' => 'nullable|array',
            'frequency.invoice_reminders' => 'nullable|in:daily,weekly,monthly',
            'frequency.digest' => 'nullable|in:daily,weekly,never',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $user = Auth::user();

            // Build update data
            $updateData = [];

            if ($request->has('email_notifications')) {
                $email = $request->email_notifications;
                if (isset($email['enabled'])) $updateData['email_enabled'] = $email['enabled'];
                if (isset($email['invoice_created'])) $updateData['email_invoice_created'] = $email['invoice_created'];
                if (isset($email['invoice_reminder'])) $updateData['email_invoice_reminder'] = $email['invoice_reminder'];
                if (isset($email['invoice_overdue'])) $updateData['email_invoice_overdue'] = $email['invoice_overdue'];
                if (isset($email['payment_received'])) $updateData['email_payment_received'] = $email['payment_received'];
                if (isset($email['contract_expiring'])) $updateData['email_contract_expiring'] = $email['contract_expiring'];
                if (isset($email['maintenance_update'])) $updateData['email_maintenance_update'] = $email['maintenance_update'];
            }

            if ($request->has('sms_notifications')) {
                $sms = $request->sms_notifications;
                if (isset($sms['enabled'])) $updateData['sms_enabled'] = $sms['enabled'];
                if (isset($sms['invoice_reminder'])) $updateData['sms_invoice_reminder'] = $sms['invoice_reminder'];
                if (isset($sms['invoice_overdue'])) $updateData['sms_invoice_overdue'] = $sms['invoice_overdue'];
                if (isset($sms['payment_received'])) $updateData['sms_payment_received'] = $sms['payment_received'];
                if (isset($sms['contract_expiring'])) $updateData['sms_contract_expiring'] = $sms['contract_expiring'];
            }

            if ($request->has('push_notifications')) {
                $push = $request->push_notifications;
                if (isset($push['enabled'])) $updateData['push_enabled'] = $push['enabled'];
                if (isset($push['all_notifications'])) $updateData['push_all'] = $push['all_notifications'];
            }

            if ($request->has('quiet_hours')) {
                $quiet = $request->quiet_hours;
                if (isset($quiet['enabled'])) $updateData['quiet_hours_enabled'] = $quiet['enabled'];
                if (isset($quiet['start_time'])) $updateData['quiet_hours_start'] = $quiet['start_time'];
                if (isset($quiet['end_time'])) $updateData['quiet_hours_end'] = $quiet['end_time'];
            }

            if ($request->has('frequency')) {
                $freq = $request->frequency;
                if (isset($freq['invoice_reminders'])) $updateData['reminder_frequency'] = $freq['invoice_reminders'];
                if (isset($freq['digest'])) $updateData['digest_frequency'] = $freq['digest'];
            }

            $updateData['updated_at'] = now();

            // Update or create preferences
            DB::table('notification_preferences')->updateOrInsert(
                ['user_id' => $user->id],
                $updateData
            );

            return $this->sendResponse(null, 'Notification preferences updated successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to update preferences', [$e->getMessage()], 500);
        }
    }

    /**
     * Handle SMS delivery webhook
     */
    public function handleSmsDelivery(Request $request)
    {
        try {
            // Log SMS delivery status
            \Log::info('SMS delivery webhook received', $request->all());

            // You would handle specific provider webhooks here
            // Example: Twilio, Nexmo, etc.

            $messageId = $request->input('MessageSid') ?? $request->input('message_id');
            $status = $request->input('MessageStatus') ?? $request->input('status');
            $to = $request->input('To') ?? $request->input('to');

            // Update delivery status in your database if tracking
            if ($messageId) {
                DB::table('sms_logs')
                    ->where('message_id', $messageId)
                    ->update([
                        'delivery_status' => $status,
                        'delivered_at' => now(),
                        'updated_at' => now(),
                    ]);
            }

            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            \Log::error('SMS delivery webhook error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Test notification
     */
    public function testNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'channel' => 'required|in:email,sms,push,database',
            'type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $user = Auth::user();

            // Send test notification
            $this->notificationService->sendNotification(
                $user,
                $request->type,
                $request->channel,
                'Test Notification',
                'This is a test notification sent from RoomGate.',
                ['test' => true, 'timestamp' => now()->toIso8601String()]
            );

            return $this->sendResponse(null, 'Test notification sent successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to send test notification', [$e->getMessage()], 500);
        }
    }

    /**
     * Subscribe to push notifications
     */
    public function subscribePush(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_token' => 'required|string',
            'device_type' => 'required|in:ios,android,web',
            'device_name' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $user = Auth::user();

            // Store or update device token
            DB::table('push_subscriptions')->updateOrInsert(
                [
                    'user_id' => $user->id,
                    'device_token' => $request->device_token,
                ],
                [
                    'device_type' => $request->device_type,
                    'device_name' => $request->device_name ?? 'Unknown Device',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            return $this->sendResponse(null, 'Push notification subscription successful');
        } catch (\Exception $e) {
            return $this->sendError('Failed to subscribe to push notifications', [$e->getMessage()], 500);
        }
    }

    /**
     * Unsubscribe from push notifications
     */
    public function unsubscribePush(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $user = Auth::user();

            DB::table('push_subscriptions')
                ->where('user_id', $user->id)
                ->where('device_token', $request->device_token)
                ->update(['is_active' => false, 'updated_at' => now()]);

            return $this->sendResponse(null, 'Push notification unsubscription successful');
        } catch (\Exception $e) {
            return $this->sendError('Failed to unsubscribe from push notifications', [$e->getMessage()], 500);
        }
    }

    /**
     * Get notification statistics
     */
    public function getStatistics()
    {
        try {
            $user = Auth::user();

            $totalNotifications = DB::table('notifications')
                ->where('notifiable_id', $user->id)
                ->where('notifiable_type', get_class($user))
                ->count();

            $unreadNotifications = DB::table('notifications')
                ->where('notifiable_id', $user->id)
                ->where('notifiable_type', get_class($user))
                ->whereNull('read_at')
                ->count();

            $notificationsByType = DB::table('notifications')
                ->where('notifiable_id', $user->id)
                ->where('notifiable_type', get_class($user))
                ->select(DB::raw('type, COUNT(*) as count'))
                ->groupBy('type')
                ->get()
                ->map(function ($item) {
                    return [
                        'type' => $this->getNotificationType($item->type),
                        'count' => $item->count,
                    ];
                });

            $lastNotification = DB::table('notifications')
                ->where('notifiable_id', $user->id)
                ->where('notifiable_type', get_class($user))
                ->latest('created_at')
                ->first();

            $data = [
                'total_notifications' => $totalNotifications,
                'unread_notifications' => $unreadNotifications,
                'read_notifications' => $totalNotifications - $unreadNotifications,
                'read_percentage' => $totalNotifications > 0
                    ? round((($totalNotifications - $unreadNotifications) / $totalNotifications) * 100, 2)
                    : 0,
                'by_type' => $notificationsByType,
                'last_notification_at' => $lastNotification ? $lastNotification->created_at : null,
            ];

            return $this->sendResponse($data, 'Notification statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve statistics', [$e->getMessage()], 500);
        }
    }

    // ==================== Helper Methods ====================

    /**
     * Create default notification preferences
     */
    protected function createDefaultPreferences($userId)
    {
        $preferences = [
            'user_id' => $userId,
            'email_enabled' => true,
            'email_invoice_created' => true,
            'email_invoice_reminder' => true,
            'email_invoice_overdue' => true,
            'email_payment_received' => true,
            'email_contract_expiring' => true,
            'email_maintenance_update' => true,
            'sms_enabled' => false,
            'sms_invoice_reminder' => false,
            'sms_invoice_overdue' => false,
            'sms_payment_received' => false,
            'sms_contract_expiring' => false,
            'push_enabled' => true,
            'push_all' => true,
            'quiet_hours_enabled' => false,
            'quiet_hours_start' => '22:00',
            'quiet_hours_end' => '07:00',
            'reminder_frequency' => 'weekly',
            'digest_frequency' => 'daily',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('notification_preferences')->insert($preferences);

        return (object) $preferences;
    }

    /**
     * Get notification type from class name
     */
    protected function getNotificationType($className)
    {
        $type = class_basename($className);
        $type = str_replace('Notification', '', $type);
        return snake_case($type);
    }
}