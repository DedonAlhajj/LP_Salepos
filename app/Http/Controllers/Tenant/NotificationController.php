<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Mail\UserDetails;
use App\Models\CustomNotification;
use App\Services\Tenant\NotificationService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class NotificationController extends Controller
{

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of employees.
     *
     * @return View|RedirectResponse
     */
    public function index(): View|RedirectResponse
    {
        try {
            // Check authorization for managing all_notification
            $this->authorize('all_notification');

            // Retrieve all notifications records using the optimized service method
            $notifications = $this->notificationService->getNotifications();

            return view('Tenant.notification.index', compact('notifications'));
        } catch (\Exception $e) {
            // Return an error message if fetching notifications fails
            return back()->with('not_permitted', 'Failed to load notification. Please try again.');
        }
    }


    public function resendFailedNotifications(){

        $failedNotifications = CustomNotification::where('status', 'failed')->get();
        $this->notificationService->resendFailedNotifications($failedNotifications);
    }

    public function resendFailedNotificationsBatchId($batchId){

        $failedNotifications = CustomNotification::where('batch_id', $batchId)
            ->where('status', 'failed')
            ->get();
        $this->notificationService->resendFailedNotifications($failedNotifications);
    }

    public function store(Request $request)
    {

        try {
    	$user = User::find($request->receiver_id);
        $data = [
            'title' => 'إعادة تعيين كلمة المرور',
            'message' => 'اضغط على الرابط أدناه لإعادة تعيين كلمة المرور الخاصة بك.',
            'name' => 'dsmlmv',
            'password' => "987896",
            'email' => 'user@example.com',
            'mailableClass' => UserDetails::class
        ];
        $user->notify(new \App\Notifications\CustomNotification($data, ['mail', 'in_app'],Auth::id()));
    	return redirect()->back()->with('message', 'Notification send successfully');
        } catch (\Exception $e) {
            // Return an error message if fetching notifications fails
            return back()->with('not_permitted', $e->getMessage());
        }
    }

    public function markAsRead()
    {
        auth()->user()->notifications()->whereNull('read_at')->update(['read_at' => now()]);
    }
}
