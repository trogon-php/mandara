<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\Courses\CourseService;
use App\Services\CourseUnits\CourseUnitService;
use App\Services\CourseMaterials\CourseMaterialService;

class CourseContentController extends AdminBaseController
{
    protected CourseService $courseService;
    protected CourseUnitService $courseUnitService;
    protected CourseMaterialService $courseMaterialService;

    public function __construct(
        CourseService $courseService,
        CourseUnitService $courseUnitService,
        CourseMaterialService $courseMaterialService
    ) {
        $this->courseService = $courseService;
        $this->courseUnitService = $courseUnitService;
        $this->courseMaterialService = $courseMaterialService;

        $this->middleware('can:courses/edit')->only(['index', 'manageUnits', 'manageMaterials']);
    }

    /**
     * Main course content management page
     */
    public function index($courseId)
    {
        $course = $this->courseService->find($courseId, ['category']);
        
        if (!$course) {
            return redirect()->route('admin.courses.index')
                           ->with('error', 'Course not found.');
        }

        // Get the complete course structure with materials
        $courseStructure = $this->buildCourseStructure($course);
        
        // Get selected unit ID from request
        $selectedUnitId = request('unit_id');

        return view('admin.course_content.index', [
            'page_title' => 'Course Content Management - ' . $course->title,
            'course' => $course,
            'courseStructure' => $courseStructure,
            'selectedUnitId' => $selectedUnitId,
        ]);
    }

    /**
     * Manage units for a specific course
     */
    public function manageUnits($courseId)
    {
        $course = $this->courseService->find($courseId);
        
        if (!$course) {
            return redirect()->route('admin.courses.index')
                           ->with('error', 'Course not found.');
        }

        $units = $this->courseUnitService->getTreeByCourse($courseId);

        return view('admin.course_content.units', [
            'page_title' => 'Manage Course Units - ' . $course->title,
            'course' => $course,
            'units' => $units,
        ]);
    }

    /**
     * Manage materials for a specific unit
     */
    public function manageMaterials($courseId, $unitId)
    {
        $course = $this->courseService->find($courseId);
        $unit = $this->courseUnitService->find($unitId);
        
        if (!$course || !$unit) {
            return redirect()->route('admin.courses.index')
                           ->with('error', 'Course or unit not found.');
        }

        $materials = $this->courseMaterialService->getByCourseAndUnit($courseId, $unitId);

        return view('admin.course_content.materials', [
            'page_title' => 'Manage Materials - ' . $unit->title,
            'course' => $course,
            'unit' => $unit,
            'materials' => $materials,
        ]);
    }

    /**
     * Build complete course structure with subjects as cards and nested units
     */
    private function buildCourseStructure($course)
    {
        // Get all units for the course
        $allUnits = $this->courseUnitService->getByCourse($course->id);
        
        // Group units by subjects (first level units with type 'subject')
        $subjects = $allUnits->whereNull('parent_id');
        
        return $subjects->map(function ($subject) use ($allUnits) {
            // Build the complete structure for this subject
            $subject->units = $this->buildSubjectUnits($subject, $allUnits);
            $subject->total_units = $this->countTotalUnits($subject->units);
            $subject->total_materials = $this->countTotalMaterials($subject->units);
            
            return $subject;
        });
    }

    /**
     * Build nested units structure for a subject
     */
    private function buildSubjectUnits($subject, $allUnits)
    {
        // Get all child units of this subject
        $childUnits = $allUnits->where('parent_id', $subject->id);
        
        return $childUnits->map(function ($unit) use ($allUnits) {
            $unit->materials = $this->courseMaterialService->getByCourseUnit($unit->id);
            $unit->children = $this->buildSubjectUnits($unit, $allUnits);
            
            return $unit;
        });
    }

    /**
     * Count total units recursively
     */
    private function countTotalUnits($units)
    {
        $count = $units->count();
        
        foreach ($units as $unit) {
            if ($unit->children && $unit->children->count() > 0) {
                $count += $this->countTotalUnits($unit->children);
            }
        }
        
        return $count;
    }

    /**
     * Count total materials recursively
     */
    private function countTotalMaterials($units)
    {
        $count = 0;
        
        foreach ($units as $unit) {
            $count += $unit->materials ? $unit->materials->count() : 0;
            
            if ($unit->children && $unit->children->count() > 0) {
                $count += $this->countTotalMaterials($unit->children);
            }
        }
        
        return $count;
    }

    /**
     * Recursively build unit structure with materials
     */
    private function buildUnitStructure($unit)
    {
        $unit->materials = $this->courseMaterialService->getByCourseUnit($unit->id);
        $unit->children = $unit->children->map(function ($child) {
            return $this->buildUnitStructure($child);
        });
        
        return $unit;
    }
}