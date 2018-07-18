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
        //TODO UTC?
        $today = Carbon::now()->startOfDay()->format('Y-m-d H:i:s');
        $assignedOrderIds = Assignment::select('order_id')
            ->where('created_at', '>', $today)
            ->get();

        //TODO USE FLAG is_allocated will be better
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

    public function getAssignmentsWithStatus(Request $request)
    {
        $page = $request->input('page');
        $status = $request->input('status');
        $offset = ($page - 1)*3;
        $countAssignments = Assignment::where('is_approved', '=', $status)->count();
        $assignments = Assignment::leftJoin('users', 'assignments.assignee_id', '=', 'users.id')
            ->leftJoin('orders', 'assignments.order_id', '=', 'orders.id')
            ->where('is_approved', '=', $status)
            ->select('orders.order_items', 'orders.customer_details', 'orders.environment', 'orders.product_look_up', 'assignments.*', 'users.name')
            ->offset($offset)
            ->limit(3)
            ->get();
        foreach($assignments as $index=>$assignment){
            $assignments[$index]['environment'] = json_decode($assignment['environment']);
            $assignments[$index]['order_items'] = json_decode($assignment['order_items']);
            $assignments[$index]['customer_details'] = json_decode($assignment['customer_details']);
            $assignments[$index]['product_look_up'] = json_decode($assignment['product_look_up']);
        }
        return json_encode(['count' => $countAssignments, 'assignments' => $assignments]);
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
        $countAssignments = $assignments->count();
        $page = $request->input('page');
        $offset = ($page-1)*3;
        //TODO LIMIT?
        $assignments = $assignments->select('users.id', 'users.name', 'orders.*', 'assignments.*')
            ->offset($offset)
            ->limit(3)
            ->get();

        // TODO CONFIRM
        foreach($assignments as $index=>$assignment){
            $assignments[$index]['environment'] = json_decode($assignment['environment']);
            $assignments[$index]['order_items'] = json_decode($assignment['order_items']);
            $assignments[$index]['customer_details'] = json_decode($assignment['customer_details']);
            $assignments[$index]['product_look_up'] = json_decode($assignment['product_look_up']);
        }
        return json_encode(['count' => $countAssignments, 'assignments' => $assignments]);
    }

    public function getAgentAssignments(Request $request)
    {
        $page = $request->input('page');
        $offset = ($page - 1) * 6;
        $agentId = Auth::user()->id;
        $countAssignments = Assignment::where('assignee_id', '=', $agentId)->where('is_approved', '=', 0)->count();

        $assignments = Assignment::leftJoin('users', 'assignments.assignee_id', '=', 'users.id')
            ->leftJoin('orders', 'assignments.order_id', '=', 'orders.id')
            ->where('is_approved', '=', 0)
            ->where('assignee_id', $agentId)
            ->select('orders.order_items', 'orders.customer_details', 'orders.environment', 'orders.product_look_up', 'assignments.*', 'users.name')
            ->offset($offset)
            ->limit(6)
            ->get();
        foreach($assignments as $index=>$assignment){
            $assignments[$index]['environment'] = json_decode($assignment['environment']);
            $assignments[$index]['order_items'] = json_decode($assignment['order_items']);
            $assignments[$index]['customer_details'] = json_decode($assignment['customer_details']);
            $assignments[$index]['product_look_up'] = json_decode($assignment['product_look_up']);
        }
        return json_encode(['count' => $countAssignments, 'assignments' => $assignments]);
    }

    public function setAllGood(Request $request)
    {
        $assignmentId = $request->input('assignmentId');
        $result = Assignment::where('id', '=', $assignmentId)
            ->update(['is_all_good' => 1]);
        if(!$result){
            echo json_encode(['result' => 'Failed', 'message' => 'Failed to set assignment to all good']);
        }else{
            echo $result;
        }
    }
}
