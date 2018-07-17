<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Notifications\LogIn;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Notification;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Mail\Message;

class AuthController extends Controller
{
    const EMAIL = 'email';
    const DESCRIPTION = 'description';
    const TOKEN = 'token';
    const IS_VERIFIED = 'is_verified';

    private $task;
    public function __construct()
    {
        $this->task['title'] = "Auth";
        $this->task[self::DESCRIPTION] = "";
        $this->task['id'] = 1;
        $this->task[self::EMAIL] = 1;

    }

    public function register(RegisterRequest $request)
    {
        $this->task[self::DESCRIPTION] = "New Register";
        $name = $request->get('name');
        $email = $request->get(self::EMAIL);
        $password = $request->get('password');
        $this->task[self::EMAIL] = $request->email;
        $user = User::create(['name' => $name, self::EMAIL => $email, 'password' => Hash::make($password), self::IS_VERIFIED => true]);
//        $verification_code = str_random(30); //Generate verification code
//        \DB::table('user_verifications')->insert(['user_id'=>$user->id,'token'=>$verification_code]);
//        $subject = "Please verify your email address.";
//        \Mail::send('verify', ['name' => $name, 'verification_code' => $verification_code],
//            function($mail) use ($email, $name, $subject){
//                $mail->from('verify@dbchain.com');
//                $mail->to($email, $name);
//                $mail->subject($subject);
//            });
        Notification::send($user, new LogIn($this->task));

        return response()->json(['success'=> true, 'message'=> 'Thanks for signing up! Please check your email to complete your registration.']);
    }

    /**
     * @param $verification_code
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyUser($verification_code)
    {
        $check = \DB::table('user_verifications')->where(self::TOKEN,$verification_code)->first();
        if(!is_null($check)){
            $user = User::find($check->user_id);
            if($user->is_verified == 1){
                return response()->json([
                    'success'=> true,
                    'message'=> 'Account already verified..'
                ]);
            }
            $user->update([self::IS_VERIFIED => 1]);
            \DB::table('user_verifications')->where(self::TOKEN,$verification_code)->delete();
            return response()->json([
                'success'=> true,
                'message'=> 'You have successfully verified your email address.'
            ]);
        }
        return response()->json(['success'=> false, 'error'=> "Verification code is invalid."]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only(self::EMAIL, 'password');

        $rules = [
            self::EMAIL => 'required | email',
            'password' => 'required',
        ];

        $validator = Validator::make($credentials, $rules);

        if($validator->fails()) {
            return response()->json(['success'=> false, 'error'=> $validator->messages()]);
        }

        $credentials[self::IS_VERIFIED] = 1;

        try {
            // attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['success' => false, 'error' => 'We cant find an account with this credentials. Please make sure you entered the right information and you have verified your email address.'], 401);
            }
        } catch (JWTException $e) {
            $this->task[self::DESCRIPTION]  = $e;
            Notification::send(User::first(), new LogIn($this->task));
            // something went wrong whilst attempting to encode the token
            return response()->json(['success' => false, 'error' => 'Failed to login, please try again.'], 500);
        }
        // all good so return the token
        $user = User::where(self::EMAIL, $request->get(self::EMAIL))->get();
        Notification::send($user, new LogIn($this->task));
        return response()->json(['success' => true, 'data'=> [
            self::TOKEN => $token,
            'userName' => $user[0]->name]]);
    }

    public function logout(Request $request) {
        try {
            JWTAuth::invalidate($request->input(self::TOKEN));
            return response()->json(['success' => true, 'message'=> "You have successfully logged out."]);
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['success' => false, 'error' => 'Failed to logout, please try again.'], 500);
        }
    }

    public function recover(Request $request)
    {
        $user = User::where(self::EMAIL, $request->email)->first();
        if (!$user) {
            $error_message = "Your email address was not found.";
            return response()->json(['success' => false, 'error' => [self::EMAIL=> $error_message]], 401);
        }
        try {
            Password::sendResetLink($request->only(self::EMAIL), function (Message $message) {
                $message->subject('Your Password Reset Link');
            });
        } catch (\Exception $e) {
            //Return with error
            $error_message = $e->getMessage();
            return response()->json(['success' => false, 'error' => $error_message], 401);
        }
        return response()->json([
            'success' => true, 'message'=> 'A reset email has been sent! Please check your email.'
        ]);
    }
}
