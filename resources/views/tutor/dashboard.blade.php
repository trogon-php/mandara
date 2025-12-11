<h1>Tutor Dashboard</h1>
<p>Welcome, {{ $user->name }}</p>
<form method="POST" action="{{ route('logout') }}">@csrf <button>Logout</button></form>
