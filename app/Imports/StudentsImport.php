<?php

namespace App\Imports;

use App\Models\Classes;
use App\Models\Section;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;

class StudentsImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $classId = self::getClassId($row['class']);

        return new Student([
            'name' => $row['name'],
            'email' => $row['email'],
            'class_id' => $classId,
            'section_id' => $this->getSectionId($classId, $row['section']),
        ]);
    }

    public function getClassId(string $class)
    {
        return Classes::query()
            ->where('class_id', $class)
            ->first()
            ->id;
    }

    public function getSectionId(int $classId, string $section)
    {
        return Section::query()
            ->where('name', $section)
            ->where('class_id', $classId)
            ->first()
            ->id;
    }
}
