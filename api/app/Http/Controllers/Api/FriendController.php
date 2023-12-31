<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Api\BaseApiController;
use Carbon\Carbon;

class FriendController extends BaseApiController
{
    private $request;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data  = Auth::user()->friends;
            return $this->sendSuccess($data, 'Friends retrive successfully.');
        } catch (ValidationException $exception) {
            // Handle validation errors
            return $this->sendValidationFail($exception);
        } catch (\Exception $exception) {
            // Handle other exceptions
            return $this->sendError('Failed to retrive messages.'.$exception->getMessage(), 500);
        }
    }
     /**
     * Display a listing of the resource.
     */
    public function request()
    {
        try {
            $data  = Auth::user()->friendRequests;
            return $this->sendSuccess($data, 'Friends requests retrive successfully.');
        } catch (ValidationException $exception) {
            // Handle validation errors
            return $this->sendValidationFail($exception);
        } catch (\Exception $exception) {
            // Handle other exceptions
            return $this->sendError('Failed to retrive request messages.'.$exception->getMessage(), 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function add()
    {
        try {
           $this->request->validate([
                'add_to' => 'required',
            ]);
           DB::table('friends')->insert([
                'user_id' => Auth::user()->id,
                'friend_id' => $this->request->add_to,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            return $this->sendCreateSuccess('', 'Friend request sent successfully.');

        } catch (ValidationException $exception) {
            // Handle validation errors
            return $this->sendValidationFail($exception);
        } catch (\Exception $exception) {
            // Handle other exceptions
            return $this->sendError('Failed to sent  request.'.$exception->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    

    /**
     * Update the specified resource in storage.
     */
    public function confirm()
    {
        try {
           $this->request->validate([
                'confirm_for' => 'required',
            ]);
            DB::table('friends')->where('user_id', Auth::user()->id)
            ->where('friend_id', $this->request->confirm_for)
            ->update([
                'confirm' => 1,
            ]);
            return $this->sendCreateSuccess('', 'Friend request accepted successfully.');

        } catch (ValidationException $exception) {
            // Handle validation errors
            return $this->sendValidationFail($exception);
        } catch (\Exception $exception) {
            // Handle other exceptions
            return $this->sendError('Failed to accept  request.'.$exception->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function unfriend()
    {
        try {
           $this->request->validate([
                'unfriend_to' => 'required',
            ]);
            DB::table('friends')->where('user_id', Auth::user()->id)
            ->where('friend_id', $this->request->unfriend_to)
            ->delete();
            return $this->sendCreateSuccess('', 'Unfriend  successfully.');

        } catch (ValidationException $exception) {
            // Handle validation errors
            return $this->sendValidationFail($exception);
        } catch (\Exception $exception) {
            // Handle other exceptions
            return $this->sendError('Failed to unfriend.'.$exception->getMessage(), 500);
        }
    }
}
