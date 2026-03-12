<div id="pendingModal"
     class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">

    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <h2 class="text-lg font-bold mb-3">Mark Appointment as Pending</h2>

        <form method="POST" id="pendingForm">
            @csrf
            @method('PATCH')

            <textarea name="reason"
                      required
                      class="w-full border rounded p-2 mb-4"
                      rows="4"
                      placeholder="Enter reason for pending..."></textarea>

            <div class="flex justify-end gap-2">
                <button type="button"
                        onclick="closePendingModal()"
                        class="bg-gray-600 text-white px-4 py-2 rounded">
                    Cancel
                </button>

                <button type="submit"
                        class="bg-yellow-600 text-white px-4 py-2 rounded">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>