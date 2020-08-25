<?php

namespace App\Http\Controllers;

use App\Coupon;
use App\Jobs\UpdateCoupon;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;

class CouponsController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $coupon=Coupon::where('code',$request->coupon_code)->first();
        if (!$coupon){
            return redirect()->route('checkout.index')->withErrors('Invalid Coupon Code .');
        }
        dispatch_now( new UpdateCoupon($coupon));

        return redirect()->route('checkout.index')->with('success','Coupon has been applied');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        session()->forget('coupon');
        return redirect()->route('checkout.index')->with('success','Coupon Has been Removed');
    }
}
