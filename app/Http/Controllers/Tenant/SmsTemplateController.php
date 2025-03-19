<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\SmsTemplate;
use Illuminate\Http\Request;

class SmsTemplateController extends Controller
{
    public function index()
    {
        $templates = SmsTemplate::all();

        return view('backend.sms_templates.index',compact('templates'));
    }

    public function store(Request $request)
    {
        $data = $request->all();

        if (isset($data['is_default']) && $data['is_default'] == true) {
            SmsTemplate::where('is_default', true)->update(['is_default' => false]);
        }

        if (isset($data['is_default_ecommerce']) && $data['is_default_ecommerce'] == true) {
            SmsTemplate::where('is_default_ecommerce', true)->update(['is_default' => false]);
        }

        SmsTemplate::create($data);

        return redirect('smstemplates')->with('message', 'Data inserted successfully');
    }

    public function update(Request $request, string $id)
    {
        $data = $request->all();

        $template = SmsTemplate::find($data['smstemplate_id']);

        if (isset($data['is_default']) && $data['is_default'] == true) {
            // Update existing default item to false, excluding the current item being updated
            SmsTemplate::where('id', '!=', $template->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }
        else {
            $data['is_default'] = false;
        }
        if (isset($data['is_default_ecommerce']) && $data['is_default_ecommerce'] == true) {
            // Update existing default item to false, excluding the current item being updated
            SmsTemplate::where('id', '!=', $template->id)
                ->where('is_default_ecommerce', true)
                ->update(['is_default_ecommerce' => false]);
        }
        else {
            $data['is_default_ecommerce'] = false;
        }

        $template->update($data);
        return redirect('smstemplates')->with('message', 'Data updated successfully');

    }

    public function destroy(string $id)
    {
        $template = SmsTemplate::find($id);
        $template->delete();
        return redirect()->back();
    }
}
