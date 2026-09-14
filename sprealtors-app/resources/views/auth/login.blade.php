<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex, nofollow">
	<title>Admin Login — SP REALTORS</title>

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=DM+Serif+Display&display=swap" rel="stylesheet">

	@vite(['resources/css/app.css'])
</head>
<body class="bg-lightgray antialiased min-h-screen flex items-center justify-center p-5">

	<div class="w-full max-w-[400px]">
		<div class="text-center mb-6">
			<a href="{{ route('home') }}" class="inline-flex items-center gap-2">
				<span class="text-navy"><x-icon name="home" class="w-9 h-9" /></span>
				<span class="flex flex-col leading-tight text-left">
					<span class="font-display text-[22px] text-navy">SP REALTORS</span>
					<span class="text-[11px] text-muted">Properties · People · Possibilities</span>
				</span>
			</a>
		</div>

		<div class="card p-6">
			<h1 class="font-sans text-[20px] font-bold text-ink mb-1">Admin Login</h1>
			<p class="text-[13px] text-muted mb-5">Sign in to manage properties, projects and enquiries.</p>

			@if($errors->any())
				<div class="mb-4 rounded-[6px] bg-red-50 border border-red-200 px-4 py-3 text-[13px] text-red-700" role="alert">
					{{ $errors->first() }}
				</div>
			@endif

			<form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-4">
				@csrf

				<div>
					<label for="email" class="field-label">Email Address</label>
					<input id="email" type="email" name="email" value="{{ old('email') }}"
					       class="field" required autofocus autocomplete="username">
				</div>

				<div>
					<label for="password" class="field-label">Password</label>
					<input id="password" type="password" name="password"
					       class="field" required autocomplete="current-password">
				</div>

				<label class="flex items-center gap-2 text-[13px] text-body cursor-pointer">
					<input type="checkbox" name="remember" class="w-4 h-4 accent-blue">
					Remember me
				</label>

				<button type="submit" class="btn btn-primary w-full">
					<x-icon name="lock" class="w-[18px] h-[18px]" />
					Sign In
				</button>
			</form>
		</div>

		<p class="text-center text-[12px] text-muted mt-5">
			<a href="{{ route('home') }}" class="hover:text-blue">&larr; Back to website</a>
		</p>
	</div>

</body>
</html>
