<div class="border p-4 mb-4 rounded bg-gray-50">
    <input type="text"
        name="announcements[{{ $i }}][title]"
        value="{{ $a['title'] }}"
        placeholder="Title"
        class="w-full mb-2 p-2 border rounded">

    <textarea
        name="announcements[{{ $i }}][message]"
        class="w-full mb-2 p-2 border rounded"
        placeholder="Message">{{ $a['message'] }}</textarea>

    <div class="grid grid-cols-2 gap-2">
        <input type="date"
            name="announcements[{{ $i }}][start_date]"
            value="{{ $a['start_date'] }}"
            class="p-2 border rounded">

        <input type="date"
            name="announcements[{{ $i }}][end_date]"
            value="{{ $a['end_date'] }}"
            class="p-2 border rounded">
    </div>

    <label class="flex items-center mt-2">
        <input type="checkbox"
            name="announcements[{{ $i }}][is_active]"
            value="1"
            @checked($a['is_active'])>
        <span class="ml-2">Active</span>
    </label>

    <button type="button"
        onclick="this.closest('div').remove()"
        class="mt-2 text-red-600">
        Remove
    </button>
</div>
