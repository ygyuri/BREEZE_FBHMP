{{-- resources/views/dashboards/partials/foodbank.blade.php --}}
<h5>Welcome, Foodbank!</h5>
<p>Manage your inventory and handle requests efficiently from this dashboard.</p>
<div class="mt-3">
    <a href="{{ route('inventory.index') }}" class="btn btn-info">Manage Inventory</a>
    <a href="{{ route('requests.index') }}" class="btn btn-warning">View Requests</a>
</div>
