<?php

namespace App\Http\Controllers\Tienda;

use App\Mail\SaleMail;
use App\Models\Sale\Cart;
use App\Models\Sale\Sale;
use Illuminate\Http\Request;
use App\Models\CoursesStudent;
use App\Models\Sale\SaleDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        $request->request->add(["user_id" => auth("api")->user()->id]);
        $sale = Sale::create($request->all());

        $carts = Cart::where("user_id",auth('api')->user()->id)->get();

        foreach ($carts as $key => $cart) {
            // $cart->delete();
            $new_detail = [];
            $new_detail = $cart->toArray();
            $new_detail["sale_id"] = $sale->id;
            SaleDetail::create($new_detail);
            CoursesStudent::create([
                "course_id" =>$new_detail["course_id"],
                "user_id" => auth('api')->user()->id,
            ]);
        }   

        // AQUI VA EL CODIGO PARA EL ENVIO DE CORREO
        Mail::to($sale->user->email)->send(new SaleMail($sale)); 
        return response()->json(["message" => 200, "message_text" => "LOS CURSOS SE HAN ADQUIRIDO CORRECTAMENTE"]);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
