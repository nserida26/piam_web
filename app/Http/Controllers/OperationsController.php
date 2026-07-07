<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;

use App\Models\Operation;
use Illuminate\Http\Request;

class OperationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;

        if (!empty($keyword)) {
            $operations = Operation::where('code', 'LIKE', "%$keyword%")
                ->orWhere('intitule', 'LIKE', "%$keyword%")
                ->orWhere('intitule_fr', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $operations = Operation::latest()->paginate($perPage);
        }

        return view('admin.operations.index', compact('operations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.operations.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        
        $requestData = $request->all();
        
        Operation::create($requestData);

        return redirect('admin/operations')->with('flash_message', 'Operation added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $operation = Operation::findOrFail($id);

        return view('admin.operations.show', compact('operation'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $operation = Operation::findOrFail($id);

        return view('admin.operations.edit', compact('operation'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        
        $requestData = $request->all();
        
        $operation = Operation::findOrFail($id);
        $operation->update($requestData);

        return redirect('admin/operations')->with('flash_message', 'Operation updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        Operation::destroy($id);

        return redirect('admin/operations')->with('flash_message', 'Operation deleted!');
    }
}
