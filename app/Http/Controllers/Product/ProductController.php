<?php

namespace App\Http\Controllers\Product;

use App\Traits\File;
use App\Models\Product;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    use File;
    /**
     * Display a listing of the resource.
     */
    public function index(string $id)
    {
        $count = Section::whereSection_id($id)->count();
        if($count > 0)
        {
            return abort('404');
        }
        $section = Section::with('products')->select('sections.id' , 'name')->findOrFail($id);
        $products = $section->products()->paginate(PAGINATE);

        return view('products.index' , ['section' => $section , 'products' => $products ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $id)
    {
        $count = Section::whereSection_id($id)->count();
        if($count > 0)
        {
            return abort('404');
        }
        return view('products.create' , ['section_id' => $id]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $title = Section::find($request->section_id)->title;
        $validated = collect($request->validated())->forget('image')->all();
        $product = Product::create($validated + ['title' => $title]);

        if($request->hasFile('image'))
        {
            static::upload($request->file('image') , '/products')->storeAs($product, 'image');
        }
        return back()->with('success' , 'تم انشاء منتج جديد بنجاح!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('products.edit' , ['product' => $product]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product)
    {
        $validated = collect($request->validated())->forget('image')->all();
        if($request->hasFile('image'))
        {
            static::updateAs($request->file('image'), '/products', $product, 'image');
        }
        $product->update($validated);
        return to_route('products.index' , $product->section_id)->with('success', 'تم تحديث المنتج بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // if($product->orders()->count() > 0){
        //     return back()->with('error' , 'Product has invoice!');
        // }
        if(!is_null($product->image) && Storage::exists($product->image))
        {
            Storage::delete($product->image);
        }
        $product->delete();
        return back()->with('success' , 'تم حذف المنتج بنجاح!');
    }
    public function search(Request $request)
    {
        try {
            $query = $request->input('search');
            // $status = $request->input('status');

            $queryBuilder = DB::table('products')
                ->where('section_id' , $request->sectionId)
                ->select(
                    'products.id',
                    'products.quantity',
                    'products.color',
                    'products.image',
                    'products.model',
                    'products.size',
                    'products.created_at',
                );
            if ($query) {
                $queryBuilder->where(function ($subQuery) use ($query) {
                    $subQuery->where('products.color', 'like', "%{$query}%")
                            // ->orWhere('products.quantity', 'like', "%{$query}%")
                            ->orWhere('products.size', 'like', "%{$query}%")
                            ->orWhere('products.model', 'like', "%{$query}%")
                            ->orWhere('products.created_at', 'like', "%{$query}%");
                });
            }

            // if ($status) {
            //     $queryBuilder->where('products.status', $status);
            // }
            $products = $queryBuilder->paginate(PAGINATE);

            return response()->json([
                'tableRows' => view('partials.products_table', ['products' => $products])->render(),
                'pagination' => $products->appends(['search' => $query])->links()->render()
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
