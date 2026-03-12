
    <div id="uploadModal"
     class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">

    <div class="bg-white rounded-lg w-full max-w-2xl p-6">

        <h2 class="text-lg font-bold mb-3">CSV Preview & Upload</h2>

        <form id="csvForm" enctype="multipart/form-data">
            @csrf
            <input type="file" name="csv_file" id="csvInput" class="border p-2 w-full">

            <div id="preview" class="mt-4 hidden">
                <table class="w-full text-sm border">
                    <tbody id="previewBody"></tbody>
                </table>
            </div>

            <div class="mt-4 hidden" id="progressBox">
                <div class="w-full bg-gray-200 rounded">
                    <div id="progressBar"
                         class="bg-blue-600 text-white text-xs h-4 text-center"
                         style="width:0%">0%</div>
                </div>
            </div>

            <button type="button"
                    onclick="startPreview()"
                    class="mt-4 bg-gray-600 text-white px-4 py-2 rounded w-full">
                Preview CSV
            </button>

            <button type="button"
                    onclick="startUpload()"
                    class="mt-2 bg-blue-600 text-white px-4 py-2 rounded w-full">
                Upload CSV
            </button>

            <button type="button"
                    onclick="closeUploadModal()"
                    class="mt-2 bg-red-600 text-white px-4 py-2 rounded w-full">
                Close
            </button>
        </form>
    </div>
</div>