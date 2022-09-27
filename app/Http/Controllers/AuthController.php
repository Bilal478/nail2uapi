<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Validator;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){
    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json([
            'error'=> true,
            'message' => $validator->errors(),
            'data' => []
            ], 422);
        }
        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json([
             'error' => true,
             'message'=>'Unauthorized',
             'data' => []
            ], 401);
        }
        return $this->createNewToken($token);
    }
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        $data = [];
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'phone' => 'required|unique:users',
            'address' => 'required|string',
            'password' => 'required|string|confirmed|min:6',
            'type' => 'string',
        ]);
        if($validator->fails()){
            return response()->json([
                'error' => true,
                'message' => $validator->errors()->toJson(),
                'data' => $data
            ], 400);
        }
        $user = User::create(array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password)]
                ));
        $data['user'] = $user;
        return response()->json([
            'error' => false,
            'message' => 'User successfully registered',
            'data' => $data
        ], 201);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth()->logout();
        return response()->json([
            'error' => false,
            'message' => 'User successfully signed out',
            'data' => []
            ]);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile() {
        return response()->json([
            'error' => false,
            'message' => 'User successfully updated',
            'data' => auth()->user()
        ], 201);
    }
     /**
     * update the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profileUpdate(Request $request) {
        $data = [];
        $user = User::where('id',auth()->user()->id)->first();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;
        if($request->password){
            $user->password = bcrypt($request->password);
        }
        if($user->type == 'artist'){
            $user->about_artist = $request->about_artist;
        }
        $user->save();
        $data['user'] = $user;
        return response()->json([
                'error' => false,
                'message' => 'User successfully updated',
                'data' => $data
          
        ], 201);
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        $data = [];
        $data['access_token'] = $token;
        $data['token_type'] = 'bearer';
        $data['expires_in'] = auth()->factory()->getTTL() * 10080;
        $data['user'] = auth()->user();
        return response()->json([
                'error' => false,
                'message' => '',
                'data' => $data
        ]);
    }
}