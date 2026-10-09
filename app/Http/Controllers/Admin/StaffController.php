<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ManagesUploads;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StaffRequest;
use App\Models\Staff;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffController extends Controller
{
    use ManagesUploads;

    public function index(Request $request): View
    {
        return view('admin.staff.index', [
            'staff' => Staff::with('team')
                ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%' . $request->input('q') . '%'))
                ->when(array_key_exists((string) $request->input('type'), Staff::TYPES), fn ($q) => $q->ofType($request->input('type')))
                ->orderBy('type')->ordered()
                ->paginate(20)
                ->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('admin.staff.form', [
            'member' => new Staff(['type' => 'coaching', 'is_active' => true, 'sort_order' => 0]),
            'teams' => Team::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function store(StaffRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['photo', 'remove_photo']);
        $data['photo'] = $this->handleUpload($request, 'photo', 'staff');
        $member = Staff::create($data);

        return redirect()->route('admin.staff.index')->with('success', "{$member->name} added.");
    }

    public function edit(Staff $staff): View
    {
        return view('admin.staff.form', ['member' => $staff, 'teams' => Team::orderBy('name')->pluck('name', 'id')]);
    }

    public function update(StaffRequest $request, Staff $staff): RedirectResponse
    {
        $data = $request->safe()->except(['photo', 'remove_photo']);
        $data['photo'] = $this->handleUpload($request, 'photo', 'staff', $staff->photo);
        $staff->update($data);

        return redirect()->route('admin.staff.index')->with('success', "{$staff->name} updated.");
    }

    public function destroy(Staff $staff): RedirectResponse
    {
        $staff->delete();

        return redirect()->route('admin.staff.index')->with('success', "{$staff->name} removed.");
    }
}
