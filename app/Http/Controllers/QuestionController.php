<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Mode;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $questions = Question::with('modes')->get();
        $modes = Mode::all();

        return Inertia::render('Questions/Show', [
            'questions' => $questions,
            'modes' => $modes,
            'routeName' => request()->route()->getName(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Question $question)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Question $question)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Question $question)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|string',
            'modes' => 'array', // Ensure modes is an array
        ]);



        $question->update([
            'question_text' => $validated['question_text'],
            'question_type' => $validated['question_type'],
        ]);



        // Sync modes if provided
        if (isset($validated['modes'])) {
            $mode_ids = collect($validated['modes'])->pluck('id')->sort()->values()->toArray();
            $question->modes()->sync($mode_ids);
        }

        return response()->json(['message' => 'Question updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Question $question)
    {
        //
    }
}
