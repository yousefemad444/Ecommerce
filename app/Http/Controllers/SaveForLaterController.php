<?php

namespace App\Http\Controllers;

use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;

class SaveForLaterController extends Controller
{

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Cart::instance('saveForLater')->remove($id);
        return back()->with('success','Item Has been Removed !');
    }

    /**
     * Switch item form save to later to Cart .
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function switchToCart($id)
    {
        $item=Cart::instance('saveForLater')->get($id);
        Cart::instance('saveForLater')->remove($id);

        $duplicates=Cart::instance('default')->search(function ($cartItem,$rowId) use ($id){
            return $rowId ===$id;
        });

        if ($duplicates->isNotEmpty())
        {
            return redirect()->route('cart.index')->with('success','Item is already In Your Cart !');
        }

        Cart::instance('default')->add($item->id,$item->name,1,$item->price)->associate('App\Product');

        return redirect()->route('cart.index')->with('success','Item Has been Moved To Cart !');
    }
}
