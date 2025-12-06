<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the departments.
     */
    public function index()
    {
        return response()->json([
            'data' => Department::all(),
            'message' => 'Departments retrieved successfully'
        ]);
    }

    /**
     * Store a newly created department in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:departments',
            'code' => 'required|string|unique:departments',
            'head_id' => 'nullable|exists:users,id',
        ]);

        $department = Department::create($validated);

        return response()->json([
            'data' => $department,
            'message' => 'Department created successfully'
        ], 201);
    }

    /**
     * Display the specified department.
     */
    public function show(Department $department)
    {
        return response()->json([
            'data' => $department,
            'message' => 'Department retrieved successfully'
        ]);
    }

    /**
     * Update the specified department in storage.
     */
    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:departments,name,' . $department->id,
            'code' => 'required|string|unique:departments,code,' . $department->id,
            'head_id' => 'nullable|exists:users,id',
        ]);

        $department->update($validated);

        return response()->json([
            'data' => $department,
            'message' => 'Department updated successfully'
        ]);
    }

    /**
     * Remove the specified department from storage.
     */
    public function destroy(Department $department)
    {
        $department->delete();

        return response()->json([
            'message' => 'Department deleted successfully'
        ]);
    }
}
