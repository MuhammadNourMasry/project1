<?php
namespace App\Http\Controllers;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
class UserController extends Controller
{
    public function register(StoreUserRequest $request)
    {
     $data = $request->validated();
     $data['password'] = Hash::make($data['password']);
     if($request->hasFile('personal_photo')&&$request->hasFile('photo_of_personal_ID'))
     {
        $path1=$request->file('personal_photo')->store('my_photo_personal','public');
        $path2=$request->file('photo_of_personal_ID')->store('my_photo_of_personal_ID','public');
        $data['personal_photo']=$path1;
        $data['photo_of_personal_ID']=$path2;
    }
     $user = User::create($data);
    return response()->json([
        'message'=>'User Registered Successfully',
        'User'=>$user
    ],201);
    }
    public function login(Request $request)
    {
      $request->validate(
        [
        'phone'=>'required|digits:10',
        'password'=>'required|string|min:8'
        ]);
      if(!Auth::attempt($request->only('phone','password')))
        return response()->json([
      'message'=>'invalid phone or password'],401);
     $user=User::where('phone',$request->phone)->FirstOrFail();
     $token=$user->createToken('auth_token')->plainTextToken;
     return response()->json([
        'message'=>'login Successfully',
        'User'=>$user,
        'token'=>$token
     ]);
    }
    public function logout(Request $request)
    {
      $request->user()->currentAccessToken()->delete();
      return response()->json(
        ['message'=>'logout sucessfully']
      );
    }

    
    //TODO للاستخدام في واجهة الادمن
    /*
    public function getaAllUser()
    {
        $user=User::where('role','!=','admin')->get();
        return response()->json($user,200);
    }
   public function deleteUser(Request $request)
{
    $request->validate([
        'id' => 'required|exists:users,id'  
    ]);
    
    $deleted = User::where('id', $request->id)->delete();  
    if ($deleted) {
        return response()->json(['message' => 'User deleted successfully']);
    } else {
        return response()->json(['message' => 'User not found'], 404);
    }
}*/
}
