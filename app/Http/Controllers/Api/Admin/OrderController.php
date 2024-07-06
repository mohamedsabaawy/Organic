<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\InvoiceResource;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use function included\sendResponse;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $invoices = Invoice::with(['items', 'offers', 'address', 'invoiceStatuses', 'client']);

        if ($request->has('status')) {
            $invoices = $invoices->where('status', $request->status);
        }

        $invoices = $invoices->orderBy('id','desc')->get();

        return sendResponse(InvoiceResource::collection($invoices),'successful',1);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request ,$id)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * add delivery price to an invoice.
     */
    public function deliveryPrice(Request $request,string $id)
    {
//        return $request->ipinfo->ip;
        $validation = Validator::make($request->all(),[
            'delivery_price' => 'required|numeric',
        ]);
        if (count($validation->errors()) > 0)
            return sendResponse($validation->errors(),'validation error',0);
        if (!$invoice = Invoice::find($id))
            return sendResponse([],'no data found',0);
        $invoice->update([
            'delivery_price'=>$request->delivery_price,
        ]);
        return sendResponse([],'successful',1);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (!$invoice = Invoice::find($id))
            return sendResponse([],'not found',0);
        switch ($invoice->status){
            case 'pending'://pending
            {
                $invoice->update([
                    'status' => "underPrepare",
                ]);
                $invoice->invoiceStatuses()->create([
                    'status' => 'underPrepare' //'pending','underPrepare','onTheWay','delivery'
                ]);
                break;
            }
            case 'underPrepare':
            {
                $invoice->update([
                    'status' => "onTheWay"
                ]);
                $invoice->invoiceStatuses()->create([
                    'status' => 'onTheWay' //'pending','underPrepare','onTheWay','delivery'
                ]);
                break;
            }
            case 'onTheWay':
            {
                $invoice->update([
                    'status' => "delivery"
                ]);
                $invoice->invoiceStatuses()->create([
                    'status' => 'delivery' //'pending','underPrepare','onTheWay','delivery'
                ]);
                break;
            }
            default:
                break;
        }
        return sendResponse(InvoiceResource::make($invoice),'successful',1);
    }
    /**
     * Update the specified resource in storage.
     */
    public function rollback(Request $request, string $id)
    {
        if (!$invoice = Invoice::find($id))
            return sendResponse([],'not found',0);
        switch ($invoice->status){
            case 'delivery'://pending
            {
                $invoice->update([
                    'status' => "onTheWay "
                ]);
                $invoice->invoiceStatuses()->where('status','delivery')->delete();
                break;
            }
            case 'onTheWay'://pending
            {
                $invoice->update([
                    'status' => "underPrepare"
                ]);
                $invoice->invoiceStatuses()->where('status','onTheWay')->delete();
                break;
            }
            case 'underPrepare'://pending
            {
                $invoice->update([
                    'status' => "pending"
                ]);
                $invoice->invoiceStatuses()->where('status','underPrepare')->delete();
                break;
            }

            default:
                break;
        }
        return sendResponse(InvoiceResource::make($invoice),'successful',1);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function canceled($id){

        if(!$invoice = Invoice::find($id)){
            return sendResponse([],'no data found',0);
        }
        $invoice->update([
            'is_canceled' => 1 //$invoice->is_canceled ==0 ? 1:0,
        ]);

        return sendResponse(InvoiceResource::make($invoice),'success',1);

    }
}
