@extends('layouts.admin')

@section('content')
    <div class="container-xl">
        <h1 class="page-title mb-4">Станцын мэдээлэл засах</h1>

        <form action="{{ route('power-plants.update', $powerPlant->id) }}" method="POST" class="card">
            @csrf
            @method('PUT')

            <div class="card-body">

                <!-- Станцын нэр -->
                <div class="mb-3">
                    <label class="form-label" for="name">Станцын нэр</label>
                    <input type="text" name="name" id="name" class="form-control"
                        value="{{ old('name', $powerPlant->name) }}" required>
                </div>

                <!-- Богино нэр -->
                <div class="mb-3">
                    <label class="form-label" for="short_name">Богино нэр</label>
                    <input type="text" name="short_name" id="short_name" class="form-control"
                        value="{{ old('short_name', $powerPlant->short_name) }}" required>
                </div>

                <!-- Зуухууд -->
                <h3 class="mt-4 mb-2">Зуухууд</h3>
                <div id="boilers">
                    @foreach ($powerPlant->boilers as $index => $boiler)
                        <div class="boiler-entry mb-3 d-flex align-items-center gap-2">
                            <input type="hidden" name="boilers[{{ $index }}][id]" value="{{ $boiler->id }}">
                            <input type="text" name="boilers[{{ $index }}][name]" value="{{ $boiler->name }}"
                                class="form-control" required>
                            <button type="button" class="btn btn-outline-danger remove-boiler" title="Хасах">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M4 7l16 0" />
                                    <path d="M10 11l0 6" />
                                    <path d="M14 11l0 6" />
                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="add-boiler">
                    <i class="ti ti-plus"></i> Зуух нэмэх
                </button>

                <!-- Турбингенераторууд -->
                <h3 class="mt-4 mb-2">Турбингенераторууд</h3>
                <div id="turbine_generators">
                    @foreach ($powerPlant->turbineGenerators as $index => $turbine)
                        <div class="turbine-generator-entry mb-3 d-flex gap-2 align-items-center">
                            <input type="hidden" name="turbine_generators[{{ $index }}][id]"
                                value="{{ $turbine->id }}">
                            <input type="text" name="turbine_generators[{{ $index }}][name]"
                                value="{{ $turbine->name }}" class="form-control" required>
                            <button type="button" class="btn btn-outline-danger btn-sm remove-turbine-generator mt-2"
                                title="Хасах">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M4 7l16 0" />
                                    <path d="M10 11l0 6" />
                                    <path d="M14 11l0 6" />
                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="add-turbine-generator">
                    <i class="ti ti-plus"></i> Турбингенератор нэмэх
                </button>

                <!-- Save Button -->
                <div class="mt-4">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="ti ti-device-floppy"></i> Хадгалах
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        let boilerCount = {{ $powerPlant->boilers->count() }};
        let turbineCount = {{ $powerPlant->turbineGenerators->count() }};

        // Add new boiler
        document.getElementById('add-boiler').addEventListener('click', function() {
            const boilerDiv = document.createElement('div');
            boilerDiv.classList.add('boiler-entry', 'mb-2');
            boilerDiv.innerHTML = `
                <input type="text" name="boilers[${boilerCount}][name]" class="form-control" required>
                <button type="button" class="btn btn-outline-danger btn-sm remove-boiler mt-2" title="Хасах">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 7l16 0" />
                                <path d="M10 11l0 6" />
                                <path d="M14 11l0 6" />
                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                            </svg>
                </button>
            `;
            document.getElementById('boilers').appendChild(boilerDiv);
            boilerCount++;
        });

        // Add new turbine generator
        document.getElementById('add-turbine-generator').addEventListener('click', function() {
            const turbineDiv = document.createElement('div');
            turbineDiv.classList.add('turbine-generator-entry', 'mb-2');
            turbineDiv.innerHTML = `
                <input type="text" name="turbine_generators[${turbineCount}][name]" class="form-control" required>
                <button type="button" class="btn btn-outline-danger btn-sm remove-turbine-generator mt-2" title="Хасах">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 7l16 0" />
                                <path d="M10 11l0 6" />
                                <path d="M14 11l0 6" />
                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                            </svg>
                </button>
            `;
            document.getElementById('turbine_generators').appendChild(turbineDiv);
            turbineCount++;
        });

        // Delegate event listeners for dynamic delete buttons
        document.addEventListener('click', function(event) {
            if (event.target.closest('.remove-boiler')) {
                event.target.closest('.boiler-entry').remove();
            }
            if (event.target.closest('.remove-turbine-generator')) {
                event.target.closest('.turbine-generator-entry').remove();
            }
        });
    </script>
@endsection
