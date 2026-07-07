<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Requests\LocaliteRequest;
use App\Models\Commune;
use App\Models\Localite;
use App\Models\Moughata;
use App\Models\Wilaya;
use Illuminate\Http\Request;

class LocalitesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('admin.localites.index');
    }

    public function datatable(Request $request)
    {
        $draw   = intval($request->input('draw', 1));
        $start  = intval($request->input('start', 0));
        $length = intval($request->input('length', 25));
        $search = $request->input('search.value', '');
        $orderColIndex = intval($request->input('order.0.column', 0));
        $orderDir = $request->input('order.0.dir', 'asc') === 'desc' ? 'desc' : 'asc';

        // Columns that map to DB fields (null = non-orderable)
        $columns = [null, 'code', 'intitule', 'intitule_fr', null, null, null, 'valider', null];

        $query = Localite::with(['wilaya:id,intitule,intitule_fr', 'moughata:id,intitule,intitule_fr', 'commune:id,intitule,intitule_fr']);

        $totalRecords = Localite::count();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'LIKE', "%$search%")
                  ->orWhere('intitule', 'LIKE', "%$search%")
                  ->orWhere('intitule_fr', 'LIKE', "%$search%")
                  ->orWhere('code_ansade', 'LIKE', "%$search%")
                  ->orWhereHas('wilaya', fn($q) => $q->where('intitule', 'LIKE', "%$search%")->orWhere('intitule_fr', 'LIKE', "%$search%"))
                  ->orWhereHas('moughata', fn($q) => $q->where('intitule', 'LIKE', "%$search%")->orWhere('intitule_fr', 'LIKE', "%$search%"))
                  ->orWhereHas('commune', fn($q) => $q->where('intitule', 'LIKE', "%$search%")->orWhere('intitule_fr', 'LIKE', "%$search%"));
            });
        }

        $filteredRecords = $query->count();

        $orderColumn = $columns[$orderColIndex] ?? 'id';
        $query->orderBy($orderColumn, $orderDir);

        $rows = $query->skip($start)->take($length)->get()->map(function ($item, $index) use ($start) {
            $validerBadge = $item->valider
                ? '<span class="badge badge-success">' . __('Oui') . '</span>'
                : '<span class="badge badge-danger">' . __('Non') . '</span>';

            $actions = '<div class="btn-group">'
                . '<a href="' . url('/admin/localites/' . $item->id) . '" class="btn btn-info btn-sm"><i class="fa fa-eye"></i></a>'
                . '<a href="' . url('/admin/localites/' . $item->id . '/edit') . '" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>'
                . '<form action="' . url('/admin/localites/' . $item->id) . '" method="POST" style="display:inline-block" onsubmit="return confirm(\'' . __('localite.confirm_delete') . '\')">'
                . csrf_field() . method_field('DELETE')
                . '<button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>'
                . '</form></div>';

            return [
                $start + $index + 1,
                e($item->code),
                e($item->intitule),
                e($item->intitule_fr),
                e($item->wilaya->intitule ?? ''),
                e($item->moughata->intitule ?? ''),
                e($item->commune->intitule ?? ''),
                $validerBadge,
                $actions,
            ];
        });

        return response()->json([
            'draw'            => $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $rows,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $wilayas = Wilaya::all();
        $moughatas = Moughata::all();
        $communes = Commune::all();
        return view('admin.localites.create', compact('wilayas', 'moughatas', 'communes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\LocaliteRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(LocaliteRequest $request)
    {
        $requestData = $request->validated();
        
        Localite::create($requestData);

        return redirect('admin/localites')
            ->with('flash_message', __('Localite added!'))
            ->with('flash_type', 'success');
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
        $localite = Localite::with(['wilaya', 'moughata', 'commune'])->findOrFail($id);

        return view('admin.localites.show', compact('localite'));
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
        $localite = Localite::with(['wilaya', 'moughata', 'commune'])->findOrFail($id);
        $wilayas = Wilaya::all();
        $moughatas = Moughata::all();
        $communes = Commune::all();
        
        return view('admin.localites.edit', compact('localite', 'wilayas', 'moughatas', 'communes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\LocaliteRequest $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(LocaliteRequest $request, $id)
    {
        $requestData = $request->validated();

        $localite = Localite::findOrFail($id);
        $localite->update($requestData);

        return redirect('admin/localites')
            ->with('flash_message', __('Localite updated!'))
            ->with('flash_type', 'success');
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
        Localite::destroy($id);

        return redirect('admin/localites')
            ->with('flash_message', __('Localite deleted!'))
            ->with('flash_type', 'success');
    }

    /**
     * Get moughatas by wilaya
     */
    public function getMoughatasByWilaya($wilayaId)
    {
        $moughatas = Moughata::where('wilaya_id', $wilayaId)->get();
        return response()->json(['moughatas' => $moughatas]);
    }

    /**
     * Get communes by moughata
     */
    public function getCommunesByMoughata($moughataId)
    {
        $communes = Commune::where('moughata_id', $moughataId)->get();
        return response()->json(['communes' => $communes]);
    }
}