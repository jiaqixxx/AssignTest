<?php

namespace App\Http\Controllers;

use App\User;
use App\Orders;
use App\Assignment;
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

    public function assignTests(Request $request)
    {
        $numAssignments = $request->input('numAssignments');
        $notAssigned = AssignmentController::getNotAssignedOrders($numAssignments);

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
}
