<?php

namespace App\Filament\Reports\Common;

use App\Exports\CollectionExport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

abstract class BaseReport
{
    protected string $color = '00695C';

    abstract public function title(): string;

    abstract public function headers(): array;

    abstract public function searchableFields(): array;

    abstract public function modelClass(): string;

    abstract public function mapRow(Model $record): array;

    public function query(?string $search = null): Builder
    {
        $query = $this->modelClass()::query();

        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                foreach ($this->searchableFields() as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

        return $query->orderBy('id', 'desc');
    }

    public function download(object $user, ?string $search = null)
    {
        $records = $this->query($search)->get();

        $data = $records->map(fn (Model $record) => $this->mapRow($record))->toArray();

        $title = $this->title();
        $filename = Str::slug($title).'_'.now()->format('d-m-Y_His').'.xlsx';

        return Excel::download(
            new CollectionExport(
                title: $title,
                data: $data,
                headers: $this->headers(),
                user: $user,
                color: $this->color,
            ),
            $filename,
        );
    }

    public function store(object $user, ?string $search = null): string
    {
        $records = $this->query($search)->get();

        $data = $records->map(fn (Model $record) => $this->mapRow($record))->toArray();

        $title = $this->title();
        $filename = Str::slug($title).'_'.now()->format('d-m-Y_His').'.xlsx';

        $path = 'mla-exports/'.$filename;

        Excel::store(
            new CollectionExport(
                title: $title,
                data: $data,
                headers: $this->headers(),
                user: $user,
                color: $this->color,
            ),
            $path,
            's3',
        );

        return $path;
    }
}
