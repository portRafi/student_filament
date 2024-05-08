<?php

namespace Database\Seeders;

use App\Models\Section;
use Database\Factories\ClassesFactory;
use Database\Factories\SectionFactory;
use Database\Factories\StudentFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class ClassesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ClassesFactory::new()
            ->count(10)
            ->sequence(fn($sequence) => ['name' => 'Class ' . $sequence->index + 1])
            ->has(
                SectionFactory::new()
                    ->count(2)
                    ->state(
                        new Sequence(
                            ['name' => 'Section A'],
                            ['name' => 'Section B'],
                        )
                    )
                    ->has(
                        StudentFactory::new()
                            ->count(5)
                            ->state(function (array $attributes, Section $section) {
                                return ['class_id' => $section->class_id];
                            })
                    )
            )
            ->create();
    }
}
