<!DOCTYPE html>
<html lang="en">
@include('partials.head')

<body>
	<header>
		@include('partials.navbar')
	</header>

	<main>
		@yield('content')
	</main>

	@include('partials.footer')
</body>
</html>