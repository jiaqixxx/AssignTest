<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;
use App\Order;
use App\Assignment;
use Auth;

class AssignmentController extends Controller
{
    public function getWorkload()
    {
        $workloads = Assignment::leftJoin('users', 'assignments.assignee_id', '=', 'users.id')
            ->selectRaw(
                'count(*) as numAssignments, users.name'
            )
            ->groupBy('users.id')->get();

        return $workloads;
    }

    public static function getNotAssignedOrders()
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

    public function approveOrNotAssignment(Request $request)
    {
        $rules = [
            'assignment_id' => 'string|required',
            'status' => 'string|required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return json_encode(['result' => 'Failed', 'message' => 'missing data, cannot change assignment status']);
        }
        $result = Assignment::where('id', '=', $request->input('assignment_id'))
            ->update(['is_approved' => $request->input('status')]);
        if ($result) {
            return $result;
        } else {
            return json_encode(['result' => 'Failed', 'message' => 'Failed to change assignment status']);
        }
    }

    public function getAssignmentsWithStatus($status)
    {
        $assignments = Assignment::leftJoin('users', 'assignments.assignee_id', '=', 'users.id')
            ->leftJoin('orders', 'assignments.order_id', '=', 'orders.id')
            ->where('is_approved', '=', $status)
            ->select('orders.order_items', 'orders.customer_details', 'orders.environment', 'orders.product_look_up', 'assignments.*', 'users.name')
            ->get();
        foreach($assignments as $index=>$assignment){
            $assignments[$index]['order_items'] = json_decode($assignment['order_items']);
            $assignments[$index]['customer_details'] = json_decode($assignment['customer_details']);
        }
        return $assignments;
    }

    public function searchAssignment(Request $request)
    {
        $rules = [
            'id' => 'string|nullable',
            'order_num' => 'string|nullable',
            'customer_details' => 'string|nullable',
            'bugs' => 'integer|nullable|in:0,1'
        ];
        $validator = Validator::make($request->all(),$rules);

        if($validator->fails()){
            return json_encode(['result' => 'Failed', 'message' => 'Failed to search']);
        }
        $assignments = Assignment::leftJoin('users', 'assignments.assignee_id', '=', 'users.id')
            ->leftJoin('orders', 'assignments.order_id', '=', 'orders.id');
        if ($request->input('id')) {
            $assignments->where('assignments.id', '=', $request->input('id'));
        }
        if ($request->input('order_id')) {
            $assignments->where('orders.id', '=', $request->input('order_id'));
        }
        if ($request->input('customer_details')) {
            $assignments->where('orders.customer_details', 'like', '%' . $request->input('customer_details') . '%');
        }
        if ($request->input('bugs') !== null){
            $assignments->where('assignments.has_comments', '=', $request->input('bugs'));
        }
        $assignments = $assignments->select('users.id', 'users.name', 'orders.*', 'assignments.*')
            ->get();

        foreach($assignments as $index=>$assignment){
            $assignments[$index]['order_items'] = json_decode($assignment['order_items']);
            $assignments[$index]['customer_details'] = json_decode($assignment['customer_details']);
        }
        return $assignments;
    }

    public function getAgentAssignments(){
        $agentId = Auth::user()->id;
        $assignments = Assignment::leftJoin('users', 'assignments.assignee_id', '=', 'users.id')
            ->leftJoin('orders', 'assignments.order_id', '=', 'orders.id')
            ->where('is_approved', '=', 0)
            ->where('assignee_id', $agentId)
            ->select('orders.order_items', 'orders.customer_details', 'orders.environment', 'orders.product_look_up', 'assignments.*', 'users.name')
            ->get();
        foreach($assignments as $index=>$assignment){
            $assignments[$index]['order_items'] = json_decode($assignment['order_items']);
            $assignments[$index]['customer_details'] = json_decode($assignment['customer_details']);
        }
        return $assignments;

    }
}
