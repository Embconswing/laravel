<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointments</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        let allowRefresh = true;

        setInterval(() => {
            const modal = document.getElementById('uploadModal');
            if (allowRefresh && (!modal || modal.classList.contains('hidden'))) {
                window.location.reload();
            }
        }, 5000);
    </script>
</head>

<body class="bg-gray-100">

<div class="container mx-auto p-4">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-bold">Appointments</h1>

        <button
            onclick="openUploadModal()"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded font-semibold">
            Upload CSV
        </button>
    </div>

    {{-- TABLE --}}
    <div class="overflow-x-auto border bg-white rounded">
        <table class="min-w-[900px] w-full border-collapse">
            <thead class="bg-gray-200">
            <tr>
                <th class="border px-2 py-1">Appt</th>
                <th class="border px-2 py-1">Name</th>
                <th class="border px-2 py-1">Start time</th>
                <th class="border px-2 py-1">Window</th>
                <th class="border px-2 py-1">Assign Window</th>
                <th class="border px-2 py-1 w-48">Action</th>
            </tr>
            </thead>

            <tbody>
            @foreach($appointments as $appointment)

                @php
                    $usedByOthers = array_diff($usedWindows, [$appointment->window_no]);
                @endphp

                <tr class="text-center
                    {{ $appointment->status === 'waiting' ? 'bg-blue-50' : '' }}
                    {{ $appointment->status === 'completed' ? 'bg-green-50' : '' }}
                ">
                    <td class="border px-2 py-1">
                        {{ $appointment->appointment_number }}
                    </td>

                    <td class="border px-2 py-1">
                        {{ $appointment->customer_name }}
                    </td>

                    <td class="border px-2 py-1">
                        {{ $appointment->start_time }}
                    </td>

                    <td class="border px-2 py-1 font-semibold">
                        {{ $appointment->window_no ?? '-' }}
                    </td>

                    {{-- ASSIGN WINDOW --}}
                    <td class="border px-2 py-1">
                        @if($appointment->status !== 'completed')
                            <form method="POST"
                                  action="{{ route('appointments.assignWindow', $appointment) }}"
                                  class="flex justify-center gap-2 flex-wrap">
                                @csrf
                                @method('PATCH')

                                @for($i = 1; $i <= 4; $i++)
                                    @php
                                        if ($appointment->window_no == $i) {
                                            $bg = '#F54927';
                                            $disabled = false;
                                        } elseif (in_array($i, $usedByOthers)) {
                                            $bg = '#dc2626';
                                            $disabled = true;
                                        } else {
                                            $bg = '#3b82f6';
                                            $disabled = false;
                                        }
                                    @endphp

                                    <button
                                        type="button"
                                        value="{{ $i }}"
                                        {{ $disabled ? 'disabled' : '' }}
                                        onclick="assignWindowWithSound(this)"
                                        style="
                                            background-color: {{ $bg }};
                                            color: white;
                                            padding: 6px 12px;
                                            border-radius: 4px;
                                            font-weight: 600;
                                            opacity: {{ $disabled ? '0.6' : '1' }};
                                            cursor: {{ $disabled ? 'not-allowed' : 'pointer' }};
                                        ">
                                        {{ $i }}
                                    </button>
                                @endfor
                            </form>
                        @else
                            <span class="text-gray-400 italic">—</span>
                        @endif
                    </td>

                    {{-- ACTION --}}
                    <td class="border px-2 py-1">
                        <div class="flex justify-center gap-2 flex-wrap">

                            {{-- PLAY SOUND --}}
                            

                            {{-- COMPLETE --}}
                            @if($appointment->status !== 'completed')
                                <form method="POST"
                                      action="{{ route('appointments.complete', $appointment) }}">
                                    @csrf
                                    <button class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded">
                                        Complete
                                    </button>
                                </form>
                            @endif

                            {{-- BACK TO QUEUE --}}
                            @if($appointment->window_no)
                                <form method="POST"
                                      action="{{ route('appointments.skip', $appointment) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">
                                        Back to Queue
                                    </button>
                                </form>
                            @endif

                        </div>
                    </td>
                </tr>

            @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- AUDIO --}}
<audio id="callNextSound" preload="auto">
    <source src="{{ asset('sounds/callnext.mp3') }}" type="audio/mpeg">
</audio>

{{-- SCRIPTS --}}
<script>
    function playCallSound() {
        const sound = document.getElementById('callNextSound');
        allowRefresh = false;

        sound.currentTime = 0;
        sound.play().catch(() => {});

        setTimeout(() => allowRefresh = true, 2000);
    }

    function assignWindowWithSound(button) {
        const form = button.closest('form');
        const sound = document.getElementById('callNextSound');

        allowRefresh = false;

        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'window_no';
        hiddenInput.value = button.value;
        form.appendChild(hiddenInput);

        sound.currentTime = 0;
        sound.play().catch(() => {});

        setTimeout(() => form.submit(), 200);
    }

    function openUploadModal() {
        document.getElementById('uploadModal').classList.remove('hidden');
        document.getElementById('uploadModal').classList.add('flex');
    }

    function closeUploadModal() {
        document.getElementById('uploadModal').classList.add('hidden');
        document.getElementById('uploadModal').classList.remove('flex');
    }
</script>

{{-- CSV UPLOAD MODAL --}}
<div id="uploadModal"
     class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold">Upload Appointments CSV</h2>
            <button onclick="closeUploadModal()" class="text-xl">&times;</button>
        </div>

        <form action="{{ route('appointments.import') }}"
              method="POST"
              enctype="multipart/form-data"
              class="space-y-4">
            @csrf
            <input type="file" name="csv_file" accept=".csv" required>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeUploadModal()">Cancel</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                    Upload
                </button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
