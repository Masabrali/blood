@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            @include('layouts.filter')

            <div class="card">
                <div class="card-header">

                  @if (!$restriction->restricted('zone') && $user->role_id == 1)
                      <a href="{{ route('addZoneForm') }}" class="btn btn-success float-right ml-2">Add Zone</a>
                  @endif

                  <h3 class="pt-1">Zones</h3>
                </div>

                <div class="card-body">
                    @if (!$stock->stock->isEmpty() && isset($stock->total->a_plus))
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Zone</th>
                                    <th scope="col" class="text-right">A+</th>
                                    <th scope="col" class="text-right">A-</th>
                                    <th scope="col" class="text-right">B+</th>
                                    <th scope="col" class="text-right">B-</th>
                                    <th scope="col" class="text-right">AB+</th>
                                    <th scope="col" class="text-right">AB-</th>
                                    <th scope="col" class="text-right">O+</th>
                                    <th scope="col" class="text-right">O-</th>
                                    <th scope="col" class="text-right">Total</th>
                                    <th scope="col" class="text-right">Actions</th>
                                </tr>
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">Total</th>
                                    <th scope="col" class="text-right">
                                        {{ $stock->total->a_plus }}
                                    </th>
                                    <th scope="col" class="text-right">
                                        {{ $stock->total->a_minus }}
                                    </th>
                                    <th scope="col" class="text-right">
                                        {{ $stock->total->b_plus }}
                                    </th>
                                    <th scope="col" class="text-right">
                                        {{ $stock->total->b_minus }}
                                    </th>
                                    <th scope="col" class="text-right">
                                        {{ $stock->total->ab_plus }}
                                    </th>
                                    <th scope="col" class="text-right">
                                        {{ $stock->total->ab_minus }}
                                    </th>
                                    <th scope="col" class="text-right">
                                        {{ $stock->total->o_plus }}
                                    </th>
                                    <th scope="col" class="text-right">
                                        {{ $stock->total->o_minus }}
                                    </th>
                                    <th scope="col" class="text-right">
                                        {{ $stock->total->total }}
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
                                            {{ $_stock->a_plus }}
                                        </td>
                                        <td class="text-right">
                                            {{ $_stock->a_minus }}
                                        </td>
                                        <td class="text-right">
                                            {{ $_stock->b_plus }}
                                        </td>
                                        <td class="text-right">
                                            {{ $_stock->b_minus }}
                                        </td>
                                        <td class="text-right">
                                            {{ $_stock->ab_plus }}
                                        </td>
                                        <td class="text-right">
                                            {{ $_stock->ab_minus }}
                                        </td>
                                        <td class="text-right">
                                            {{ $_stock->o_plus }}
                                        </td>
                                        <td class="text-right">
                                            {{ $_stock->o_minus }}
                                        </td>
                                        <td class="text-right font-weight-bold">
                                            {{ $_stock->total }}
                                        </td>
                                        <td class="text-right">
                                            <a href="/zones/zone/{{ $_stock->id }}" class="btn btn-primary">View</a>

                                            @if (!$restriction->restricted('zone') && $user->role_id == 1)
                                                <a href="/zones/edit/{{ $_stock->id }}" class="btn btn-warning">Edit</a>
                                                <a href="/zones/delete/{{ $_stock->id }}" class="btn btn-danger">Delete</a>
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
                                        {{ $stock->total->a_plus }}
                                    </th>
                                    <th scope="col" class="text-right">
                                        {{ $stock->total->a_minus }}
                                    </th>
                                    <th scope="col" class="text-right">
                                        {{ $stock->total->b_plus }}
                                    </th>
                                    <th scope="col" class="text-right">
                                        {{ $stock->total->b_minus }}
                                    </th>
                                    <th scope="col" class="text-right">
                                        {{ $stock->total->ab_plus }}
                                    </th>
                                    <th scope="col" class="text-right">
                                        {{ $stock->total->ab_minus }}
                                    </th>
                                    <th scope="col" class="text-right">
                                        {{ $stock->total->o_plus }}
                                    </th>
                                    <th scope="col" class="text-right">
                                        {{ $stock->total->o_minus }}
                                    </th>
                                    <th scope="col" class="text-right">
                                        {{ $stock->total->total }}
                                    </th>
                                    <th scope="col"></th>
                                </tr>
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col" class="text-right">A+</th>
                                    <th scope="col" class="text-right">A-</th>
                                    <th scope="col" class="text-right">B+</th>
                                    <th scope="col" class="text-right">B-</th>
                                    <th scope="col" class="text-right">AB+</th>
                                    <th scope="col" class="text-right">AB-</th>
                                    <th scope="col" class="text-right">O+</th>
                                    <th scope="col" class="text-right">O-</th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                        </table>
                    @else
                        <h5 class="text-center">No Zone Stock Data found</h5>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
