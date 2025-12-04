@extends('layouts.admin')

@section('content')
<div class="container mt-4">

    <h3>Хэрэглэгч бүрийн цагийн ачааллын мэдээ ({{ $date }})</h3>

    <table class="table table-bordered table-striped table-sm">
        <thead>
            <tr>
                <th>Станц нэр</th>
                <th>Тоноглол нэр</th>
                <th>01</th>
                <th>02</th>
                <th>03</th>
                <th>04</th>
                <th>05</th>
                <th>06</th>
                <th>07</th>
                <th>08</th>
                <th>09</th>
                <th>10</th>
                <th>11</th>
                <th>12</th>
                <th>13</th>
                <th>14</th>
                <th>15</th>
                <th>16</th>
                <th>17</th>
                <th>18</th>
                <th>19</th>
                <th>20</th>
                <th>21</th>
                <th>22</th>
                <th>23</th>
                <th>24</th>      
            </tr>
        </thead>

        <tbody>
          @php
                // Станцын мөрийн count бэлдэх
                $plantCounts = [];
                foreach ($data as $row) {
                    $plantId = $row['powerPlant']->id;
                    if (!isset($plantCounts[$plantId])) {
                        $plantCounts[$plantId] = 0;
                    }
                    $plantCounts[$plantId]++;
                }

                $printedPlants = [];
            @endphp

            @foreach($data as $row)
                @php
                    $plantId = $row['powerPlant']->id;
                @endphp
                <tr>
                    {{-- Станцын нэрийг rowspan-тэй хэвлэх --}}
                    @if(!in_array($plantId, $printedPlants))
                        <td rowspan="{{ $plantCounts[$plantId] }}">
                            {{ $row['powerPlant']->name ?? '-' }}
                        </td>
                        @php
                            $printedPlants[] = $plantId;
                        @endphp
                    @endif

                    {{-- Тоноглол --}}
                    <td>{{ $row['equipment']->power_equipment ?? '-' }}</td>

                    {{-- Цагууд --}}
                    @foreach($times as $time)
                        <td>{{ $row[$time] ?? '-' }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection
