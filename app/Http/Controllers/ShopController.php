<?php

namespace App\Http\Controllers;

use App\Category;
use App\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pagination=9;
        $categories=Category::all();
        if (request()->category){
            $products=Product::with('categories')->whereHas('categories',function ($query){
               $query->where('slug',\request()->category);
            });
            $categoryName=optional($categories->where('slug',\request()->category)->first())->name;
        }else{
            $products = Product::where('featured',true);
            $categoryName='Featured';
        }

        if (request()->sort =='low_high'){
            $products=$products->orderBy('price')->paginate($pagination);
        }elseif (request()->sort == 'high_low'){
            $products=$products->orderByDesc('price')->paginate($pagination);
        }else {
            $products=$products->paginate($pagination);
        }

        return view('shop',compact('products','categories','categoryName'));
    }

    public function show($slug)
    {
        $product= Product::where('slug',$slug)->firstOrFail();
        $mightAlsoLike= Product::where('slug','!=',$slug)->inRandomOrder()->take(4)->get();

        $stockLevel= $this->getStockLevel($product->quantity);
        if ($product->quantity > setting('site.stock_threshold')){
            $stockLevel='<div class="badge badge-success">In Stock</div>';
        }else if($product->quantity <= setting('site.stock_threshold') && $product->quantity > 0){
            $stockLevel='<div class="badge badge-warning">Low Stock</div>';
        }else{
            $stockLevel='<div class="badge badge-danger">Not Available</div>';
        }
        return view('product',compact('product','mightAlsoLike','stockLevel'));
    }

    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|min:3',
        ]);

        $query = $request->input('query');
        $products = Product::where('name','like',"%$query%")
                             ->orWhere('details', 'like', "%$query%")
                              ->orWhere('description', 'like', "%$query%")
                                ->paginate(10);

//        $products=Product::search($query)->paginate(10);

        return view('search-results',compact('products'));
    }


    protected function getStockLevel($quantity){
        if ($quantity > setting('site.stock_threshold')){
            $stockLevel='<div class="badge badge-success">In Stock</div>';
        }else if($quantity <= setting('site.stock_threshold') && $quantity > 0){
            $stockLevel='<div class="badge badge-warning">Low Stock</div>';
        }else{
            $stockLevel='<div class="badge badge-danger">Not Available</div>';
        }

        return $stockLevel;
    }

}
