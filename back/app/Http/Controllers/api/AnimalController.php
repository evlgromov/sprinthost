<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnimalCreateRequest;
use App\Jobs\AnimalGrowthJob;
use App\Models\AnimalKind;
use App\Models\Animal;
use Illuminate\Http\Request;

class AnimalController extends Controller
{
    public function getAnimalKinds()
    {
        return AnimalKind::all();
    }

    public function getAnimals()
    {
        return Animal::with(['animal_kind'])->get();
    }

    public function getAnimal($id)
    {
        $animal = Animal::with('animal_kind')->find($id);

        return response()->json(
            [
                'error' => false,
                'data' => $animal
            ], 200
        );
    }

    public function createAnimal(AnimalCreateRequest $request)
    {
        try {
            $animal = Animal::create($request->validated());

            $animal = Animal::with('animal_kind')->find($animal->id);

            AnimalGrowthJob::dispatch($animal)->onQueue('animals');

            return response()->json(
                [
                    'error' => false,
                    'animal' => $animal,
                    'message' => 'Запись успешно создана'
                ], 201
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => true,
                    'message' => $e->getMessage()
                ], 500
            );
        }

    }

    public function deleteAnimal($id)
    {
        $animal = Animal::findOrFail($id);
        $animal->delete();

        return response()->json(
            [
                'error' => false,
                'message' => 'Запись успешно удалена'
            ], 200
        );
    }

    public function toGetOld($id)
    {
        $animal = Animal::with('animal_kind')->find($id);
        $k = ((int) $animal->animal_kind->max_age / (int) $animal->animal_kind->max_size);
        $n = (int) ($animal->animal_kind->max_age - 1) / $k;

        $animal->update([
            'age' => $animal->animal_kind->max_age - 1,
            'size' => $n
        ]);

        $animal->save();

        return response()->json(
            [
                'error' => false,
                'message' => 'Запись успешно изменена'
            ], 200
        );
    }
}
