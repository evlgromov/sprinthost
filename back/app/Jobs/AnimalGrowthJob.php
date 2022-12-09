<?php

namespace App\Jobs;

use App\Events\AnimalAgeUpEvent;
use App\Events\AnimalKillEvent;
use App\Models\Animal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AnimalGrowthJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $animal;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Animal $animal)
    {
        $this->animal = $animal;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        for ($i = 0; $i < $this->animal->animal_kind->max_size; $i = $this->animal->size) {
            $animal = Animal::find($this->animal->id);
            $this->animal->size = $animal->size;
            $this->animal->age = $animal->age;
            $age_by_size = $this->animal->animal_kind->max_age / $this->animal->animal_kind->max_size;
            $this->animal->size += 1;
            $this->animal->age += $age_by_size;

            $this->animal->save();
            AnimalAgeUpEvent::dispatch($this->animal);
            sleep(1);
        }
        AnimalKillEvent::dispatch($this->animal);
    }
}
