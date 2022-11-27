<?php

namespace App\Http\Controllers;

use App\Task;
use Illuminate\Http\Request;
use Validator;
use Datatables;

class TaskController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(){
        $tasks = Task::all();
        if($tasks)
        {
            return view('tasks.tasks')->with('tasks', $tasks);
        }
        else
        {
            return response()->json(['message'=>'No Product found'], 404);
        }
        
    }




    

    public function show(Request $request)
    {
        $id = $request->input('id');
        $task = Task::find($id);
        $output = array(
            'Status'    =>  $task->Status,
        );
        echo json_encode($output);
    }

    


   public function fetchdata(Request $request)
        {
            $id = $request->input('id');
            $task = Task::find($id);
            $output = array(
                'status'     =>  $task->status
            );
            echo json_encode($output);
        }





    public function create()
    {
    $tasks = Task::where('user_id', auth()->user()->id)->get();
    return Datatables::of($tasks)
            ->addColumn('action', function($task){
                if($task->status == "pending"){
                    return '<a href="#" class="btn btn-xs btn-primary edit" id="'.$task->id.'"><i class="glyphicon glyphicon-edit"></i> Check</a>';
                }else{
                    return '<a style="background-color:green; border:green;" href="#" class="btn btn-xs btn-primary edit" id="'.$task->id.'"> Done</a>';
                }
            })
            ->make(true);
    }










    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'user_id'  => '',
            'task' => '',
        ]);

        $error_array = array();
        $success_output = '';
        if ($validation->fails())
        {
            foreach($validation->messages()->getMessages() as $field_name => $messages)
            {
                $error_array[] = $messages;
            }
        }
        else
        {
            if($request->get('button_action') == "insert")
            {
                $task = new Task;
                $task->user_id = auth()->user()->id;
                $task->task = $request->input('task');
                $task->save();
                $success_output = '<div class="alert alert-success">Data Inserted</div>';
            }


            if($request->get('button_action') == 'update')
            {
                $task = Task::find($request->get('task_id'));
                $task->status = $request->get('status');
                $task->save();
                $success_output = '<div class="alert alert-success">Data Updated</div>';
            }


        }
        $output = array(
            'error'     =>  $error_array,
            'success'   =>  $success_output
        );
        echo json_encode($output);
    }


    
}
