<?php

namespace App\Http\Controllers\Tenant;

use App\DTOs\GeneralSettingCentralDTO;
use App\DTOs\GeneralSettingStoreDTO;
use App\DTOs\MailSettingDTO;
use App\DTOs\PosSettingDTO;
use App\DTOs\RewardPointSettingDTO;
use App\DTOs\SmsDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\HrmRequest;
use App\Http\Requests\Tenant\PosSettingRequest;
use App\Http\Requests\Tenant\SmsSettingRequest;
use App\Services\Tenant\DatabaseService;
use App\Services\Tenant\SettingServices;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;


class SettingController extends Controller
{

    private SettingServices $settingServices;
    private DatabaseService $databaseService;


    public function __construct(SettingServices $settingServices,DatabaseService $databaseService)
    {
        $this->settingServices = $settingServices;
        $this->databaseService = $databaseService;
    }

    /**
     * Empty the database while preserving essential tables.
     *
     * @return RedirectResponse
     */
    public function emptyDatabase(): RedirectResponse
    {
        // Prevent execution if the application is running in demo mode
        if (!config('app.demo_mode')) {
            return back()->with('not_permitted', 'This feature is disabled in demo mode.');
        }

        // Reset the database using the service
        $success = $this->databaseService->resetDatabase();

        return $success
            ? Redirect::back()->with('message', 'Database cleared successfully')
            : Redirect::back()->with('not_permitted', 'Database reset failed! Check logs.');
    }

    /**
     * Retrieve general settings for the tenant.
     *
     * @return View|RedirectResponse
     */
    public function generalSetting(): View|RedirectResponse
    {
        try {
            // Fetch general settings from the service
            $data = $this->settingServices->getGeneralSettings();

            return view('Tenant.setting.general_setting', [
                'lims_general_setting_data' => $data->generalSetting,
                'lims_account_list' => $data->accounts,
                'lims_currency_list' => $data->currencies,
                'zones_array' => $data->timezones
            ]);
        } catch (\Exception $e) {
            return back()->with('not_permitted', "Failed to load settings: " . $e->getMessage());
        }
    }

    /**
     * Store and update general settings for the tenant.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function generalSettingStore(Request $request): RedirectResponse
    {
        try {
            // Prevent modification if the application is in demo mode
            if (!config('app.demo_mode')) {
                return back()->with('not_permitted', 'This feature is disabled in demo mode.');
            }

            // Validate input data
            $validatedData = $request->validate([
                'site_logo' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:100000',
            ]);

            // Convert request data to a DTO for structured handling
            $dto = GeneralSettingStoreDTO::fromRequest($request);

            // Update settings using the service
            $this->settingServices->updateGeneralSettings($dto);

            return redirect()->back()->with('message', 'Data updated successfully');
        } catch (\Exception $e) {
            return back()->with('not_permitted', 'An unexpected error occurred. Please try again.');
        }
    }

    /**
     * Retrieve general settings for the super admin.
     *
     * @return View|RedirectResponse
     */
    public function superadminGeneralSetting(): View|RedirectResponse
    {
        try {
            // Fetch general settings from the service
            $data = $this->settingServices->getGeneralSetting();
            return view('landlord.general_setting', compact('data'));
        } catch (\Exception $e) {
            return back()->with('not_permitted', $e->getMessage());
        }
    }

    /**
     * Store and update general settings for the super admin.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function superadminGeneralSettingStore(Request $request): RedirectResponse
    {
        // Prevent modification if the application is in demo mode
        if (!config('app.demo_mode')) {
            return back()->with('not_permitted', 'This feature is disabled in demo mode.');
        }

        try {
            // Validate input data
            $validatedData = $request->validate([
                'site_logo' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:10240',
                'og_image' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
            ]);

            // Convert request data into a structured DTO
            $dto = new GeneralSettingCentralDTO($request);

            // Update settings using the service
            $this->settingServices->updateGeneralSettingsCentral($dto);

            return redirect()->back()->with('message', 'Data updated successfully');
        } catch (\Exception $e) {
            return Redirect()->back()->with('not_permitted', 'Failed to update settings. Please try again.');
        }
    }

    /**
     * Retrieve reward point settings for the tenant.
     *
     * @return View|RedirectResponse
     */
    public function rewardPointSetting(): View|RedirectResponse
    {
        try {
            // Fetch reward point settings from the service
            $lims_reward_point_setting_data = $this->settingServices->rewardPointSetting();
            return view('Tenant.setting.reward_point_setting', compact('lims_reward_point_setting_data'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('not_permitted', $exception->getMessage());
        }
    }

    public function backup()
    {
        if (!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');

        // Database configuration
        $host = env('DB_HOST');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        if (!config('database.connections.saleprosaas_landlord'))
            $database_name = env('DB_DATABASE');
        else
            $database_name = env('DB_PREFIX') . $this->getTenantId();

        // Get connection object and set the charset
        $conn = mysqli_connect($host, $username, $password, $database_name);
        $conn->set_charset("utf8");


        // Get All Table Names From the Database
        $tables = array();
        $sql = "SHOW TABLES";
        $result = mysqli_query($conn, $sql);

        while ($row = mysqli_fetch_row($result)) {
            $tables[] = $row[0];
        }

        $sqlScript = "";
        foreach ($tables as $table) {

            // Prepare SQLscript for creating table structure
            $query = "SHOW CREATE TABLE $table";
            $result = mysqli_query($conn, $query);
            $row = mysqli_fetch_row($result);

            $sqlScript .= "\n\n" . $row[1] . ";\n\n";


            $query = "SELECT * FROM $table";
            $result = mysqli_query($conn, $query);

            $columnCount = mysqli_num_fields($result);

            // Prepare SQLscript for dumping data for each table
            for ($i = 0; $i < $columnCount; $i++) {
                while ($row = mysqli_fetch_row($result)) {
                    $sqlScript .= "INSERT INTO $table VALUES(";
                    for ($j = 0; $j < $columnCount; $j++) {
                        $row[$j] = $row[$j];

                        if (isset($row[$j])) {
                            $sqlScript .= '"' . $row[$j] . '"';
                        } else {
                            $sqlScript .= '""';
                        }
                        if ($j < ($columnCount - 1)) {
                            $sqlScript .= ',';
                        }
                    }
                    $sqlScript .= ");\n";
                }
            }

            $sqlScript .= "\n";
        }

        if (!empty($sqlScript)) {
            // Save the SQL script to a backup file
            $backup_file_name = public_path() . '/' . $database_name . '_backup_' . time() . '.sql';
            //return $backup_file_name;
            $fileHandler = fopen($backup_file_name, 'w+');
            $number_of_lines = fwrite($fileHandler, $sqlScript);
            fclose($fileHandler);

            $zip = new ZipArchive();
            $zipFileName = $database_name . '_backup_' . time() . '.zip';
            $zip->open(public_path() . '/' . $zipFileName, ZipArchive::CREATE);
            $zip->addFile($backup_file_name, $database_name . '_backup_' . time() . '.sql');
            $zip->close();

            // Download the SQL backup file to the browser
            /*header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($backup_file_name));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($backup_file_name));
            ob_clean();
            flush();
            readfile($backup_file_name);
            exec('rm ' . $backup_file_name); */
        }
        return redirect('public/' . $zipFileName);
    }

    /**
     * Store reward point settings.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rewardPointSettingStore(Request $request): RedirectResponse
    {
        try {
            // Convert request data to DTO for better data handling
            $dto = RewardPointSettingDTO::fromRequest($request);

            // Store reward point settings using service layer
            $this->settingServices->storeRewardPointSetting($dto);

            return redirect()->back()->with('message', 'Reward point setting updated successfully');
        } catch (\Exception $exception) {
            return redirect()->back()->with('not_permitted', $exception->getMessage());
        }
    }

    /**
     * Change the theme settings.
     *
     * @param string $theme
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeTheme($theme): \Illuminate\Http\JsonResponse
    {
        try {
            // Update theme setting using the service layer
            $this->settingServices->changeTheme($theme);

            return response()->json('Change Theme updated successfully');
        } catch (\Exception $exception) {
            return response()->json('An unexpected error occurred. Please try again.');
        }
    }

    /**
     * Display mail settings page.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function mailSetting(): \Illuminate\Contracts\View\View|RedirectResponse
    {
        try {
            // Fetch mail settings using service layer
            $mail_setting_data = $this->settingServices->mailSetting();

            return view('Tenant.setting.mail_setting', compact('mail_setting_data'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('not_permitted', $exception->getMessage());
        }
    }

    /**
     * Store mail settings.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function mailSettingStore(Request $request): RedirectResponse
    {
        try {
            // Convert request data to DTO for better handling
            $dto = MailSettingDTO::fromRequest($request);

            // Store mail settings using service layer
            $this->settingServices->storeMailSettings($dto);

            return redirect()->back()->with('message', 'Data updated successfully');
        } catch (\Exception $e) {
            return back()->with('not_permitted', 'An unexpected error occurred. Please try again.');
        }
    }

    /**
     * Display SMS settings page.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function smsSetting(): \Illuminate\Contracts\View\View|RedirectResponse
    {
        try {
            // Fetch SMS settings using service layer
            $smsSettings = $this->settingServices->getSmsSettings();

            return view('Tenant.setting.sms_setting', ['smsSettings' => $smsSettings]);
        } catch (\Exception $exception) {
            return redirect()->back()->with('not_permitted', $exception->getMessage());
        }
    }

    /**
     * Store SMS settings.
     *
     * @param SmsSettingRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function smsSettingStore(SmsSettingRequest $request): RedirectResponse
    {
        // Prevent modification if the application is in demo mode
        if (!config('app.demo_mode')) {
            return back()->with('not_permitted', 'This feature is disabled in demo mode.');
        }

        try {
            // Validate request data
            $data = $request->validated();

            // Store SMS settings using service layer
            $this->settingServices->getSmsSettingsStore($data);

            return redirect()->back()->with('message', 'Data updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('not_permitted', 'Failed to update settings: ' . $e->getMessage());
        }
    }

    /**
     * Display the create SMS template page.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function createSms(): \Illuminate\Contracts\View\View|RedirectResponse
    {
        try {
            // Fetch necessary data for creating an SMS template
            $data = $this->settingServices->createSms();

            return view('Tenant.setting.create_sms', $data);
        } catch (\Exception $e) {
            return redirect()->back()->with('not_permitted', 'Error occurred, please try again.');
        }
    }

    /**
     * Send an SMS message to a specified mobile number.
     *
     * @param Request $request The request containing SMS details (message and mobile number).
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse JSON response if AJAX, otherwise redirects back with a success or failure message.
     */
    public function sendSms(Request $request): \Illuminate\Http\JsonResponse|RedirectResponse
    {
        try {
            // Validate the input data
            $validated = $request->validate([
                'message' => 'required|string|max:160', // SMS message (max 160 chars)
                'mobile' => 'required|string' // Mobile number (can be improved with regex validation)
            ]);

            // Create DTO for structured data handling
            $smsDTO = SmsDTO::fromRequest($validated, $this->settingServices->getProviderDetails());

            // Send the SMS using the service
            $success = $this->settingServices->sendSms($smsDTO);

            // Handle response based on request type
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => $success ? 'SMS sent successfully' : 'Failed to send SMS'
                ], $success ? 200 : 500);
            }

            return redirect()->back()->with('message', $success ? 'SMS sent successfully' : 'Failed to send SMS');
        } catch (\Throwable $e) {
            if ($request->wantsJson()) {
                return response()->json(['not_permitted' => 'An error occurred while sending SMS'], 500);
            }

            return redirect()->back()->with('not_permitted', 'An error occurred while sending SMS')->withInput();
        }
    }

    /**
     * Retrieve HRM (Human Resource Management) settings.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View View with HRM settings or redirect with an error message.
     */
    public function hrmSetting(): View|RedirectResponse
    {
        try {
            $lims_hrm_setting_data = $this->settingServices->hrmSetting();
            return view('Tenant.setting.hrm_setting', compact('lims_hrm_setting_data'));
        } catch (\Exception $e) {
            return back()->with('not_permitted', 'Error loading HRM settings. Please try again.');
        }
    }

    /**
     * Store or update HRM settings.
     *
     * @param HrmRequest $request The validated request containing HRM settings data.
     * @return \Illuminate\Http\RedirectResponse Redirects back with a success or failure message.
     */
    public function hrmSettingStore(HrmRequest $request): RedirectResponse
    {
        try {
            $this->settingServices->updateHrmSettings($request->validated());
            return redirect()->back()->with('message', 'HRM setting updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('not_permitted', 'Error updating HRM settings. Please try again.');
        }
    }

    /**
     * Retrieve POS (Point of Sale) settings.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View View with POS settings or redirect with an error message.
     */
    public function posSetting(): View|RedirectResponse
    {
        try {
            $posSettings = $this->settingServices->getPosSettings();
            return view('Tenant.setting.pos_setting', [
                'lims_customer_list' => $posSettings['customers'],
                'lims_warehouse_list' => $posSettings['warehouses'],
                'lims_biller_list' => $posSettings['billers'],
                'lims_pos_setting_data' => $posSettings['posSetting'],
                'options' => $posSettings['options']
            ]);
        } catch (\Exception $e) {
            return back()->with('not_permitted', 'Error loading POS settings. Please try again.');
        }
    }

    /**
     * Store or update POS settings.
     *
     * @param PosSettingRequest $request The validated request containing POS settings data.
     * @return \Illuminate\Http\RedirectResponse Redirects back with a success or failure message.
     */
    public function posSettingStore(PosSettingRequest $request): RedirectResponse
    {
        // Prevent modification if the application is in demo mode
        if (!config('app.demo_mode')) {
            return back()->with('not_permitted', 'This feature is disabled in demo mode.');
        }
        try {
            // Convert request data to DTO format
            $dto = PosSettingDTO::fromRequest($request->validated());
            $success = $this->settingServices->updatePosSettings($dto);

            return redirect()->back()->with('message', 'POS setting updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('not_permitted', 'Error updating POS settings. Please try again.');
        }
    }
}
