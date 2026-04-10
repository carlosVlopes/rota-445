<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TableStatus;
use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TableController extends Controller
{
    public function index(Request $request): View
    {
        $query = Table::withCount('orders');

        if ($search = $request->get('search')) {
            $query->where('number', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $tables = $query->orderBy('number')->paginate(15)->withQueryString();

        return view('admin.tables.index', compact('tables'));
    }

    public function create(): View
    {
        return view('admin.tables.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'number' => ['required', 'string', 'max:10', 'unique:tables,number'],
        ]);

        Table::create([
            'number' => $validated['number'],
            'status' => TableStatus::Free,
        ]);

        return redirect()->route('admin.mesas.index')
            ->with('success', "Mesa {$validated['number']} criada com sucesso.");
    }

    public function edit(Table $mesa): View
    {
        $mesa->loadCount('orders');

        return view('admin.tables.edit', compact('mesa'));
    }

    public function update(Request $request, Table $mesa): RedirectResponse
    {
        $validated = $request->validate([
            'number' => ['required', 'string', 'max:10', 'unique:tables,number,' . $mesa->id],
        ]);

        $mesa->update(['number' => $validated['number']]);

        return redirect()->route('admin.mesas.index')
            ->with('success', "Mesa atualizada com sucesso.");
    }

    public function destroy(Table $mesa): RedirectResponse
    {
        if ($mesa->status !== TableStatus::Free) {
            return back()->with('error', 'Não é possível remover uma mesa que está ocupada ou aguardando pagamento.');
        }

        $mesa->delete();

        return redirect()->route('admin.mesas.index')
            ->with('success', "Mesa {$mesa->number} removida com sucesso.");
    }
}
