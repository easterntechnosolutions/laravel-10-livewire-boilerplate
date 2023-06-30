<?php

namespace App\Http\Livewire;

use App\Exports\UsersExport;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Maatwebsite\Excel\Facades\Excel;

class UsersTable extends DataTableComponent
{
    protected $model = User::class;


    public function builder(): Builder
    {
        return User::query()
            ->where('user_type', '!=', '0');
    }

    /**
     * configure
     *
     * @return void
     */
    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('id', 'desc')
            //->setHideBulkActionsWhenEmptyEnabled()
            ->setFilterLayoutSlideDown();
    }

    /**
     * columns
     *
     * @return array
     */
    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("First Name", "first_name")
                ->sortable()
                ->searchable(),
            Column::make("last Name", "last_name")
                ->sortable()
                ->searchable(),

            Column::make("Email", "email")
                ->sortable()
                ->searchable(),

            Column::make("Status", "status")
                ->format(
                    fn ($value, $row, Column $column) => $value == '1' ? 'Active' : 'Deactive'
                )->sortable()
                ->searchable(),

            Column::make("Created at", "created_at")
                ->sortable()
                ->searchable(),
            Column::make("Updated at", "updated_at")
                ->sortable()
                ->searchable(),

            /*  Column::make('Actions')
                ->label(
                    function ($row, Column $column) {
                        $delete = '<button wire:click="deleteCategory(' . $row->id . ')" class="inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:border-red-700 focus:shadow-outline-red active:bg-red-600 transition ease-in-out duration-150">
                                Delete
                            </button>';
                        $edit = '<button wire:click="edit(' . $row->id . ')" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">
                        Edit
                    </button>';
                        return $edit . $delete;
                    }
                )->html(), */

            Column::make('Actions')->label(function ($row, Column $column) {
                return view('livewire.datatables_actions', ['row' => $row]);
            },)

        ];
    }

    public function delete($id)
    {
        // dd("54555");
        // User::whereIn('id', $id)->update(['status' => '1']);

        $user = User::find($id);
        $user->delete();
        // $this->clearSelected();
        // $this->emitTo('confirm', 'displayConfirmation', 'Delete Record', 'Are you sure?', 'create-users', 'destroyRecord', $id);
    }


    /**
     * bulkActions
     *
     * @return array
     */
    public function bulkActions(): array
    {
        return [
            'activate' => 'Activate',
            'deactivate' => 'Deactivate',
            'export' => 'Export',
        ];
    }


    public function export()
    {
        $usersId = $this->getSelected();

        $this->clearSelected();

        return Excel::download(new UsersExport($usersId), 'Users.csv');
    }

    /**
     * activate
     *
     * @return void
     */
    public function activate()
    {
        User::whereIn('id', $this->getSelected())->update(['status' => '1']);

        $this->clearSelected();
    }

    /**
     * deactivate
     *
     * @return void
     */
    public function deactivate()
    {
        User::whereIn('id', $this->getSelected())->update(['status' => '0']);

        $this->clearSelected();
    }

    /**
     * filters
     *
     * @return array
     */
    public function filters(): array
    {
        return [
            SelectFilter::make('Status')
                ->setFilterPillTitle('Status')
                ->setFilterPillValues([
                    '1' => 'Active',
                    '0' => 'Deactive',
                ])
                ->options([
                    '' => 'All',
                    '1' => 'Active',
                    '0' => 'Deactive',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if ($value === '1') {
                        $builder->where('status', '1');
                    } elseif ($value === '0') {
                        $builder->where('status', '0');
                    }
                }),
            DateFilter::make('Date From')
                ->config([
                    'min' => '2020-01-01',
                    'max' => '2021-12-31',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('created_at', '>=', $value);
                }),
            DateFilter::make('Date To')
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('created_at', '<=', $value);
                }),
        ];
    }
}