<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App;
use Illuminate\Support\Facades\DB;
class ApplicationController extends Controller
{

    public function all_reports($request,$current_user)
    {
        $req = $request->all();

        $reports = DB::table('localite_operations');
        $reports = $this->reports_filtering($req,$reports);
        
        $reports = $reports->join('users','users.id','localite_operations.user_id')->select('localite_operations.*','users.phone','localites.*','operations.*')->where('localite_operations.user_id',$current_user->id)->get();
        
        return $reports;
    }
    //
    public function reports_filtering($request,$reports)
    {
        $reports = $reports->join('localites','localite_operations.localite_id','localites.id')->join('operations','localite_operations.operation_id','operations.id');
        if (isset($request['start_date'])) {
            # code...
            $from = $request['start_date']."T00:00:00.000000Z";
           
            $reports = $reports->where('localite_operations.created_at','>=',$from);
        }
        if (isset( $request['end_date'])) {
            # code...
            $to = $request['end_date']."T00:00:00.000000Z";
            
            $reports = $reports->where('localite_operations.created_at','<=', $to);
        }
        /*
        if (isset($request['state_id'])) {
            # code...
            $reports = $reports->where('reports.state_id', $request['state_id']);
        }
        */

        return $reports;
    # code...
    }
}
