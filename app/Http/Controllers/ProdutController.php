<?php

namespace App\Http\Controllers;

use App\Models\produt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProdutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $searchCode = request('code');
        $query = produt::query();

        if (!empty($searchCode)) {
            $query->where('code', 'like', $searchCode);
        }
        $products = $query->paginate(50);
        return response()->json(['products' => $products], 200);
    }




    /**
     * Store a newly created resource in storage.
     */
    public function store()
    {
        $validator = Validator::make(request()->all(), [
            'name' => 'required|string|max:255',
            'img' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string|min:8|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // معالجة الصورة وحفظها
        if (request()->hasFile('img')) {
            $image = request()->file('img');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
        }

        // إنشاء المنتج
        $product = produt::create([
            'name' => request('name'),
            'img' => $imageName,
            'price' => request('price'),
            'description' => request('description'),
        ]);

        return response()->json($product, 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(produt $product)
    {
        return response()->json($product, 200);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, produt $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'img' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'price' => 'sometimes|numeric|min:0',
            'description' => 'sometimes|string|min:8|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // إذا تم تحميل صورة جديدة، استبدال القديمة
        if ($request->hasFile('img')) {
            // حذف الصورة القديمة
            if (file_exists(public_path('images/' . $product->img))) {
                unlink(public_path('images/' . $product->img));
            }

            $image = $request->file('img');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);

            $product->img = $imageName;
        }

        // تحديث باقي الحقول إذا تم تمريرها
        $product->update($request->only(['name', 'price', 'description']));

        return response()->json($product, 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(produt $product)
    {
        // حذف الصورة المرتبطة بالمنتج
        if (file_exists(public_path('images/' . $product->img))) {
            unlink(public_path('images/' . $product->img));
        }

        // حذف المنتج
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully'], 200);
    }

}
