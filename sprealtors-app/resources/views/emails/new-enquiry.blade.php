<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>New Enquiry</title>
</head>
<body style="margin:0; padding:0; background:#F5F8FA; font-family:Arial, Helvetica, sans-serif; color:#1F2933;">
	<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F5F8FA; padding:24px 0;">
		<tr>
			<td align="center">
				<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px; background:#ffffff; border-radius:8px; overflow:hidden;">
					<tr>
						<td style="background:#092B50; padding:20px 28px;">
							<span style="color:#ffffff; font-size:18px; font-weight:bold;">SP REALTORS</span><br>
							<span style="color:#C89A45; font-size:13px;">New website enquiry</span>
						</td>
					</tr>
					<tr>
						<td style="padding:24px 28px;">
							<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px; line-height:1.6;">
								<tr>
									<td style="width:120px; color:#5B6B7F; vertical-align:top;">Name</td>
									<td style="font-weight:bold;">{{ $enquiry->name }}</td>
								</tr>
								<tr>
									<td style="color:#5B6B7F; vertical-align:top;">Phone</td>
									<td><a href="tel:{{ $enquiry->phone }}" style="color:#12579A;">{{ $enquiry->phone }}</a></td>
								</tr>
								@if($enquiry->email)
									<tr>
										<td style="color:#5B6B7F; vertical-align:top;">Email</td>
										<td><a href="mailto:{{ $enquiry->email }}" style="color:#12579A;">{{ $enquiry->email }}</a></td>
									</tr>
								@endif
								<tr>
									<td style="color:#5B6B7F; vertical-align:top;">Source</td>
									<td>{{ $enquiry->sourceLabel() }}</td>
								</tr>
								@if($enquiry->subject())
									<tr>
										<td style="color:#5B6B7F; vertical-align:top;">Regarding</td>
										<td>{{ $enquiry->subject() }}</td>
									</tr>
								@endif
								@if($enquiry->message)
									<tr>
										<td style="color:#5B6B7F; vertical-align:top;">Message</td>
										<td>{{ $enquiry->message }}</td>
									</tr>
								@endif
							</table>

							<p style="margin:24px 0 0; text-align:center;">
								<a href="{{ route('admin.enquiries.show', $enquiry) }}"
								   style="display:inline-block; background:#12579A; color:#ffffff; text-decoration:none; padding:10px 20px; border-radius:6px; font-size:14px; font-weight:bold;">
									View in Admin Panel
								</a>
							</p>
						</td>
					</tr>
					<tr>
						<td style="padding:16px 28px; background:#F5F8FA; font-size:12px; color:#5B6B7F;">
							Sent automatically from sprealtors.in — no reply needed here, "Reply" goes to the visitor.
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>
