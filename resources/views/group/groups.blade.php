@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            @include('layouts.filter')

            <div class="card">
                <div class="card-header">

                  @if ($user->role_id == 1)
                      <a href="{{ route('addGroupForm') }}" class="btn btn-success float-right ml-2">Add Group</a>
                  @endif

                  <h3 class="pt-1">Groups</h3>
                </div>

                <div class="card-body">
                    @if (!$stock->stock->isEmpty() && isset($stock->total))
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Group</th>
                                    <th scope="col" class="text-right">Collections</th>
                                    <th scope="col" class="text-right">Transfers In</th>
                                    <th scope="col" class="text-right">Transfers Out</th>
                                    <th scope="col" class="text-right">Distributions</th>
                                    <th scope="col" class="text-right">Units</th>
                                    <th scope="col" class="text-right">Storage</th>
                                    <th scope="col" class="text-right">Stock</th>
                                    <th scope="col" class="text-right">Actions</th>
                                </tr>
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">Total</th>
                                    <th scope="col" class="text-right">
                                        {{ $stock->total->collections }}
                                    </th>
                                    <th scope="col" class="text-right">
                                        {{ $stock->total->transfers_in }}
                                    </th>
                                    <th scope="col" class="text-right">
                                        {{ $stock->total->transfers_out }}
                                    </th>
                                    <th scope="col" class="text-right">
                                        {{ $stock->total->distributions }}
                                    </th>
                                    <th scope="col" class="text-right">
                                        {{ $stock->total->total }}
                                    </th>
                                    <th scope="col" class="text-right">
                                        {{ $stock->storage }}
                                    </th>
                                    <th scope="col" class="text-right">
                                        {{ $stock->percent }}%
                                    </th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $counter = 0; ?>
                                @foreach($stock->stock as $_stock)
                                    <tr>
                                        <th scope="row">{{ ++$counter }}</th>
                                        <td>
                                            {{ $_stock->name }}
                                        </td>
                                        <td class="text-right">
                                            {{ $_stock->collections }}
                                        </td>
                                        <td class="text-right">
                                            {{ $_stock->transfers_in }}
                                        </td>
                                        <td class="text-right">
                                            {{ $_stock->transfers_out }}
                                        </td>
                                        <td class="text-right">
                                            {{ $_stock->distributions }}
                                        </td>
                                        <td class="text-right">
                                            {{ $_stock->units }}
                                        </td>
                                        <td class="text-right">
                                            {{ $_stock->storage }}
                                        </td>
                                        <td class="text-right">
                                            {{ $_stock->percent }}%
                                        </td>
                                        <td class="text-right">
                                            <a href="/groups/group/{{ $_stock->id }}" class="btn btn-primary">View</a>

                                            @if ($user->role_id == 1)
                                                <a href="/groups/edit/{{ $_stock->id }}" class="btn btn-warning">Edit</a>
                                                <a href="/groups/delete/{{ $_stock->id }}" class="btn btn-danger">Delete</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <thead>
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">Total</th>
                                    <th scope="col" class="text-right">
                                        {{ $stock->total->collections }}
                                    </th>
                                    <th scope="col" class="text-right">
                                        {{ $stock->total->transfers_in }}
                                    </th>
                                    <th scope="col" class="text-right">
                                        {{ $stock->total->transfers_out }}
                                    </th>
                                    <th scope="col" class="text-right">
                                        {{ $stock->total->distributions }}
                                    </th>
                                    <th scope="col" class="text-right">
                                        {{ $stock->total->total }}
                                    </th>
                                    <th scope="col" class="text-right">
                                        {{ $stock->storage }}
                                    </th>
                                    <th scope="col" class="text-right">
                                        {{ $stock->percent }}%
                                    </th>
                                    <th scope="col"></th>
                                </tr>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Group</th>
                                    <th scope="col" class="text-right">Collections</th>
                                    <th scope="col" class="text-right">Transfers In</th>
                                    <th scope="col" class="text-right">Transfers Out</th>
                                    <th scope="col" class="text-right">Distributions</th>
                                    <th scope="col" class="text-right">Units</th>
                                    <th scope="col" class="text-right">Storage</th>
                                    <th scope="col" class="text-right">Stock</th>
                                    <th scope="col" class="text-right">Actions</th>
                                </tr>
                            </thead>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
