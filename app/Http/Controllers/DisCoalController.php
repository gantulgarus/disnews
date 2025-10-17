<?php

namespace App\Http\Controllers;

use App\Models\DisCoal;
use Illuminate\Http\Request;

class DisCoalController extends Controller
{
    
    public function index()
    {
        $disCoals = DisCoal::latest()->paginate(10); // many records
        return view('dis_coal.index', compact('disCoals'));
    }

    // Show create form
    public function create()
    {
        return view('dis_coal.create');
    }

    // Store new record
    public function store(Request $request)
    {
        //dd($request->all());
            $request->validate([
            'date' => 'required|date',
            'CAME_TRAIN' => 'nullable|integer',
            'UNLOADING_TRAIN' => 'nullable|integer',
            'ULDSEIN_TRAIN' => 'nullable|integer',
            'COAL_INCOME' => 'nullable|integer',
            'COAL_OUTCOME' => 'nullable|integer',
            'COAL_TRAIN_QUANTITY' => 'nullable|numeric',
            'COAL_REMAIN' => 'nullable|integer',
            'COAL_REMAIN_BYDAY' => 'nullable|numeric',
            'COAL_REMAIN_BYWINTERDAY' => 'nullable|integer',
            'MAZUT_INCOME' => 'nullable|integer',
            'MAZUT_OUTCOME' => 'nullable|integer',
            'MAZUT_TRAIN_QUANTITY' => 'nullable|integer',
            'MAZUT_REMAIN' => 'nullable|integer',
            'BAGANUUR_MINING_COAL_D' => 'nullable|integer',
            'SHARINGOL_MINING_COAL_D' => 'nullable|integer',
            'SHIVEEOVOO_MINING_COAL' => 'nullable|integer',
            'OTHER_MINIG_COAL_SUPPLY' => 'nullable|integer',
            'FUEL_SENDING_EMPL' => 'nullable|integer',
            'FUEL_RECEIVER_EMPL' => 'nullable|integer',
            'ORG_NAME' => 'required|string|max:255',
        ]);

        $data = $request->all();

         // Calculate COAL_REMAIN_BYDAY = COAL_REMAIN / COAL_OUTCOME
          if (!empty($data['COAL_OUTCOME']) && $data['COAL_OUTCOME'] > 0) {
              $data['COAL_REMAIN_BYDAY'] = round($data['COAL_REMAIN'] / $data['COAL_OUTCOME'], 2);
             } else {
                $data['COAL_REMAIN_BYDAY'] = 0;
                }

               DisCoal::create($data);
        
            return redirect()->route('dis_coal.index')
                     ->with('success', 'Амжилттай.');
           
    }

    // Show one record
    public function show(string $id)
    {
        $disCoal = DisCoal::findOrFail($id); // one record
        return view('dis_coal.show', compact('disCoal'));
    }

    
    public function edit(string $id)
    {
        $disCoal = DisCoal::findOrFail($id); // one record
        return view('dis_coal.edit', compact('disCoal'));
    }

   
    public function update(Request $request, string $id)
{
    $request->validate([
        'date' => 'required|date',
        'CAME_TRAIN' => 'nullable|integer',
        'UNLOADING_TRAIN' => 'nullable|integer',
        'ULDSEIN_TRAIN' => 'nullable|integer',
        'COAL_INCOME' => 'nullable|integer',
        'COAL_OUTCOME' => 'nullable|integer',
        'COAL_TRAIN_QUANTITY' => 'nullable|numeric',
        'COAL_REMAIN' => 'nullable|integer',
        'COAL_REMAIN_BYWINTERDAY' => 'nullable|integer',
        'MAZUT_INCOME' => 'nullable|integer',
        'MAZUT_OUTCOME' => 'nullable|integer',
        'MAZUT_TRAIN_QUANTITY' => 'nullable|integer',
        'MAZUT_REMAIN' => 'nullable|integer',
        'BAGANUUR_MINING_COAL_D' => 'nullable|integer',
        'SHARINGOL_MINING_COAL_D' => 'nullable|integer',
        'SHIVEEOVOO_MINING_COAL' => 'nullable|integer',
        'OTHER_MINIG_COAL_SUPPLY' => 'nullable|integer',
        'FUEL_SENDING_EMPL' => 'nullable|integer',
        'FUEL_RECEIVER_EMPL' => 'nullable|integer',
        'ORG_NAME' => 'required|string|max:255',
    ]);

         $data = $request->all();

          if (!empty($data['COAL_OUTCOME']) && $data['COAL_OUTCOME'] > 0) {
          $data['COAL_REMAIN_BYDAY'] = round($data['COAL_REMAIN'] / $data['COAL_OUTCOME'], 2);
           } else {
                $data['COAL_REMAIN_BYDAY'] = 0;
                 }

          $disCoal = DisCoal::findOrFail($id);
          $disCoal->update($data);

            return redirect()->route('dis_coal.index')
                    ->with('success', 'Амжилттай шинэчлэгдсэн..');
}

    // Delete record
    public function destroy(string $id)
    {
        $disCoal = DisCoal::findOrFail($id); // one record
        $disCoal->delete();

        return redirect()->route('dis_coal.index')
            ->with('success', 'Амжилттай устгагдсан.');
    }
}
