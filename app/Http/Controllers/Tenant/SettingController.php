<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\Warehouse;
use App\Models\Biller;
use App\Models\Account;
use App\Models\Currency;
use App\Models\ExternalService;
use App\Models\PosSetting;
use App\Models\MailSetting;
use App\Models\GeneralSetting;
use App\Models\HrmSetting;
use App\Models\RewardPointSetting;
use App\Models\SmsTemplate;
//use App\Services\SmsService;
use DB;
//use ZipArchive;
//use Twilio\Rest\Client;
//use Clickatell\Rest;
//use Clickatell\ClickatellException;

class SettingController extends Controller
{
    use \App\Traits\CacheForget;
    //use \App\Traits\TenantInfo;
    private $_smsService;

  /*  public function __construct(SmsService $smsService)
    {
        $this->_smsService = $smsService;
    }
*/
    public function emptyDatabase()
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        //clearing all the cached queries
        $this->cacheForget('biller_list');
        $this->cacheForget('brand_list');
        $this->cacheForget('category_list');
        $this->cacheForget('coupon_list');
        $this->cacheForget('customer_list');
        $this->cacheForget('customer_group_list');
        $this->cacheForget('product_list');
        $this->cacheForget('product_list_with_variant');
        $this->cacheForget('warehouse_list');
        $this->cacheForget('tax_list');
        $this->cacheForget('currency');
        $this->cacheForget('general_setting');
        $this->cacheForget('pos_setting');
        $this->cacheForget('user_role');
        $this->cacheForget('permissions');
        $this->cacheForget('role_has_permissions');
        $this->cacheForget('role_has_permissions_list');

        $tables = DB::select('SHOW TABLES');
        if(!config('database.connections.saleprosaas_landlord'))
            $database_name = env('DB_DATABASE');
        else
            $database_name = env('DB_PREFIX').$this->getTenantId();
        $str = 'Tables_in_'.$database_name;
        foreach ($tables as $table) {
            if($table->$str != 'accounts' && $table->$str != 'general_settings' && $table->$str != 'hrm_settings' && $table->$str != 'languages' && $table->$str != 'migrations' && $table->$str != 'password_resets' && $table->$str != 'permissions' && $table->$str != 'pos_setting' && $table->$str != 'roles' && $table->$str != 'role_has_permissions' && $table->$str != 'users' && $table->$str != 'currencies' && $table->$str != 'reward_point_settings' && $table->$str != 'ecommerce_settings' && $table->$str != 'external_services') {
                DB::table($table->$str)->truncate();
            }
        }
        return redirect()->back()->with('message', 'Database cleared successfully');
    }

    public function generalSetting()
    {
        $lims_general_setting_data = GeneralSetting::latest()->first();
        $lims_account_list = Account::where('is_active', true)->get();
        $lims_currency_list = Currency::get();
        $zones_array = array();
        $timestamp = time();
        foreach(timezone_identifiers_list() as $key => $zone) {
            date_default_timezone_set($zone);
            $zones_array[$key]['zone'] = $zone;
            $zones_array[$key]['diff_from_GMT'] = 'UTC/GMT ' . date('P', $timestamp);
        }
        return view('backend.setting.general_setting', compact('lims_general_setting_data', 'lims_account_list', 'zones_array', 'lims_currency_list'));
    }

    public function generalSettingStore(Request $request)
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');

        $this->validate($request, [
            'site_logo' => 'image|mimes:jpg,jpeg,png,gif|max:100000',
        ]);

        $data = $request->except('site_logo');
        // return $data;
        //writting timezone info in .env file
        $path = app()->environmentFilePath();
        $searchArray = array('APP_TIMEZONE='.env('APP_TIMEZONE'));
        $replaceArray = array('APP_TIMEZONE='.$data['timezone']);

        file_put_contents($path, str_replace($searchArray, $replaceArray, file_get_contents($path)));

        if(isset($data['is_rtl']))
            $data['is_rtl'] = true;
        else
            $data['is_rtl'] = false;

        $general_setting = GeneralSetting::latest()->first();
        $general_setting->id = 1;
        $general_setting->site_title = $data['site_title'];
        $general_setting->is_rtl = $data['is_rtl'];
        if(isset($data['is_zatca'])) {
            $general_setting->is_zatca = true;
        }
        else
            $general_setting->is_zatca = false;
        $general_setting->company_name = $data['company_name'];
        $general_setting->vat_registration_number = $data['vat_registration_number'];
        $general_setting->currency = $data['currency'];
        $general_setting->currency_position = $data['currency_position'];
        $general_setting->decimal = $data['decimal'];
        $general_setting->staff_access = $data['staff_access'];
        $general_setting->without_stock = $data['without_stock'];
        $general_setting->is_packing_slip = $data['is_packing_slip'];
        $general_setting->date_format = $data['date_format'];
        $general_setting->developed_by = $data['developed_by'];
        $general_setting->invoice_format = $data['invoice_format'];
        $general_setting->state = $data['state'];
        $general_setting->expiry_type = $data['expiry_type'];
        $general_setting->expiry_value = $data['expiry_value'];
        $logo = $request->site_logo;
        if ($logo) {
            $this->fileDelete('logo/', $general_setting->site_logo);

            $ext = pathinfo($logo->getClientOriginalName(), PATHINFO_EXTENSION);
            $logoName = date("Ymdhis") . '.' . $ext;
            $logo->move('public/logo', $logoName);
            $general_setting->site_logo = $logoName;
        }
        $general_setting->save();
        cache()->forget('general_setting');

        return redirect()->back()->with('message', 'Data updated successfully');
    }

    public function superadminGeneralSetting()
    {
        $lims_general_setting_data = GeneralSetting::latest()->first();
        return view('landlord.general_setting', compact('lims_general_setting_data'));
    }

    public function superadminGeneralSettingStore(Request $request)
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');

        $this->validate($request, [
            'site_logo' => 'image|mimes:jpg,jpeg,png,gif|max:100000',
            'og_image' => 'image|mimes:jpg,jpeg,png|max:100000',
        ]);

        $data = $request->except('site_logo');
        if(isset($data['is_rtl']))
            $data['is_rtl'] = true;
        else
            $data['is_rtl'] = false;

        $general_setting = GeneralSetting::latest()->first();
        $general_setting->id = 1;
        $general_setting->site_title = $data['site_title'];
        $general_setting->is_rtl = $data['is_rtl'];
        $general_setting->phone = $data['phone'];
        $general_setting->email = $data['email'];
        $general_setting->free_trial_limit = $data['free_trial_limit'];
        $general_setting->date_format = $data['date_format'];
        $general_setting->dedicated_ip = $data['dedicated_ip'];
        $general_setting->currency = $data['currency'];
        $general_setting->developed_by = $data['developed_by'];
        $logo = $request->site_logo;
        $general_setting->meta_title = $data['meta_title'];
        $general_setting->meta_description = $data['meta_description'];
        $general_setting->og_title = $data['og_title'];
        $general_setting->og_description = $data['og_description'];
        $general_setting->chat_script = $data['chat_script'];
        $general_setting->ga_script = $data['ga_script'];
        $general_setting->fb_pixel_script = $data['fb_pixel_script'];
        $general_setting->active_payment_gateway = implode(",", $data['active_payment_gateway']);
        $general_setting->stripe_public_key = $data['stripe_public_key'];
        $general_setting->stripe_secret_key = $data['stripe_secret_key'];
        $general_setting->paypal_client_id = $data['paypal_client_id'];
        $general_setting->paypal_client_secret = $data['paypal_client_secret'];
        $general_setting->razorpay_number = $data['razorpay_number'];
        $general_setting->razorpay_key = $data['razorpay_key'];
        $general_setting->razorpay_secret = $data['razorpay_secret'];
        $general_setting->paystack_public_key = $data['paystack_public_key'];
        $general_setting->paystack_secret_key = $data['paystack_secret_key'];
        $general_setting->paydunya_master_key = $data['paydunya_master_key'];
        $general_setting->paydunya_public_key = $data['paydunya_public_key'];
        $general_setting->paydunya_secret_key = $data['paydunya_secret_key'];
        $general_setting->paydunya_token = $data['paydunya_token'];
        $general_setting->ssl_store_id = $data['ssl_store_id'];
        $general_setting->ssl_store_password = $data['ssl_store_password'];
        $general_setting->bkash_app_key = $data['bkash_app_key'];
        $general_setting->bkash_app_secret = $data['bkash_app_secret'];
        $general_setting->bkash_username = $data['bkash_username'];
        $general_setting->bkash_password = $data['bkash_password'];
        $og_image = $request->og_image;
        if ($logo) {
            $this->fileDelete('landlord/images/logo/', $general_setting->site_logo);

            $ext = pathinfo($logo->getClientOriginalName(), PATHINFO_EXTENSION);
            $logoName = date("Ymdhis") . '.' . $ext;
            $logo->move('public/landlord/images/logo', $logoName);
            $general_setting->site_logo = $logoName;
        }
        if ($og_image) {
            $this->fileDelete('landlord/images/og-image/', $general_setting->og_image);

            $ext = pathinfo($og_image->getClientOriginalName(), PATHINFO_EXTENSION);
            $og_image_name = date("Ymdhis") . '.' . $ext;
            $og_image->move('public/landlord/images/og-image/', $og_image_name);
            $general_setting->og_image = $og_image_name;
        }
        $this->cacheForget('general_setting');
        $general_setting->save();
        return redirect()->back()->with('message', 'Data updated successfully');
    }

    public function superadminMailSetting()
    {
        $mail_setting_data = MailSetting::latest()->first();
        return view('landlord.mail_setting', compact('mail_setting_data'));
    }

    public function superadminMailSettingStore(Request $request)
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');

        $data = $request->all();
        $mail_setting = MailSetting::latest()->first();
        if(!$mail_setting)
            $mail_setting = new MailSetting;
        $mail_setting->driver = $data['driver'];
        $mail_setting->host = $data['host'];
        $mail_setting->port = $data['port'];
        $mail_setting->from_address = $data['from_address'];
        $mail_setting->from_name = $data['from_name'];
        $mail_setting->username = $data['username'];
        $mail_setting->password = trim($data['password']);
        $mail_setting->encryption = $data['encryption'];
        $mail_setting->save();
        return redirect()->back()->with('message', 'Data updated successfully');
    }

    public function rewardPointSetting()
    {
        $lims_reward_point_setting_data = RewardPointSetting::latest()->first();
        return view('backend.setting.reward_point_setting', compact('lims_reward_point_setting_data'));
    }

    public function rewardPointSettingStore(Request $request)
    {
        $data = $request->all();
        if(isset($data['is_active']))
            $data['is_active'] = true;
        else
            $data['is_active'] = false;
        $lims_reward_point_data = RewardPointSetting::latest()->first();
        if($lims_reward_point_data)
            $lims_reward_point_data->update($data);
        else
            RewardPointSetting::create($data);
        return redirect()->back()->with('message', 'Reward point setting updated successfully');
    }

    public function backup()
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');

        // Database configuration
        $host = env('DB_HOST');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        if(!config('database.connections.saleprosaas_landlord'))
            $database_name = env('DB_DATABASE');
        else
            $database_name = env('DB_PREFIX').$this->getTenantId();

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
            for ($i = 0; $i < $columnCount; $i ++) {
                while ($row = mysqli_fetch_row($result)) {
                    $sqlScript .= "INSERT INTO $table VALUES(";
                    for ($j = 0; $j < $columnCount; $j ++) {
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

        if(!empty($sqlScript))
        {
            // Save the SQL script to a backup file
            $backup_file_name = public_path().'/'.$database_name . '_backup_' . time() . '.sql';
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

    public function changeTheme($theme)
    {
        $lims_general_setting_data = GeneralSetting::latest()->first();
        $lims_general_setting_data->theme = $theme;
        $lims_general_setting_data->save();
    }

    public function mailSetting()
    {
        $mail_setting_data = MailSetting::latest()->first();
        return view('Tenant.setting.mail_setting', compact('mail_setting_data'));
    }

    public function mailSettingStore(Request $request)
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');

        $data = $request->all();
        $mail_setting = MailSetting::latest()->first();
        if(!$mail_setting)
            $mail_setting = new MailSetting;
        $mail_setting->driver = $data['driver'];
        $mail_setting->host = $data['host'];
        $mail_setting->port = $data['port'];
        $mail_setting->from_address = $data['from_address'];
        $mail_setting->from_name = $data['from_name'];
        $mail_setting->username = $data['username'];
        $mail_setting->password = trim($data['password']);
        $mail_setting->encryption = $data['encryption'];
        $mail_setting->save();
        return redirect()->back()->with('message', 'Data updated successfully');
    }

    public function smsSetting()
    {
        $settings = ExternalService::all();
        $tonkra = [];
        $revesms = [];
        $bdbulksms = [];
        $twilio = [];
        $clickatell = [];

        foreach($settings as $setting)
        {
                if($setting->name == 'tonkra'){
                    $tonkra['sms_id'] = $setting->id ?? '';
                    $tonkra['active'] = $setting->active ?? '';
                    $tonkra['details'] = json_decode($setting->details) ?? '';
                }

                if($setting->name == 'revesms'){
                    $revesms['sms_id'] = $setting->id ?? '';
                    $revesms['active'] = $setting->active ?? '';
                    $revesms['details'] = json_decode($setting->details) ?? '';
                }

                if($setting->name == 'bdbulksms'){
                    $bdbulksms['sms_id'] = $setting->id ?? '';
                    $bdbulksms['active'] = $setting->active ?? '';
                    $bdbulksms['details'] = json_decode($setting->details) ?? '';
                }

                if($setting->name == 'twilio'){
                    $twilio['sms_id'] = $setting->id ?? '';
                    $twilio['active'] = $setting->active ?? '';
                    $twilio['details'] = json_decode($setting->details) ?? '';
                }

                if($setting->name == 'clickatell'){
                    $clickatell['sms_id'] = $setting->id ?? '';
                    $clickatell['active'] = $setting->active ?? '';
                    $clickatell['details'] = json_decode($setting->details) ?? '';
                }

        }

        $tonkra['sms_id']       = $tonkra['sms_id'] ?? '';
        $tonkra['active']       = $tonkra['active'] ?? '';
        $tonkra['api_token']    = $tonkra['details']->api_token  ?? '';
        $tonkra['recipent']     = $tonkra['details']->recipent  ?? '';
        $tonkra['sender_id']    = $tonkra['details']->sender_id  ?? '';

        $revesms['sms_id']      = $revesms['sms_id'] ?? '';
        $revesms['active']      = $revesms['active'] ?? '';
        $revesms['apikey']      = $revesms['details']->apikey  ?? '';
        $revesms['secretkey']   = $revesms['details']->secretkey  ?? '';
        $revesms['callerID']    = $revesms['details']->callerID  ?? '';

        $bdbulksms['sms_id']    = $bdbulksms['sms_id'] ?? '';
        $bdbulksms['active']    = $bdbulksms['active'] ?? '';
        $bdbulksms['token']     = $bdbulksms['details']->token   ?? '';

        $twilio['sms_id']       = $twilio['sms_id'] ?? '';
        $twilio['active']       = $twilio['active'] ?? '';
        $twilio['account_sid']  = $twilio['details']->account_sid  ?? '';
        $twilio['auth_token']   = $twilio['details']->auth_token  ?? '';
        $twilio['twilio_number']= $twilio['details']->twilio_number  ?? '';

        $clickatell['sms_id']   = $clickatell['sms_id'] ?? '';
        $clickatell['active']   = $clickatell['active'] ?? '';
        $clickatell['api_key']  = $clickatell['details']->api_key ?? '';

        return view('backend.setting.sms_setting',compact('tonkra','twilio','clickatell','revesms','bdbulksms'));
    }

    public function smsSettingStore(Request $request)
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');

        $data = $request->all();

        $data['active'] = $data['active'] ?? 0;
        $tonkra = [];
        $revesms = [];
        $bdbulksms = [];
        $twilio = [];
        $clickatell = [];

        if($data['gateway'] == 'revesms'){
            $revesms['apikey'] = $data['apikey'] ;
            $revesms['secretkey'] = $data['secretkey'] ;
            $revesms['callerID'] = $data['callerID'];
            $data['details'] = json_encode($revesms);
        }

        if($data['gateway'] == 'bdbulksms'){
            $bdbulksms['token'] = $data['token'] ;
            $data['details'] = json_encode($bdbulksms);
        }

        if($data['gateway'] == 'twilio'){
            $twilio['account_sid'] = $data['account_sid'] ;
            $twilio['auth_token'] = $data['auth_token'] ;
            $twilio['twilio_number'] = $data['twilio_number'] ;
            $data['details'] = json_encode($twilio);
        }

        if($data['gateway'] == 'tonkra'){
            $tonkra['api_token'] = $data['api_token'];
            $tonkra['sender_id'] = $data['sender_id'];
            $data['details'] = json_encode($tonkra);
        }

        if($data['gateway'] == 'clickatell'){
            $clickatell['api_key'] = $data['api_key'];
            $data['details'] = json_encode($clickatell);
        }
        if (isset($data['active']) && $data['active'] == true) {
            ExternalService::where('type','sms')
                            ->where('active', true)
                            ->update(['active' => false]);
        }
        ExternalService::updateOrCreate(
            [
                'name' => $data['gateway']
            ],
            [
            'name' => $data['gateway'],
            'type' => $data['type'],
            'details' => $data['details'],
            'active' => $data['active']
            ]
        );

        return redirect()->back()->with('message', 'Data updated successfully');
    }

    public function createSms()
    {
        $lims_customer_list = Customer::where('is_active', true)->get();
        $smsTemplates = SmsTemplate::all();
        // dd($smsTemplates);
        return view('backend.setting.create_sms', compact('lims_customer_list','smsTemplates'));
    }

    public function sendSms(Request $request)
    {
        $data = $request->all();

        $smsProvider = ExternalService::where('active',true)->where('type','sms')->first();

        $smsData['sms_provider_name'] = $smsProvider->name;
        $smsData['details'] = $smsProvider->details;
        $smsData['message'] = $data['message'];
        $smsData['recipent'] = $data['mobile'];
        $numbers = explode(",", $data['mobile']);
        $smsData['numbers'] = $numbers;

        $this->_smsService->initialize($smsData);

        return redirect()->back()->with('message','SMS sent successfully');

    }

    public function processSmsData($templateId, $customerId, $referenceNo)
    {
        $smsData = [];

        $smsTemplate = SmsTemplate::find($templateId);
        $template = $smsTemplate['content'];

        $customer = Customer::find($customerId);
        $customerName = $customer['name'];

        $smsData['message'] = $this->replacePlaceholders($template, $customerName, $referenceNo);

        $smsProvider = ExternalService::where('active',true)->where('type','sms')->first();
        $smsData['sms_provider_name'] = $smsProvider->name;
        $smsData['details'] = $smsProvider->details;

        return $smsData;
    }

    public function replacePlaceholders($template, $customerName, $referenceNo) {
        // Check for the presence of the [customer] placeholder in the template
        if (strpos($template, '[customer]') !== false) {
            // Replace [customer] with the value of $customerName
            $template = str_replace('[customer]', $customerName, $template);
        }

        // Check for the presence of the [reference] placeholder in the template
        if (strpos($template, '[reference]') !== false) {
            // Replace [reference] with the value of $referenceNo
            $template = str_replace('[reference]', $referenceNo, $template);
        }

        // Return the modified template with the placeholders replaced (if found)
        return $template;
    }

    public function hrmSetting()
    {
        $lims_hrm_setting_data = HrmSetting::latest()->first();
        return view('backend.setting.hrm_setting', compact('lims_hrm_setting_data'));
    }

    public function hrmSettingStore(Request $request)
    {
        $data = $request->all();
        $lims_hrm_setting_data = HrmSetting::firstOrNew(['id' => 1]);
        $lims_hrm_setting_data->checkin = $data['checkin'];
        $lims_hrm_setting_data->checkout = $data['checkout'];
        $lims_hrm_setting_data->save();
        return redirect()->back()->with('message', 'Data updated successfully');

    }
    public function posSetting()
    {
        $lims_customer_list = Customer::where('is_active', true)->get();
        $lims_warehouse_list = Warehouse::where('is_active', true)->get();
        $lims_biller_list = Biller::where('is_active', true)->get();
        $lims_pos_setting_data = PosSetting::latest()->first();

        if($lims_pos_setting_data)
            $options = explode(',', $lims_pos_setting_data->payment_options);
        else
            $options = [];

        return view('backend.setting.pos_setting', compact('lims_customer_list', 'lims_warehouse_list', 'lims_biller_list', 'lims_pos_setting_data','options'));
    }

    public function posSettingStore(Request $request)
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');

        $data = $request->all();

        if(isset($data['options'])){
            $options = implode(',',$data['options']);
        } else {
            $options = '"none"';
        }

        $pos_setting = PosSetting::firstOrNew(['id' => 1]);
        $pos_setting->id = 1;
        $pos_setting->customer_id = $data['customer_id'];
        $pos_setting->warehouse_id = $data['warehouse_id'];
        $pos_setting->biller_id = $data['biller_id'];
        $pos_setting->product_number = $data['product_number'];
        $pos_setting->stripe_public_key = $data['stripe_public_key'];
        $pos_setting->stripe_secret_key = $data['stripe_secret_key'];
        $pos_setting->paypal_live_api_username = $data['paypal_username'];
        $pos_setting->paypal_live_api_password = $data['paypal_password'];
        $pos_setting->paypal_live_api_secret = $data['paypal_signature'];
        $pos_setting->payment_options = $options;
        $pos_setting->invoice_option = $data['invoice_size'];
        $pos_setting->thermal_invoice_size = $data['thermal_invoice_size'];

        if(!isset($data['keybord_active']))
            $pos_setting->keybord_active = false;
        else
            $pos_setting->keybord_active = true;
        if(!isset($data['is_table']))
            $pos_setting->is_table = false;
        else
            $pos_setting->is_table = true;
        if(!isset($data['send_sms']))
            $pos_setting->send_sms = false;
        else
            $pos_setting->send_sms = true;
        $pos_setting->save();
        cache()->forget('pos_setting');
        return redirect()->back()->with('message', 'POS setting updated successfully');
    }
}
