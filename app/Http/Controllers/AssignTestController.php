<?php

namespace App\Http\Controllers;

use App\User;
use App\Orders;
use App\Assignment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
        $assignedOrderIds = Assignment::select('orders_id')
            ->where('created_at', '>', $today)
            ->get();

        $notAssignedOrders = Orders::select('orders_id')
            ->where('created_at', '>', $today)
            ->whereNotIn('orders_id', $assignedOrderIds)
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
                        'orders_id' => $orders['orders_id'],
                        'users_id' => $agentId,
                        'assigned_by' => 'Admin'
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
        $workload = Assignment::with('assignedBy');
        $result = DB::table('assignments as a')->leftJoin('users as u', 'u.id', '=', 'a.users_id')
            ->select(DB::raw('count(*) as numAssignments, u.name'))
            ->groupBy('u.id')
            ->get();
        return $result;
    }

    public function getInProgressAssignments()
    {
        $inProgressAssignments = Assignment::with(['assignee','assignedBy','order'])->get();
        return $inProgressAssignments->toArray();
    }
}
