<script>
function addAnnouncement() {
    const container = document.getElementById('announcements');

    const html = `
    <div class="border p-4 mb-4 rounded bg-gray-50">
        <input name="announcements[${index}][title]" placeholder="Title"
            class="w-full mb-2 p-2 border rounded">

        <textarea name="announcements[${index}][message]"
            class="w-full mb-2 p-2 border rounded"
            placeholder="Message"></textarea>

        <div class="grid grid-cols-2 gap-2">
            <input type="date" name="announcements[${index}][start_date]"
                class="p-2 border rounded">
            <input type="date" name="announcements[${index}][end_date]"
                class="p-2 border rounded">
        </div>

        <label class="flex items-center mt-2">
            <input type="checkbox" name="announcements[${index}][is_active]" value="1">
            <span class="ml-2">Active</span>
        </label>

        <button type="button"
            onclick="this.closest('div').remove()"
            class="mt-2 text-red-600">
            Remove
        </button>
    </div>
    `;

    container.insertAdjacentHTML('beforeend', html);
    index++;
}
</script>
