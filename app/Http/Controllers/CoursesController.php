<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use Illuminate\Support\Facades\Validator;

class CoursesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Course::all(), 200);
    }


    public function orderedIndex($param, $order)
    {
        if (!in_array($param, Course::$sortableFields) || ($order !== 'desc' && $order !== 'asc')) {
            return response()->json([
                'error' => 'Params must be year, id or cfu and order must be desc or asc'
            ], 400);
        }
        return response()->json(Course::orderBy($param, $order)->get(), 200);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|max:255|min:3',
            'year' => 'required|max:2',
            'cfu' => 'required|numeric'
        ]);

        if ($validation->fails()) {
            return response()->json([
                'message' => 'Course not created successfully',
                'errors' => $validation->errors()
            ], 400);
        }

        $course = new Course();
        $course->name = $request->input('name');
        $course->year = $request->input('year');
        $course->cfu = $request->input('cfu');
        $course->save();

        return response()->json([
            'message' => 'Course created successfully',
            'errors' => null
        ], 200);
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $course = Course::find($id);
        if ($course === null) {
            return response()->json(null, 404);
        }
        return response()->json(Course::find($id), 200);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $course = Course::find($id);

        $jsonResponse = [
            'message' => '',
            'error' => null
        ];

        if ($course === null) {
            $jsonResponse['message'] = 'Course not found';
            return response()->json($jsonResponse, 404);
        }

        $validation = Validator::make($request->all(), [
            'name' => 'required_without_all:year,cfu|max:255|min:3',
            'year' => 'required_without_all:name,cfu|max:2',
            'cfu' => 'required_without_all:name,year|numeric'
        ]);

        if ($validation->fails()) {
            $jsonResponse['message'] = 'Course not created successfully';
            $jsonResponse['errors'] = $validation->errors();
            return response()->json($jsonResponse, 400);
        }

        $response = $course->update($request->all());

        if (!$response) {
            $jsonResponse['message'] = 'Server Error, contact the SysAdmin';
            return response()->json($jsonResponse, 500);
        }

        $jsonResponse['message'] = 'Course updated successfully';
        return response()->json($jsonResponse, 200);
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $course = Course::find($id);
        
        if ($course === null) {
            return response()->json([
                'message' => 'Course not found'
            ], 404);
        }

        $response = $course->delete();
        if (!$response) {
            return response()->json([
                'message' => 'Server Error, contact the SysAdmin'
            ], 500);
        }

        return response()->json([
            'message' => 'Course successfully deleted'
        ], 200);
    }
}