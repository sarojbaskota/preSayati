<?php

namespace App\Http\Controllers\Api;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Api\BaseApiController;

class MessageController extends BaseApiController
{

    private $request;
    private $message;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, Message $message)
    {
        $this->request = $request;
        $this->message = $message;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data  = Auth::user()->messages;
            return $this->sendSuccess($data, 'Messages retrive successfully.');
        } catch (ValidationException $exception) {
            // Handle validation errors
            return $this->sendValidationFail($exception);
        } catch (\Exception $exception) {
            // Handle other exceptions
            return $this->sendError('Failed to retrive messages.'.$exception->getMessage(), 500);
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
    public function store()
    {
        // Start the database transaction
        DB::beginTransaction();
        
        try {
            $validatedData = $this->request->validate([
                'content' => 'required',
                'to' => 'required',
            ]);
            $composeMessage = [
                'content' => $this->request->content,
                'from' => Auth::user()->id,
                'to' => $this->request->to
            ];
            // compose mesage 
            $message = $this->message->create($composeMessage);

            //attached id to meesage and user own track
            $message->users()->attach(Auth::user()->id);
            // finish here 
            DB::commit();
            return $this->sendCreateSuccess($message, 'Message sent successfully.');

        } catch (ValidationException $exception) {
            // If any exception occurs, roll back the transaction
            DB::rollBack();
            // Handle validation errors
            return $this->sendValidationFail($exception);
        } catch (\Exception $exception) {
             // If any exception occurs, roll back the transaction
             DB::rollBack();
            // Handle other exceptions
            return $this->sendError('Failed to sent  Message.'.$exception->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $data  = $this->message->where('from',Auth::user()->id)->where('to',$id)->get();
            return $this->sendSuccess($data, 'Messages retrive successfully.');
        } catch (ValidationException $exception) {
            // Handle validation errors
            return $this->sendValidationFail($exception);
        } catch (\Exception $exception) {
            // Handle other exceptions
            return $this->sendError('Failed to retrive messages.'.$exception->getMessage(), 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
       
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
