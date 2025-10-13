@extends('layouts.admin')

@section('content')
    <div class="container-xl">
        <h1 class="page-title">Станц, Зуух, Турбингенератор нэмэх</h1>

        <form action="{{ route('power-plants.store-equipment') }}" method="POST" class="card">
            @csrf
            <div class="card-body">

                <div class="mb-3">
                    <label class="form-label" for="power_plant_id">Станц</label>
                    <select name="power_plant_id" id="power_plant_id" class="form-select">
                        @foreach ($powerPlants as $plant)
                            <option value="{{ $plant->id }}" {{ $powerPlant->id == $plant->id ? 'selected' : '' }}>
                                {{ $plant->name }}
                            </option>
                        @endforeach
                    </select>
                </div>


                <h3 class="mb-2">Зуух</h3>
                <div id="boilers">
                    <div class="boiler-entry mb-3 d-flex align-items-center gap-2">
                        <div class="flex-grow-1">
                            <input type="text" name="boilers[0][name]" class="form-control" required>
                        </div>
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

                </div>
                <button type="button" class="btn btn-outline-primary mb-4" id="add-boiler">
                    <i class="ti ti-plus"></i> Зуух нэмэх
                </button>

                <h3 class="mb-2">Турбингенератор</h3>
                <div id="turbine-generators">
                    <div class="turbine-generator-entry mb-3 d-flex gap-2 align-items-center">
                        <div class="flex-grow-1">
                            <input type="text" name="turbine_generators[0][name]" class="form-control" required>
                        </div>
                        <button type="button" class="btn btn-outline-danger remove-turbine-generator">
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
                </div>
                <button type="button" class="btn btn-outline-primary mb-4" id="add-turbine-generator">
                    <i class="ti ti-plus"></i> Турбингенератор нэмэх
                </button>


                <div class="mt-4">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="ti ti-device-floppy"></i> Хадгалах
                    </button>
                </div>

            </div>
        </form>
    </div>

    <script>
        let boilerCount = 1;
        let turbineGeneratorCount = 1;

        document.getElementById('add-boiler').addEventListener('click', function() {
            const boilerDiv = document.createElement('div');
            boilerDiv.classList.add('boiler-entry', 'mb-3', 'd-flex', 'gap-2', 'align-items-center');
            boilerDiv.innerHTML = `
                <div class="flex-grow-1">
                    <input type="text" name="boilers[${boilerCount}][name]" class="form-control" required>
                </div>
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
            `;
            document.getElementById('boilers').appendChild(boilerDiv);
            boilerCount++;
        });

        document.getElementById('add-turbine-generator').addEventListener('click', function() {
            const turbineDiv = document.createElement('div');
            turbineDiv.classList.add('turbine-generator-entry', 'mb-3', 'd-flex', 'gap-2', 'align-items-center');
            turbineDiv.innerHTML = `
                <div class="flex-grow-1">
                    <input type="text" name="turbine_generators[${turbineGeneratorCount}][name]" class="form-control" required>
                </div>
                <button type="button" class="btn btn-outline-danger remove-turbine-generator">
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
            document.getElementById('turbine-generators').appendChild(turbineDiv);
            turbineGeneratorCount++;
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
