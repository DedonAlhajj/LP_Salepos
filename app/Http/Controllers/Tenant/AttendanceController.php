<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreAttendanceRequest;
use App\Services\Tenant\AttendanceService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\HrmSetting;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Display the attendance index page with attendance data.
     *
     * This method retrieves the attendance data for the authenticated user
     * and returns the view for the attendance index page.
     * If there is an error fetching the data, an error message is displayed.
     *
     * @return View|RedirectResponse
     */
    public function index(): View|RedirectResponse
    {
        try {
            // Authorization check: Ensure the user has the 'attendance' permission
            $this->authorize('attendance');

            // Get attendance data for the logged-in user from the service
            $attendanceData = $this->attendanceService->getAttendanceData(Auth::user());

            // Return the view with the attendance data
            return view('Tenant.attendance.index', $attendanceData);
        } catch (\Exception $e) {
            // Redirect back with an error message if something goes wrong
            return redirect()->back()->withErrors(['not_permitted' => __('An error occurred while loading attendance data.')]);
        }
    }

    /**
     * Store new attendance data in the system.
     *
     * This method validates the incoming request data and stores the attendance record.
     * If the process is successful, a success message is displayed.
     * If there is an error during the process, an error message is shown.
     *
     * @param StoreAttendanceRequest $request
     * @return RedirectResponse
     */
    public function store(StoreAttendanceRequest $request): RedirectResponse
    {
        try {
            // Pass the validated request data to the service for storage
            $this->attendanceService->storeAttendance($request->validated());

            // Redirect back with a success message
            return redirect()->back()->with('message', 'Attendance created successfully');
        } catch (\Exception $e) {
            // Redirect back with an error message if something goes wrong
            return redirect()->back()->with('error', 'Failed to create attendance, please try again.');
        }
    }

    /**
     * Delete multiple attendance records based on the selected IDs.
     *
     * This method accepts an array of selected attendance IDs and passes them to the service
     * for deletion. If the deletion is successful, a success message is returned.
     * If an error occurs, a failure message is returned.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteBySelection(Request $request): JsonResponse
    {
        try {
            // Pass the selected Attendance IDs to the service for deletion
            $this->attendanceService->deleteAttendances($request->input('attendanceSelectedArray'));

            // Return a success message as a JSON response
            return response()->json('Attendance deleted successfully!');
        } catch (\Exception $e) {
            // Handle any exceptions and return a failure message as a JSON response
            return response()->json('Failed to delete Attendance!');
        }
    }

    /**
     * Delete a single attendance record by date and employee ID.
     *
     * This method deletes the attendance record for a specific employee on a specific date.
     * If successful, a success message is displayed. If an error occurs, an error message is shown.
     *
     * @param string $date
     * @param int $employee_id
     * @return RedirectResponse
     */
    public function delete(string $date, int $employee_id): RedirectResponse
    {
        try {
            // Call the service to delete the Attendance with the specified date and employee ID
            $this->attendanceService->deleteAttendance($date, $employee_id);

            // Redirect back with a success message
            return redirect()->back()->with('message', 'Attendance deleted successfully');
        } catch (\Exception $e) {
            // Handle any exceptions and redirect back with a failure message
            return redirect()->back()->with(['not_permitted' => 'Failed to delete Attendance. ' . $e->getMessage()]);
        }
    }


    public function importDeviceCsv(Request $request)
    {
        $upload = $request->file('file');
        if ($request->Attendance_Device_date_format == null || $upload == null) {
            return redirect()->back()->with('not_permitted', 'Please select Attendance Device Date Format and upload a CSV file');
        }

        $ext = pathinfo($upload->getClientOriginalName(), PATHINFO_EXTENSION);
        if($ext != 'csv')
            return redirect()->back()->with('not_permitted', 'Please upload a CSV file');

        $filename =  $upload->getClientOriginalName();
        $filePath=$upload->getRealPath();
        //open and read
        $file=fopen($filePath, 'r');
        $exclude_header= fgetcsv($file);

        $employee_all = Employee::all();
        $lims_hrm_setting_data = HrmSetting::latest()->first();
        $checkin = $lims_hrm_setting_data->checkin;
        $data = [];
        //looping through other columns
        while($columns=fgetcsv($file))
        {
            if($columns[0]=="" || $columns[1]=="")
                continue;

            $staff_id = $columns[0];
            $employee = $employee_all->where('staff_id', $staff_id)->first();
            if (!$employee)
                return redirect()->back()->with('not_permitted', 'Staff id - '. $staff_id. ' is not available within the POS system');

            $dt_time = explode(' ', $columns[1], 2);
            $attendance_date = Carbon::createFromFormat($request->Attendance_Device_date_format, $dt_time[0])->format('Y-m-d');
            $attendance_time = str_replace(' ','',$dt_time[1]);
            $i = 0;
            $status = 0;
            foreach ($data as $key => $dt) {
                if ($dt['date'] == $attendance_date && $dt['employee_id'] == $employee->id) {
                    $status = $dt['status'];
                    $i++;
                    if ($dt['checkout'] == null) {
                        $data[$key]['checkout'] =  $attendance_time;
                        $i = -1;
                        break;
                    }
                }
            }
            //checkout update
            if ($i == -1) {
                continue;
            }
            //create attendance at first time for the employee and date
            elseif ($i == 0) {
                $diff = strtotime($checkin) - strtotime($attendance_time);
                if($diff >= 0)
                    $status = 1;
                else
                    $status = 0;

                $data[] = ['date' => $attendance_date, 'employee_id' => $employee->id, 'user_id' => Auth::id(),
                    'checkin' => $attendance_time, 'checkout' => null, 'status' => $status];
            }
            //create attendance after first time
            else {
                $data[] = ['date' => $attendance_date, 'employee_id' => $employee->id, 'user_id' => Auth::id(),
                    'checkin' => $attendance_time, 'checkout' => null, 'status' => $status];
            }
        }
        //create composite via migration with this 2nd array parameter
        Attendance::upsert($data, ['date','employee_id','checkin'], ['checkout']);
        return redirect()->back()->with('message', 'Attendance created successfully');
    }



}
