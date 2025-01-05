{{-- resources/views/dashboards/partials/sidebar.blade.php --}}
<div class="list-group">
    <a href="{{ route($role . '.dashboard') }}" class="list-group-item list-group-item-action active">
        Dashboard
    </a>
    @if ($role === 'admin')
        <a href="{{ route('users.index') }}" class="list-group-item list-group-item-action">Manage Users</a>
        <a href="{{ route('reports.index') }}" class="list-group-item list-group-item-action">View Reports</a>
    @elseif ($role === 'donor')
        <a href="{{ route('donations.index') }}" class="list-group-item list-group-item-action">My Donations</a>
    @elseif ($role === 'foodbank')
        <a href="{{ route('inventory.index') }}" class="list-group-item list-group-item-action">Manage Inventory</a>
        <a href="{{ route('requests.index') }}" class="list-group-item list-group-item-action">Manage Requests</a>
    @endif
    <a href="{{ route('logout') }}" class="list-group-item list-group-item-action text-danger">Logout</a>
</div>
