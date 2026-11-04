<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Slug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Base commune aux listes simples de l'admin (catégories, marques, couleurs) :
 * même écran, mêmes actions, seuls les champs et les libellés changent.
 */
abstract class TaxonomyController extends Controller
{
    /** @return class-string<Model> */
    abstract protected function model(): string;

    /** Préfixe des routes : « admin.brands »… */
    abstract protected function routeName(): string;

    /** @return array{plural: string, singular: string, new: string, created: string, saved: string, deleted: string, used: string} */
    abstract protected function labels(): array;

    /** Règles des champs propres à ce type (en plus du nom et de la position). */
    protected function extraRules(): array
    {
        return [];
    }

    protected function hasVisibility(): bool
    {
        return true;
    }

    /** Vrai si l'on refuse de supprimer un élément encore utilisé par des produits. */
    protected function protectsUsedItems(): bool
    {
        return false;
    }

    public function index(): View
    {
        $items = $this->model()::query()->ordered()->withCount('products')->get();

        return view('admin.taxonomies.index', $this->viewData(['items' => $items]));
    }

    public function create(): View
    {
        $model = $this->model();
        $item = new $model(['is_visible' => true, 'position' => (int) $model::max('position') + 1]);

        return view('admin.taxonomies.form', $this->viewData(['item' => $item]));
    }

    public function store(Request $request): RedirectResponse
    {
        $model = $this->model();
        $this->save($request, new $model);

        return redirect()->route($this->routeName().'.index')->with('status', $this->labels()['created']);
    }

    public function edit(int $id): View
    {
        return view('admin.taxonomies.form', $this->viewData(['item' => $this->model()::findOrFail($id)]));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $this->save($request, $this->model()::findOrFail($id));

        return redirect()->route($this->routeName().'.index')->with('status', $this->labels()['saved']);
    }

    public function destroy(int $id): RedirectResponse
    {
        $item = $this->model()::withCount('products')->findOrFail($id);
        if ($item->products_count > 0 && $this->protectsUsedItems()) {
            return back()->with('error', $this->labels()['used']);
        }
        $item->delete();

        return redirect()->route($this->routeName().'.index')->with('status', $this->labels()['deleted']);
    }

    private function save(Request $request, Model $item): void
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'position' => ['nullable', 'integer', 'min:0', 'max:9999'],
            ...$this->extraRules(),
        ]);

        $item->fill([...$data, 'position' => $data['position'] ?? 0]);
        if ($this->hasVisibility()) {
            $item->is_visible = $request->boolean('is_visible');
        }
        if (! $item->exists || $item->isDirty('name')) {
            $item->slug = Slug::unique($item::class, $data['name'], $item->getKey());
        }
        $item->save();
    }

    private function viewData(array $data): array
    {
        return [
            ...$data,
            'route' => $this->routeName(),
            'labels' => $this->labels(),
            'kind' => class_basename($this->model()),
            'hasVisibility' => $this->hasVisibility(),
        ];
    }
}
