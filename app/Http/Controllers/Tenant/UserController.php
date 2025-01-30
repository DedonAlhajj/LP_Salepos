<?php

namespace App\Http\Controllers\Tenant;

use App\Actions\SendMailAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\RegisterTenantRequest;
use App\Services\Tenant\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    protected $userService;
    protected $sendMailAction;

    public function __construct(UserService $userService, SendMailAction $sendMailAction)
    {
        $this->userService = $userService;
        $this->sendMailAction = $sendMailAction;
    }

    public function index()
    {
        $users = $this->userService->getAllUsers();
        return view('Tenant.user.index', compact('users'));
    }

    public function create()
    {
        $data = $this->userService->getUserFormData();
        return view('Tenant.user.create', $data);
    }

    public function store(RegisterTenantRequest $request)
    {
        try {
            $message = $this->userService->createUser($request->validated());
            return redirect('user')->with('message1', $message);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['message3' => 'Error while creating the account,try again.'])
                ->withInput();
        }
    }

    public function edit($id)
    {
        $data = $this->userService->getUserEditData($id);
        return view('Tenant.user.edit', $data);
    }

    public function update(RegisterTenantRequest $request, $id)
    {
        try {
            $this->userService->updateUser($id, $request->validated());
            return redirect('user')->with('message1', "Data updated successfully");
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['message3' => 'Error while updating the account,try again.'])
                ->withInput();
        }
    }

    public function profile($id)
    {
        $user = $this->userService->getUserById($id);
        return view('Tenant.user.profile', compact('user'));
    }

    public function profileUpdate(RegisterTenantRequest $request, $id)
    {
        try {
            $this->userService->updateProfile($id, $request->validated());
            return redirect()->back()->with('message1', "Data updated successfully");
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['message3' => 'Error while updating the profile,try again.'])
                ->withInput();
        }
    }

    public function changePassword(Request $request, $id)
    {
        return $this->userService->changeUserPassword($id, $request->all());
    }

    public function deleteBySelection(Request $request)
    {
        try {
            $this->userService->deleteUsers($request->input('userIdArray'));
            return response()->json('Users deleted successfully!');
        } catch (\Exception $e) {
            return response()->json('Error while deleted the account,try again.');

        }
    }

    public function destroy($id)
    {
        try {
            $this->userService->deleteUser($id);
            return redirect('user')->with('message1', __('Data deleted successfully'));
        } catch (\Exception $e) {
            return redirect('user')->with('message3', __('Error while deleted the data,try again.'));
        }

    }

    public function indexTrashed()
    {
        $users = $this->userService->getTrashedUsers();
        return view('Tenant.user.indexTrashed', compact('users'));
    }

    public function restore($id)
    {
        try {
            $this->userService->restoreUser($id);
            return redirect('user')->with('message1', __('Restored Data successfully'));
        } catch (\Exception $e) {
            return redirect('user')->with('message3', __('Error while restored the data,try again.'));
        }
    }










    public function notificationUsers()
    {
        $notification_users = DB::table('users')->where([
            ['is_active', true],
            ['id', '!=', \Auth::user()->id],
            ['role_id', '!=', '5']
        ])->get();

        $html = '';
        foreach($notification_users as $user){
            $html .='<option value="'.$user->id.'">'.$user->name . ' (' . $user->email. ')'.'</option>';
        }

        return response()->json($html);
    }

    public function allUsers()
    {
        $lims_user_list = DB::table('users')->where('is_active', true)->get();

        $html = '';
        foreach($lims_user_list as $user){
            $html .='<option value="'.$user->id.'">'.$user->name . ' (' . $user->phone. ')'.'</option>';
        }

        return response()->json($html);
    }
}
