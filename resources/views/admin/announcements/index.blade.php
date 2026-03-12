@extends('layouts.app')

@section('content')
<div style="max-width:900px;margin:20px auto;padding:20px;">

    <h1 style="font-size:24px;font-weight:bold;margin-bottom:20px;">
        Manage Announcements
    </h1>

    {{-- Success message --}}
    @if(session('success'))
        <div style="background:#d1fae5;color:#065f46;padding:10px;margin-bottom:15px;">
            {{ session('success') }}
        </div>
    @endif

    {{-- EXISTING ANNOUNCEMENTS --}}
    @forelse($announcements as $a)
        <div style="border:1px solid #ccc;padding:15px;margin-bottom:15px;">

            {{-- UPDATE FORM --}}
            <form method="POST" action="{{ route('announcements.update', $a->id) }}">
                @csrf
                @method('PUT')

                <label>Title</label><br>
                <input type="text" name="title" value="{{ $a->title }}" required
                       style="width:100%;padding:8px;margin-bottom:8px;"><br>

                <label>Message</label><br>
                <textarea name="message" required
                          style="width:100%;padding:8px;margin-bottom:8px;"
                          rows="3">{{ $a->message }}</textarea><br>

                <label>Start Date</label><br>
                <input type="date" name="start_date" value="{{ $a->start_date }}" required
                       style="padding:6px;margin-bottom:8px;"><br>

                <label>End Date</label><br>
                <input type="date" name="end_date" value="{{ $a->end_date }}"
                       style="padding:6px;margin-bottom:8px;"><br><br>

                <label>
                    <input type="checkbox" name="is_active" value="1"
                           {{ $a->is_active ? 'checked' : '' }}>
                    Active
                </label>
                <br><br>

                <button type="submit"
                        style="background:#2563eb;color:#fff;padding:8px 16px;border:none;">
                    Update
                </button>
            </form>

            {{-- DELETE FORM --}}
            <form method="POST"
                  action="{{ route('announcements.destroy', $a->id) }}"
                  style="margin-top:10px;">
                @csrf
                @method('DELETE')

                <button type="submit"
                        style="background:#dc2626;color:#fff;padding:6px 14px;border:none;">
                    Delete
                </button>
            </form>

        </div>
    @empty
        <p>No announcements yet.</p>
    @endforelse

    <hr style="margin:30px 0;">

    {{-- ADD NEW ANNOUNCEMENT --}}
    <h2 style="font-size:20px;font-weight:bold;margin-bottom:10px;">
        Add New Announcement
    </h2>

    <form method="POST" action="{{ route('announcements.store') }}">
        @csrf

        <label>Title</label><br>
        <input type="text" name="title" required
               style="width:100%;padding:8px;margin-bottom:8px;"><br>

        <label>Message</label><br>
        <textarea name="message" required
                  style="width:100%;padding:8px;margin-bottom:8px;"
                  rows="3"></textarea><br>

        <label>Start Date</label><br>
        <input type="date" name="start_date" required
               style="padding:6px;margin-bottom:8px;"><br>

        <label>End Date</label><br>
        <input type="date" name="end_date"
               style="padding:6px;margin-bottom:8px;"><br><br>

        <label>
            <input type="checkbox" name="is_active" value="1" checked>
            Active
        </label>
        <br><br>

        <button type="submit"
                style="background:#16a34a;color:#fff;padding:10px 20px;border:none;">
            Add Announcement
        </button>
    </form>

</div>
@endsection
