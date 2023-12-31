<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Api\BaseApiController;

class AuthenticationController extends BaseApiController
{
    private $user;
    private $request;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(User $user, Request $request)
    {
        // $this->middleware('auth:api')->except(['index', 'show']);
        
        // Or inject services as needed
        $this->user = $user;
        $this->request = $request;
    }
    /**
    * Handle an incoming authentication request.
    */
    public function signin()
    {
        try {
            $validatedData = $this->request->validate([
                'username' => 'required',
                'password' => 'required',
            ]);
            if (Auth::attempt(['email' => $this->request->username, 'password' => $this->request->password])) {
                // successfull authentication
                $user = Auth::user();    
                $tokenResult = $user->createToken('chat'); // This returns a PersonalAccessTokenResult object
                $accessToken = $tokenResult->accessToken;  // Extract the access token string
                $data = [
                    'token' => $accessToken,
                    'user'  => $user
                ];
                return $this->sendSuccess($data, 'User login successfully.');
            }else{
                return $this->sendError('Failed to login user.', 500);
            }


        } catch (ValidationException $exception) {
            // Handle validation errors
            return $this->sendValidationFail($exception);
        } catch (\Exception $exception) {
            // Handle other exceptions
            return $this->sendError('Failed to login user.', 500);
        }
    }
    /**
    * Handle an incoming authentication request.
    */
    public function signup()
    {
        try {
            $validatedData = $this->request->validate([
                'fname' => 'required|string|max:255',
                'lname' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
            ]);
            $payload = [
                'fname' => $this->request->fname,
                'lname' => $this->request->lname,
                'username' => $this->request->lname,
                'email' => $this->request->email,
                'password' => $this->request->password
            ];
            $data = $this->user->create($payload);
            return $this->sendCreateSuccess($data, 'User registered successfully.');

        } catch (ValidationException $exception) {
            // Handle validation errors
            return $this->sendValidationFail($exception);
        } catch (\Exception $exception) {
            // Handle other exceptions
            return $this->sendError('Failed to register user.'.$exception->getMessage(), 500);
        }
    }
     /**
    * Handle an incoming authentication request.
    */
    public function get()
    {
        try {
            if (Auth::check()){
                $data  = Auth::user();
                return $this->sendSuccess($data, 'Profile retrive successfully.');
            }
            return $this->sendSuccess(null,'Profile data not found.');

        } catch (ValidationException $exception) {
            // Handle validation errors
            return $this->sendValidationFail($exception);
        } catch (\Exception $exception) {
            // Handle other exceptions
            return $this->sendError('Failed to retrive profile.'.$exception->getMessage(), 500);
        }
    }
     /**
    * Handle an incoming authentication request.
    */
    public function update()
    {
        try {
            $validatedData = $this->request->validate([
                'fname' => 'required|string|max:255',
                'lname' => 'required|string|max:255',
                'username' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,'.Auth::user()->id.',id',
            ]);
            $payload = [
                'fname' => $this->request->fname,
                'lname' => $this->request->lname,
                'username' => $this->request->lname,
                'email' => $this->request->email,
                'password' => $this->request->password
            ];
            $user = Auth::user();
            $user->update($payload);
            
            return $this->sendSuccess($user, 'Profile Updated successfully.');

        } catch (ValidationException $exception) {
            // Handle validation errors
            return $this->sendValidationFail($exception);
        } catch (\Exception $exception) {
            // Handle other exceptions
            return $this->sendError('Failed to retrive profile.'.$exception->getMessage(), 500);
        }
    }
}
