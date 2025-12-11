<h1>Student Dashboard</h1>
<p>Welcome, {{ $user->name }}</p>
<form method="POST" action="{{ route('logout') }}">@csrf <button>Logout</button></form>
