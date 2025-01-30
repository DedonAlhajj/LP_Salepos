<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<meta name="x-apple-disable-message-reformatting">
	<title></title>
	<!--[if mso]>
  <noscript>
    <xml>
      <o:OfficeDocumentSettings>
        <o:PixelsPerInch>96</o:PixelsPerInch>
      </o:OfficeDocumentSettings>
    </xml>
  </noscript>
  <![endif]-->
	<style>
		table,
		td,
		div,
		h1,
		p {
			font-family: Arial, sans-serif;
		}
	</style>
</head>

<body style="margin:0;padding:0;">
	<table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0;background:#ffffff;">
		<tr>
			<td align="center" style="padding:0;">
				<table role="presentation" style="width:602px;border-collapse:collapse;border:1px solid #cccccc;border-spacing:0;text-align:left;">
					<tr>
						<td align="center" style="padding:40px 0 30px 0;background:#CCC;">
							@if($general_setting->site_logo)
							<a href="{{url('/')}}"><img src="{{url('logo', $general_setting->site_logo)}}" width="200" style="height:auto;display:block;"></a>
							@else
							<a href="{{url('/')}}">
								<h1 class="d-inline">{{$general_setting->site_title}}</h1>
							</a>
							@endif
						</td>
					</tr>
					<tr>
						<td style="padding:36px 30px 42px 30px;">
							<table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0;">
								<tr>
									<td style="padding:0 0 36px 0;color:#153643;">
										<table style="border-collapse: collapse; width: 100%;">
											<tbody>
												<tr>
													<td style="border: 1px solid #000; padding: 5px">Date</td>
													<td style="border: 1px solid #000; padding: 5px">{{$delivery_data['date']}}</td>
												</tr>
												<tr>
													<td style="border: 1px solid #000; padding: 5px">Delivery Reference</td>
													<td style="border: 1px solid #000; padding: 5px">{{$delivery_data['delivery_reference_no']}}</td>
												</tr>
												<tr>
													<td style="border: 1px solid #000; padding: 5px">Sale Reference</td>
													<td style="border: 1px solid #000; padding: 5px">{{$delivery_data['sale_reference_no']}}</td>
												</tr>
											</tbody>
										</table>

										<table style="border-collapse: collapse; width: 100%;">
											<thead>
												<th style="border: 1px solid #000; padding: 5px">No</th>
												<th style="border: 1px solid #000; padding: 5px">Code</th>
												<th style="border: 1px solid #000; padding: 5px">Description</th>
												<th style="border: 1px solid #000; padding: 5px">Qty</th>
											</thead>
											<tbody>
												@foreach($delivery_data['codes'] as $key => $code)
												<tr>
													<td style="border: 1px solid #000; padding: 5px">{{$key+1}}</td>
													<td style="border: 1px solid #000; padding: 5px">{{$code}}</td>
													<td style="border: 1px solid #000; padding: 5px">{{$delivery_data['name'][$key]}}</td>
													<td style="border: 1px solid #000; padding: 5px">{{$delivery_data['qty'][$key]}}</td>
												</tr>
												@endforeach
											</tbody>
										</table>

										<p>Prepared By: {{$delivery_data['prepared_by']}}</p>
										<p>Delivered By: {{$delivery_data['delivered_by']}}</p>
										<p>Recieved By: {{$delivery_data['recieved_by']}}</p>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td style="padding:30px;background:#333;">
							<table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0;font-size:9px;font-family:Arial,sans-serif;">
								<tr>
									<td style="padding:0;width:50%;" align="center">
										<p style="margin:0;font-size:14px;line-height:16px;font-family:Arial,sans-serif;color:#ffffff;">
											&copy; {{$general_setting->site_title}} {{date('Y')}}<br />
										</p>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>

</html>