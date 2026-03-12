@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Items Table</h2>
</div>

<table class="table table-striped table-hover">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Status</th>
            <th>Assigned User</th>
        </tr>
    </thead>
    <tbody>
        @forelse($items as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ Str::limit($item->description, 50) }}</td>
                <td>
                    <span class="badge {{ $item->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                        {{ ucfirst($item->status) }}
                    </span>
                </td>
                <td>
                    <form action="{{ route('items.assign', $item) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <select name="user_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">-- Unassign --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id', $item->user_id ?? '') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center">No items found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection
