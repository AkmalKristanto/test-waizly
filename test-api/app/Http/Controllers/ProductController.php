<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Requests\{ProductRequest};
use App\Http\Helpers\{LogUser};
use App\Http\Transformers\Result;
use Illuminate\Support\Facades\DB;
use Storage;
use App\Models\{Product, ProductAddOn};

class ProductController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:api', ['except' => ['login']]);
    // }

    private function _handleUpload($get_image) {
        $time = strtotime(date(now())) * 1000;
        $image_ = explode(',', $get_image);
        $image = $image_[1];

        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $file_base64 = base64_decode($image);
        $new_imgname = $time . '.png';
        
        $filePath = '/img-product/' . $new_imgname;
        Storage::disk('storage')->put($filePath, $file_base64);

        return $filePath;
    }

    public function list_product(Request $request)
    {
        try {
            $user = auth()->user();
            $id_user = $user->id_user;
            
            $search = $request->get('search');
            $status = $request->get('status') ? $request->get('status') : 1;
            if($request->get('status') == '0'){
                $status = 0;
            }
            $page = $request->get('page') ? $request->get('page') : 1;
            $per_page = $request->get('per_page') ? $request->get('per_page') : 20;

            $get_product = Product::where('product.status_active', $status)
                                ->selectRaw("product.id_product, id_category, name_product, price, CONCAT('" . env('MEDIA_BASEURL') . "', url_logo) as url_logo")
                                ->orderBy('product.created_at', 'DESC');

            if (!empty($search)) {
                $get_product->where(function ($q) use ($search) {
                    return $q->where('product.product', 'like', '%' . $search . '%');
                });
            }

            $get_product = $get_product->paginate($per_page)
                                    ->withQueryString();
            $feature = "Get List Product";
            $log = LogUser::log_user_update($id_user, $feature);

            return Result::response($get_product, 'Data Berhasil Didapatkan.');
        } catch (\Throwable $th) {
            return Result::error($th, 'Terjadi kesalahan saat memuat data');
        }
    }

    public function detail_product(Request $request)
    {
        try {
            $user = auth()->user();
            $id_user = $user->id_user;
            $id_product = $request->get('id_product');

            $detail_product = Product::where('id_product', $id_product)
                                ->selectRaw("product.id_product, name_product, price, CONCAT('" . env('MEDIA_BASEURL') . "', url_logo) as url_logo")
                                ->with([
                                    'item' => function ($q) {
                                    $q->where('status_active', 1)
                                    ->selectRaw("product_add_on.id_product_add_on, product_add_on.id_product, name_product_add_on as name, CONCAT('" . env('MEDIA_BASEURL') . "', url_logo) as url_logo");
                                    }
                                ])
                                ->first();

            $feature = "Detail Product - ".$id_product;
            $log = LogUser::log_user_update($id_user, $feature);
                    
            return Result::response($detail_product, 'Data Berhasil Didapatkan.');
        } catch (\Throwable $th) {
            return Result::error($th, 'Terjadi kesalahan saat memuat data');
        }
    }

    public function create_product(ProductRequest $request)
    {
        try {
            $user = auth()->user();
            $id_user = $user->id_user;

            DB::begintransaction();
            try {
                $url_logo = $this->_handleUpload($request->url_logo);
                $req = [
                    "id_user" => $id_user,
                    "id_category" => $request->id_category,
                    "name_product" => $request->name_product,
                    "price" => $request->price,
                    "url_logo" => $url_logo,
                ];
                $array_add_on = $request->add_on;

                $create_product = Product::create($req);
                if (empty($create_product)) {
                throw new \Exception('Gagal Create Product');
                }

                $id_product = $create_product->id_product;
                if(!empty($array_add_on)){
                    $add_on = json_decode($array_add_on);
                    foreach($add_on as $val){
                        if($val->url_logo){
                            $logo_add_on = $this->_handleUpload($val->url_logo);
                        }else{
                            $logo_add_on = null;
                        }
                        $save_add_on = [
                            'id_product' => $id_product,
                            'name_product_add_on' => $val->name,
                            'url_logo' => $logo_add_on,
                        ];
                        
                        $create_add_on = ProductAddOn::create($save_add_on);
                        if (empty($create_add_on)) {
                        throw new \Exception('Gagal Create Product Add On');
                        }
                    }
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return Result::response(array(), $e->getMessage(), 400, false);
            }

            $feature = "Create Product - ".$id_product;
            $log = LogUser::log_user_update($id_user, $feature);

            return Result::response(array(), 'Data Berhasil Disimpan.');
        } catch (\Throwable $th) {
            return Result::error($th, 'Terjadi kesalahan saat memuat data');
        }
    }

    public function update_product(Request $request)
    {
        try {
            $user = auth()->user();
            $id_user = $user->id_user;
            $id_product = $request->id_product;
            $array_add_on = $request->add_on;
            DB::begintransaction();
            try {
                
                $product = Product::findOrFail($id_product);
                $product->id_category = $request->id_category;
                $product->name_product = $request->name_product;
                $product->price = $request->price;

                if (!empty($request->url_logo)) {
                    $url_logo = $this->_handleUpload($request->url_logo);
                    $product->url_logo = $url_logo;
                }

                $product->save();

                if (empty($product)) {
                throw new \Exception('Gagal Update Product');
                }
                if($request->add_on != null){
                    $add_on = json_decode($array_add_on);
                    $nama_add_on = [];
                    foreach($add_on as $val){
                        $cek = ProductAddOn::where('id_product', $id_product)
                                        ->where('name_product_add_on', $val->name)
                                        ->first();
                        if($cek == null){
                            if($val->url_logo){
                                $logo_add_on = $this->_handleUpload($val->url_logo);
                            }else{
                                $logo_add_on = null;
                            }
                            $save_add_on = [
                                'id_product' => $id_product,
                                'name_product_add_on' => $val->name,
                                'url_logo' => $logo_add_on,
                            ];

                            $create_add_on = ProductAddOn::create($save_add_on);

                            if (empty($create_add_on)) {
                                throw new \Exception('Gagal Create Product Add On');
                            }
                            array_push($nama_add_on, $val->name);
                        }else{
                            if($cek->status_active == '0'){
                                $active_product_add_on = ProductAddOn::where('id_product_add_on', $cek->id_product_add_on)
                                                            ->where('id_product', $id_product)
                                                            ->update(
                                                                ['status_active' => '1']
                                                            );
                            }
                        }
                    }
                    $disable_product_add_on = ProductAddOn::whereNotIn('name_product_add_on', $nama_add_on)
                                                        ->where('id_product', $id_product)
                                                        ->update(
                                                            ['status_active' => '0']
                                                        );
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return Result::response(array(), $e->getMessage(), 400, false);
            }

            $feature = "Update Product - ".$id_product;
            $log = LogUser::log_user_update($id_user, $feature);

            return Result::response(array(), 'Data Berhasil Diupdate.');
        } catch (\Throwable $th) {
            return Result::error($th, 'Terjadi kesalahan saat memuat data');
        }
    }

    public function delete_product(Request $request)
    {
        try {
            $user = auth()->user();
            $id_user = $user->id_user;
            $id_product = $request->get('id_product');
            $get_product = Product::where('id_product', $id_product)
                                ->update(['status_active' => '0']);

            return Result::response(array(), 'Data Berhasil Dihapus.');
        } catch (\Throwable $th) {
            return Result::error($th, 'Terjadi kesalahan saat memuat data');
        }
    }

}