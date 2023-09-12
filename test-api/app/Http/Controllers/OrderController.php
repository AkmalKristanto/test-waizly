<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Requests\{OrderRequest};
use App\Http\Transformers\Result;
use App\Http\Helpers\{LogUser};
use DB;
use Storage;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Resources\{ListOrderResource};
use App\Models\{Order, OrderProduct};

class OrderController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }
    
    private function _handleUpload($get_image) 
    {
        $time = strtotime(date(now())) * 1000;
        $image_ = explode(',', $get_image);
        $image = $image_[1];

        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $file_base64 = base64_decode($image);
        $new_imgname = $time . '.png';
        
        $filePath = '/img-order/' . $new_imgname;
        Storage::disk('storage')->put($filePath, $file_base64);

        return $filePath;
    }

    public function list_order(Request $request)
    {
        try {
            $user = auth()->user();
            $id_user = $user->id_user;
            $category_time = $request->get('category_time');
            $search = $request->get('search');
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $page = $request->get('page') ? $request->get('page') : 1;
            $per_page = $request->get('per_page') ? $request->get('per_page') : 20;
            
            $get_pesanan = Order::where('status_active', 1)
                                ->selectRaw("id_order, no_transaction, name_order, type_order , total_amount, payment_method, created_at, payment_status")
                                ->orderBy('created_at', 'DESC');

            if (!empty($search)) {
                $get_pesanan->where(function ($q) use ($search) {
                    return $q->where('order.name_order', 'like', '%' . $search . '%')
                                ->orwhere('order.no_transaction', 'like', '%' . $search . '%')
                                ->orwhere('order.total_amount', 'like', '%' . $search . '%');
                });
            }
            if (!empty($category_time)) {
                if($category_time == '1'){
                    $start_date = date("Y-m-d 00:00:00");
                    $end_date = date("Y-m-d 23:59:59");
                }
                if($category_time == '2'){
                    $start_date = date("Y-m-d", strtotime("-1 days")). ' 00:00:00';
                    $end_date = date("Y-m-d", strtotime("-1 days")). ' 23:59:59';
                }
                if($category_time == '3'){
                    $start_date = date("Y-m-d", strtotime("this week")). ' 00:00:00';
                    $end_date = date("Y-m-d 23:59:59");
                }
                if($category_time == '4'){
                    $start_date = date("Y-m-d", strtotime("monday last week")). ' 00:00:00';
                    $end_date = date("Y-m-d", strtotime("monday last week + 6 days")). ' 23:59:59';
                }
                if($category_time == '5'){
                    $start_date = date("Y-m-d", strtotime("first day of this month")). ' 00:00:00';
                    $end_date = date("Y-m-d 23:59:59");
                }
                if($category_time == '6'){
                    $start_date = date("Y-m-d", strtotime("first day of previous month")). ' 00:00:00';
                    $end_date = date("Y-m-d", strtotime("last day of previous month")). ' 23:59:59';
                }
                $get_pesanan->where(function ($q) use ($start_date, $end_date) {
                    return $q->where('created_at', '>=', $start_date)
                            ->where('created_at', '<=', $end_date);
                });
            }
            $get_pesanan = $get_pesanan->when($start_date, function ($q, $start_date) {
                                            $q->whereDate('created_at', '>=', $start_date);
                                        })
                                        ->when($end_date, function ($q, $end_date) {
                                            $q->whereDate('created_at', '<=', $end_date);
                                        })
                                        ->paginate($per_page)->withQueryString();

            $feature = "Get List Order";
            $log = LogUser::log_user_update($id_user, $feature);
            
            return ListOrderResource::collection($get_pesanan);
        } catch (\Throwable $th) {
            return Result::error($th, 'Terjadi kesalahan saat memuat data');
        }
    }

    public function detail_order(Request $request)
    {
        try {
            $user = auth()->user();
            $id_order = $request->get('id_order');
            $id_user = $user->id_user;

            $get_pesanan = Order::where('order.id_order', $id_order)
                                ->selectRaw("order.id_order, no_transaction, name_order, type_order, payment_method, order.amount, tax, service, order.total_amount ,order.created_at")
                                ->with([
                                    'item'
                                ])
                                ->orderBy('created_at', 'DESC')
                                ->first();
            if ($get_pesanan->type_order === 1) {
                $get_pesanan['type_order'] = 'Dine In';
            } else {
                $get_pesanan['type_order'] = 'Take Away';
            }
            if ($get_pesanan->payment_method === 1) {
                $get_pesanan['payment_method'] = 'Cash';
            } else {
                $get_pesanan['payment_method'] = 'Other';
            }
            
            $feature = "Detail Order - ".$id_order;
            $log = LogUser::log_user_update($id_user, $feature);

            return Result::response($get_pesanan, 'Data Berhasil Didapatkan.');
        } catch (\Throwable $th) {
            return Result::error($th, 'Terjadi kesalahan saat memuat data');
        }
    }

    public function create_order(OrderRequest $request)
    {
        try {
            $user = auth()->user();
            $id_user = $user->id_user;

            $tahun = date("y");
            $bulan = date("m");
            $tanggal = date("d");
            
            $cek_np = 'Order-'.$tahun.''.$bulan.'-';
            $no_transaction = 'Order-'.$tahun.''.$bulan.'-001';

            $cek = Order::where('no_transaction', 'like', '%' . $cek_np . '%')
                                ->orderBy('created_at', 'DESC')
                                ->orderBy('no_transaction', 'DESC')
                                ->first();
            if($cek == null){
                $no_transaction = 'Order-'.$tahun.''.$bulan.'-001';
            }else{
                $nmr = explode('-', $cek->no_transaction);
                $nmr_next = $nmr[3] + 1;
                $kode_next = sprintf("%03d", $nmr_next);
                $no_transaction = 'Order-'.$tahun.''.$bulan.'-'.$kode_next;
            }
            
            if($request->payment_method == 1){
                $req["id_user"] = $id_user;
                $req["no_transaction"] = $no_transaction;
                $req["name_order"] = $request->name_order;
                $req["amount"] = $request->amount;
                $req["tax"] = $request->tax;
                $req["service"] = $request->service;
                $req["total_amount"] = $request->total_amount;
                $req["payment_method"] = $request->payment_method;
                $req["payment_status"] = '1';
                $req["type_order"] = $request->type_order;
                $req["status_active"] = 1;
                DB::begintransaction();
                try {
                    $create_order = Order::create($req);
                    $id_order = $create_order->id_order;
                    $array_product = json_decode($request->array_product);
                    foreach($array_product as $val){
                        $arr = [
                            'id_order' => $id_order,
                            'id_product' => $val->id_product,
                            'id_product_add_on' => $val->id_product_add_on,
                            'qty' => $val->qty,
                            'notes' => $val->notes,
                            'amount' => $val->amount,
                            'total_amount' => $val->total_amount,
                        ];
                        $create_order_product = OrderProduct::create($arr);
                    }
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollback();
                    return Result::response(array(), $e->getMessage(), 400, false);
                }

                $feature = "Create Order - ".$id_order;
                $log = LogUser::log_user_update($id_user, $feature);

                return Result::response($create_order, 'Data Berhasil Disimpan.');
            
            }else{
                return Result::error(array(), 'Payment Method Tidak Tersedia.');
            }
        } catch (\Throwable $th) {
            return Result::error($th, 'Terjadi kesalahan saat memuat data');
        }
    }

}