<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Transformers\Result;
use App\Http\Helpers\{LogUser};
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use DB;
use Storage;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Requests\RegisterRequest;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            $email = $request->email;
            $password = $request->password;

            $user = User::where('email', $email)
                            ->first();
            if($user == null){
                return Result::error(array(), 'Akun Tidak Ditemukan');
            }
            $id_user = $user->id_user;
            
            $userdata['email'] = $user->email;
            $userdata['password'] = $password;
            try {
                if (! $token = JWTAuth::attempt($userdata)) {
                    return Result::error(array(), 'Username dan password tidak cocok.');
                }
            } catch (JWTException $e) {
                return Result::exception(array(), 'Generate Token Error');
            }
            if ($user->first_login == null) {
                $update_data['first_login'] = date('Y-m-d H:i:s');
             }

            $update_data = [
                'last_login' => date('Y-m-d H:i:s'),
                'token_user' => $token,
            ];

            $update = User::where('email', $email)->update($update_data);

            if(!$update) {
                $result = $this->resultTransformer(false, 'Login Gagal Silakan Coba Lagi');
                return response()->json($result);
            }

            $ret = [
                'email' => $user->email,
                'token_user' => $token
            ];
            
            $feature = "Login";
            $log = LogUser::log_user_update($id_user, $feature);

            return Result::response($ret, 'Login Berhasil Dilakukan');
        } catch (\Throwable $th) {
            return Result::error($th, 'Terjadi kesalahan saat memuat data');
        }
    }

    public function register(RegisterRequest $request)
    {
        try {
            $check_email = User::Where('email', $request->email)->first();
            if($check_email != null){
                return Result::error(array(), 'Email Sudah Terdaftar');
            }
            $check_phone = User::Where('phone', $request->phone)->first();
            if($check_phone != null){
                return Result::error(array(), 'Nomor Telepon Sudah Terdaftar');
            }
            DB::begintransaction();
            try {
                $data_user = [
                    'username' => $request->username,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'password' => Hash::make($request->password),
                ];
                $user = User::create($data_user);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return Result::response(array(), $e->getMessage(), 400, false);
            }

            $id_user = $user->id_user;
            $feature = "Register";
            $log = LogUser::log_user_update($id_user, $feature);

            return Result::response($user, 'Register Berhasil Dilakukan');
        } catch (\Throwable $th) {
            return Result::error($th, 'Terjadi kesalahan saat memuat data');
        }
    }

    public function me(Request $request)
    {
        try {
        	$user = auth()->user();
            $id_user = $user->id_user;
            $ret['id_user'] = $user->id_user;
            $ret['username'] = $user->username;
            $ret['email'] = $user->email;
            $ret['phone'] = $user->phone;
            $ret['url_img'] = env('MEDIA_BASEURL').$user->url_img;

            return Result::response($ret, 'Data Berhasil DIdapatkan');
        } catch(\JWTException $e) {
            return Result::exception(false, 'Server sedang sibuk, coba lagi.', 500, config('app.debug')==true?$e:'');
        }
    }

    public function logout()
    {
        try {
            $user = auth()->user();
            $id_user = $user->id_user;
            $update = [
                'token_user' => 0,
            ];
            $save = User::where('id_user', $id_user)->update($update);

            $feature = "Logout";
            $log = LogUser::log_user_update($id_user, $feature);

            JWTAuth::invalidate(JWTAuth::getToken());
            return Result::response(array(), 'Logout Berhasil');
        } catch(\JWTException $e) {
            return Result::exception(false, 'Server sedang sibuk, coba lagi.', 500, config('app.debug')==true?$e:'');
        }
    }

    public function refresh()
    {
        try {
            $data['token'] = auth()->refresh();
            return Result::response($data, 'Refresh Token');
        }catch(\JWTException $e) {
            return Result::exception(false, 'Server sedang sibuk, coba lagi.', 500, config('app.debug')==true?$e:'');
        }
    }

    public function upload_image_profile(Request $request)
    {
        $user = auth()->user();
        $id_user = $user->id_user;
        
        $get_image = $request->url_img;
        
        if($get_image == null){
            $code = 400;
            $result['status'] = false;
            $result['message'] = 'Data Tidak Boleh Kosong.';
            $result['data'] = array();

            return response()->json($result, $code);
        }
        $time = strtotime(date(now())) * 1000;
        $image_ = explode(',', $get_image);
        $image = $image_[1];

        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $file_base64 = base64_decode($image);
        $new_imgname = $time . '.png';
        
        $filePath = '/img-profile/' . $new_imgname;
        Storage::disk('storage')->put($filePath, $file_base64);
        $req["url_img"] = $filePath;

        $update_img = User::where('id_user', $id_user)
                            ->update($req);

        $feature = "Upload Image";
        $log = LogUser::log_user_update($id_user, $feature);

        return Result::response(array(), 'Data Berhasil Disimpan.');
    }
}