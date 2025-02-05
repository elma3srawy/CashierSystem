<?php

namespace App\Http\Controllers\Section;

use App\Models\Section;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SectionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('sections.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'unique:sections,name', 'min:2', 'max:20'],
            'title' => ['nullable', 'string', 'min:2', 'max:20'],
            'section_id' => ['nullable' , 'integer' , 'exists:sections,id'],
            'status' => ['sometimes', "boolean"],
        ]);
        

        $count = !is_null($request->section_id) ? Section::find($request->section_id)->products()->count() : 0;

        if($count > 0){
            return back()->with('error' , 'مينفعش تخلي القسم دا قسم رئيسي عشان فيه منتجات');
        }

        if(isset($request->section_id) && isset($status))
        {
            return back()->with("error" , 'error');
        }

        Section::create($request->only(['name', 'title' ,'section_id' , 'status']));

        return back()->with('success' , 'تم انشاء قسم جديد بنجاح!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $section =  Section::find($id);
        return view('sections.show' , ['section' => $section]);
    }
    /**
     * edit the specified resource.
     */
    public function edit(Section $section)
    {
        return view('sections.edit' , ['section' => $section]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Section $section)
    {

        $request->validate([
            'name' => ['required' , 'string' , 'min:2' , 'max:20' , 'unique:sections,name,'.$section->id],
            'title' => ['nullable' , 'string' , 'min:2' , 'max:20'],
        ]);

        $section->update($request->only('name' , 'title'));

        $this->changeStatus($request, $section);

        $products = $section->products()->get();

        foreach($products AS $product) {
            $product->title = $section->title;
            $product->save();
        }

        return to_route('section.index')->with('success' ,'تم تحديث القسم بنجاح!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $section  = Section::whereId($id)->first();
        if($section->subSection()->count() > 0)
        {
            return to_route('section.index')->with('error', 'مش هينفع تمسح القسم دا عشان ليه اقسام داخليه');
        }
        if($section->products()->count() > 0)
        {
            return to_route('section.index')->with('error', 'مش هينفع تمسح القسم دا عشان ليه منتجات');
        }
        $section->delete();
        return back()->with('success' , 'تم حذف القسم بنجاح!');
    }

    public function changeStatus(Request $request , Section $section)
    {
        $request->validate(['status' => ["sometimes" , "boolean"]]);

        $section->status = $request->input('status');
        $section->save();

        return back()->with('success',"تم تغير حالة القسم بنجاح");
    }
}
