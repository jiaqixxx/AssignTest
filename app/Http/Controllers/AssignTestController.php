<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Order;
use App\User;
use App\Orders;
use App\Assignment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Validator;

class AssignTestController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAgents()
    {
        $agents = User::select('id', 'name')->get();
        return $agents;
    }

    public function getNotAssignedOrders()
    {
        $today = Carbon::now()->startOfDay()->format('Y-m-d H:i:s');
        $assignedOrderIds = Assignment::select('order_id')
            ->where('created_at', '>', $today)
            ->get();

        $notAssignedOrders = Order::select('id')
            ->where('created_at', '>', $today)
            ->whereNotIn('id', $assignedOrderIds)
            ->get();
        return $notAssignedOrders;
    }


    public function assignTests(Request $request)
    {
        $numAssignments = $request->input('numAssignments');
        $notAssigned = $this->getNotAssignedOrders($numAssignments);

        $rules = [
            'agentId' => 'required|integer',
            'numAssignments' => 'integer|required'
        ];


        $validator = Validator::make($request->all(), $rules);
        $agentId = $request->input('agentId');
        $total = $notAssigned->count();
        $validator->after(function ($validator) use ($numAssignments, $total) {
            if ($total < $numAssignments) {
                $validator->errors()->add('message', 'The number of tests you can assign today is: ' . $total)
                    ->add('result', 'Failed');
            }
        });
        if ($validator->fails()) {
            return $validator->messages();
        } else {
            DB::beginTransaction();
            try {
                $notAssigned = $notAssigned->random($numAssignments)->toArray();
                foreach ($notAssigned as $index => $orders) {
                    $result = new Assignment([
                        'order_id' => $orders['id'],
                        'assignee_id' => $agentId,
                        'assigned_by' => '2'
                    ]);
                    $result->save();
                }
                DB::commit();
                return json_encode(['result' => 'success']);
            } catch (\Exception $e) {
                Log::error($e);
                DB::rollBack();
                return json_encode(['result' => 'Failed', 'message' => 'Failed to assign tests']);

            }
        }
    }

    public function getWorkload()
    {
        $workloads = Assignment::leftJoin('users', 'assignments.assignee_id', '=', 'users.id')
            ->selectRaw(
                'count(*) as numAssignments, users.name'
            )
            ->groupBy('users.id')->get();

        return $workloads;
    }

    public function getInProgressAssignments()
    {
        $inProgressAssignments = Assignment::with(['assignee:id,name', 'order'])
            ->where('is_approved', '=', 0)->get()->toArray();
        foreach ($inProgressAssignments as $index => $assignment) {
            $inProgressAssignments[$index]['environment'] = $assignment['order']['environment'];
            $inProgressAssignments[$index]['product_look_up'] = $assignment['order']['product_look_up'];
            $inProgressAssignments[$index]['order_items'] = $assignment['order']['order_items'];
            $inProgressAssignments[$index]['customer_details'] = $assignment['order']['customer_details'];
        }
        return $inProgressAssignments;
    }

    public function getComments($assignmentId)
    {
        $comments = Comment::select('comment', 'uploaded_file')
            ->where('assignment_id', '=', $assignmentId)
            ->get();
        return $comments;
    }

    public function approveAssignment($assignmentId)
    {
        $result = Assignment::where('id', '=', $assignmentId)
            ->update(['is_approved' => 1]);
        if ($result) {
            return $result;
        } else {
            return json_encode(['result' => 'Failed', 'message' => 'Failed to approve assignment']);
        }
    }

    public function getApprovedAssignments()
    {
        $approvedAssignments = Assignment::with(['assignee:id,name', 'order'])
            ->where('is_approved', '=', 1)->get()->toArray();
        foreach ($approvedAssignments as $index => $assignment) {
            $approvedAssignments[$index]['environment'] = $assignment['order']['environment'];
            $approvedAssignments[$index]['product_look_up'] = $assignment['order']['product_look_up'];
            $approvedAssignments[$index]['order_items'] = $assignment['order']['order_items'];
            $approvedAssignments[$index]['customer_details'] = $assignment['order']['customer_details'];
        }
        return $approvedAssignments;
    }

    public function unSetAssignment($assignmentId)
    {
        $result = Assignment::where('id', '=', $assignmentId)
            ->update(['is_approved' => 0]);
        if ($result) {
            return $result;
        } else {
            return json_encode(['result' => 'Failed', 'message' => 'Failed to approve assignment']);
        }
    }

    public function search(Request $request)
    {
        $rules = [
            'id' => 'string|nullable',
            'order_num' => 'string|nullable',
            'customer_details' => 'string|nullable',
            'bugs' => 'integer|nullable|in:0,1'
        ];

        $validator = Validator::make($request->all(),$rules);

        if($validator->fails()){
            return false;
        }
        $result = Assignment::with(['assignee:id,name', 'order']);
        if ($request->id) {
            $result->where('id', '=', $request->id);
        }
        if ($request->order_num) {
            $result->where('order_id', '=', $request->order_num);
        }
        if ($request->customer_details) {
            $result->where('orders.customer_details', 'like', '%' . $request->customer_details . '%');
        }
        if ($request->bugs) {
            if ($request->bugs == 'Yes') {
                $result->where('has_comments', '=', 1);
            } else if ($request->bugs == 'No') {
                $result->where('has_comments', '=', 0);
            }
        }
        $result = $result->get();
        foreach ($result as $index => $assignment) {
            $result[$index]['environment'] = $assignment['order']['environment'];
            $result[$index]['product_look_up'] = $assignment['order']['product_look_up'];
            $result[$index]['order_items'] = $assignment['order']['order_items'];
            $result[$index]['customer_details'] = $assignment['order']['customer_details'];
        }
        return $result;
    }
}
