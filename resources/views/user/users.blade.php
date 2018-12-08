@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            @include('layouts.filter')

            <div class="card">
                <div class="card-header">

                  @if ($user->role_id == 1)
                      <a href="{{ route('addUserForm') }}" class="btn btn-success float-right ml-2">Add User</a>
                  @endif

                  <h3 class="pt-1">Users</h3>
                </div>

                <div class="card-body">
                    @if (!$stock->stock->isEmpty() && isset($stock->total))
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">User</th>
                                    <th scope="col">Role</th>
                                    <th scope="col" class="text-right">Collections</th>
                                    <th scope="col" colspan="2" class="text-right">Transfers</th>
                                    <th scope="col" class="text-right">Distributions</th>
                                    <th scope="col" class="text-right">Actions</th>
                                </tr>
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col" class="text-right">In</th>
                                    <th scope="col" class="text-right">Out</th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                </tr>
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">Total</th>
                                    <th scope="col"></th>
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
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $counter = 0; ?>
                                @foreach($stock->stock as $_stock)
                                    <tr>
                                        <th scope="row">{{ ++$counter }}</th>
                                        <td>
                                            {{ $_stock->firstname }} {{ $_stock->lastname }} {{ ($_stock->_user == $user->id)? ' (You)':'' }}<br/>
                                            <small>
                                                {{ (isset($_stock->zone))? $_stock->zone.' zone, ':'' }} {{ (isset($_stock->region))? $_stock->region.' region, ':'' }} {{ (isset($_stock->district))? $_stock->district.' district':'' }}<br/>
                                                {{ (isset($_stock->center))? $_stock->center:'' }}
                                            </small>
                                        </td>
                                        <td>
                                            {{ $_stock->role }}
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
                                            <a href="/users/user/{{ $_stock->_user }}" class="btn btn-primary">View</a>

                                            @if ($user->role_id == 1 && $_stock->_user != $user->id)
                                                @if (!isset($_stock->deleted_at))
                                                    <a href="/users/edit/{{ $_stock->_user }}" class="btn btn-warning">Edit</a>

                                                    <a href="/users/deactivate/{{ $_stock->_user }}" class="btn btn-danger">Deactivate</a>
                                                @else
                                                    <a href="/users/activate/{{ $_stock->_user }}" class="btn btn-success">Activate</a>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <thead>
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">Total</th>
                                    <th scope="col"></th>
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
                                    <th scope="col"></th>
                                </tr>
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col" class="text-right">In</th>
                                    <th scope="col" class="text-right">Out</th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                </tr>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">User</th>
                                    <th scope="col">Role</th>
                                    <th scope="col" class="text-right">Collections</th>
                                    <th scope="col" colspan="2" class="text-right">Transfers</th>
                                    <th scope="col" class="text-right">Distributions</th>
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
