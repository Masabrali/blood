@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            @include('layouts.filter')

            <div class="card">
                <div class="card-header">

                  @if ($user->role_id == 1 || $user->role_id == 2)
                      <a href="{{ route('addTransferForm') }}" class="btn btn-warning float-right">Transfer</a>
                  @endif

                  <h3 class="float-left">Transfers</h3>

                  <?php $tab = session('tab'); ?>

                  <nav class="nav nav-pills justify-content-center " role="tablist">
                      <a class="nav-item nav-link {{ (empty($tab))? 'active':($tab == 'aggregate')? 'active':'' }}" href="#aggregate" aria-controls="aggregate" role="tab" data-toggle="tab">
                          Aggregate
                      </a>
                      <a class="nav-item nav-link {{ (!empty($tab) && $tab == 'individual')? 'active':'' }}" href="#individual" aria-controls="aggregate" role="tab" data-toggle="tab">
                          Individual
                      </a>
                  </nav>
                </div>

                <div class="card-body">
                    @if (!$transfers->isEmpty() || (!$aggregates->aggregates->isEmpty() && isset($aggregates->total->a_plus_in)))
                        <div class="tab-content">
                            <div class="tab-pane {{ (empty($tab))? 'active':($tab == 'aggregate')? 'active':'' }}" id="aggregate" role="tabpanel" aria-labelledby="aggregate-tab">
                                @if (!$aggregates->aggregates->isEmpty() && isset($aggregates->total->a_plus_in))
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Center</th>
                                                <th scope="col" class="text-center" colspan="2">A+</th>
                                                <th scope="col" class="text-center" colspan="2">A-</th>
                                                <th scope="col" class="text-center" colspan="2">B+</th>
                                                <th scope="col" class="text-center" colspan="2">B-</th>
                                                <th scope="col" class="text-center" colspan="2">AB+</th>
                                                <th scope="col" class="text-center" colspan="2">AB-</th>
                                                <th scope="col" class="text-center" colspan="2">O+</th>
                                                <th scope="col" class="text-center" colspan="2">O-</th>
                                                <th scope="col" class="text-center" colspan="2">Total</th>
                                            </tr>
                                            <tr>
                                                <th scope="col"></th>
                                                <th scope="col"></th>
                                                <th scope="col" class="text-right">In</th>
                                                <th scope="col" class="text-right">Out</th>
                                                <th scope="col" class="text-right">In</th>
                                                <th scope="col" class="text-right">Out</th>
                                                <th scope="col" class="text-right">In</th>
                                                <th scope="col" class="text-right">Out</th>
                                                <th scope="col" class="text-right">In</th>
                                                <th scope="col" class="text-right">Out</th>
                                                <th scope="col" class="text-right">In</th>
                                                <th scope="col" class="text-right">Out</th>
                                                <th scope="col" class="text-right">In</th>
                                                <th scope="col" class="text-right">Out</th>
                                                <th scope="col" class="text-right">In</th>
                                                <th scope="col" class="text-right">Out</th>
                                                <th scope="col" class="text-right">In</th>
                                                <th scope="col" class="text-right">Out</th>
                                                <th scope="col" class="text-right">In</th>
                                                <th scope="col" class="text-right">Out</th>
                                            </tr>
                                            <tr>
                                                <th scope="col"></th>
                                                <th scope="col">Total</th>
                                                <th scope="col" class="text-right">
                                                    +{{ $aggregates->total->a_plus_in }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    -{{ $aggregates->total->a_plus_out }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    +{{ $aggregates->total->a_minus_in }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    -{{ $aggregates->total->a_minus_out }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    +{{ $aggregates->total->b_plus_in }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    -{{ $aggregates->total->b_plus_out }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    +{{ $aggregates->total->b_minus_in }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    -{{ $aggregates->total->b_minus_out }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    +{{ $aggregates->total->ab_plus_in }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    -{{ $aggregates->total->ab_plus_out }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    +{{ $aggregates->total->ab_minus_in }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    -{{ $aggregates->total->ab_minus_out }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    +{{ $aggregates->total->o_plus_in }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    -{{ $aggregates->total->o_plus_out }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    +{{ $aggregates->total->o_minus_in }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    -{{ $aggregates->total->o_minus_out }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    +{{ $aggregates->total->total_in }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    -{{ $aggregates->total->total_out }}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $counter = 0; ?>
                                            @foreach($aggregates->aggregates as $aggregate)
                                                <tr>
                                                    <th scope="row">{{ ++$counter }}</th>
                                                    <td>
                                                        {{ $aggregate->center }}<br/>
                                                        <small>
                                                            {{ $aggregate->zone }} zone, {{ $aggregate->region }} region, {{ $aggregate->district }} district
                                                        </small>
                                                    </td>
                                                    <td class="text-right">
                                                        +{{ $aggregate->a_plus_in }}
                                                    </td>
                                                    <td class="text-right">
                                                        -{{ $aggregate->a_plus_out }}
                                                    </td>
                                                    <td class="text-right">
                                                        +{{ $aggregate->a_minus_in }}
                                                    </td>
                                                    <td class="text-right">
                                                        -{{ $aggregate->a_minus_out }}
                                                    </td>
                                                    <td class="text-right">
                                                        +{{ $aggregate->b_plus_in }}
                                                    </td>
                                                    <td class="text-right">
                                                        -{{ $aggregate->b_plus_out }}
                                                    </td>
                                                    <td class="text-right">
                                                        +{{ $aggregate->b_minus_in }}
                                                    </td>
                                                    <td class="text-right">
                                                        -{{ $aggregate->b_minus_out }}
                                                    </td>
                                                    <td class="text-right">
                                                        +{{ $aggregate->ab_plus_in }}
                                                    </td>
                                                    <td class="text-right">
                                                        -{{ $aggregate->ab_plus_out }}
                                                    </td>
                                                    <td class="text-right">
                                                        +{{ $aggregate->ab_minus_in }}
                                                    </td>
                                                    <td class="text-right">
                                                        -{{ $aggregate->ab_minus_out }}
                                                    </td>
                                                    <td class="text-right">
                                                        +{{ $aggregate->o_plus_in }}
                                                    </td>
                                                    <td class="text-right">
                                                        -{{ $aggregate->o_plus_out }}
                                                    </td>
                                                    <td class="text-right">
                                                        +{{ $aggregate->o_minus_in }}
                                                    </td>
                                                    <td class="text-right">
                                                        -{{ $aggregate->o_minus_out }}
                                                    </td>
                                                    <td class="text-right font-weight-bold">
                                                        +{{ $aggregate->total_in }}
                                                    </td>
                                                    <td class="text-right font-weight-bold">
                                                        -{{ $aggregate->total_out }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <thead>
                                            <tr>
                                                <th scope="col"></th>
                                                <th scope="col">Total</th>
                                                <th scope="col" class="text-right">
                                                    +{{ $aggregates->total->a_plus_in }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    -{{ $aggregates->total->a_plus_out }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    +{{ $aggregates->total->a_minus_in }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    -{{ $aggregates->total->a_minus_out }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    +{{ $aggregates->total->b_plus_in }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    -{{ $aggregates->total->b_plus_out }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    +{{ $aggregates->total->b_minus_in }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    -{{ $aggregates->total->b_minus_out }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    +{{ $aggregates->total->ab_plus_in }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    -{{ $aggregates->total->ab_plus_out }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    +{{ $aggregates->total->ab_minus_in }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    -{{ $aggregates->total->ab_minus_out }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    +{{ $aggregates->total->o_plus_in }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    -{{ $aggregates->total->o_plus_out }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    +{{ $aggregates->total->o_minus_in }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    -{{ $aggregates->total->o_minus_out }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    +{{ $aggregates->total->total_in }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    -{{ $aggregates->total->total_out }}
                                                </th>
                                            </tr>
                                            <tr>
                                                <th scope="col"></th>
                                                <th scope="col"></th>
                                                <th scope="col" class="text-right">In</th>
                                                <th scope="col" class="text-right">Out</th>
                                                <th scope="col" class="text-right">In</th>
                                                <th scope="col" class="text-right">Out</th>
                                                <th scope="col" class="text-right">In</th>
                                                <th scope="col" class="text-right">Out</th>
                                                <th scope="col" class="text-right">In</th>
                                                <th scope="col" class="text-right">Out</th>
                                                <th scope="col" class="text-right">In</th>
                                                <th scope="col" class="text-right">Out</th>
                                                <th scope="col" class="text-right">In</th>
                                                <th scope="col" class="text-right">Out</th>
                                                <th scope="col" class="text-right">In</th>
                                                <th scope="col" class="text-right">Out</th>
                                                <th scope="col" class="text-right">In</th>
                                                <th scope="col" class="text-right">Out</th>
                                                <th scope="col" class="text-right">In</th>
                                                <th scope="col" class="text-right">Out</th>
                                            </tr>
                                            <tr>
                                                <th scope="col"></th>
                                                <th scope="col"></th>
                                                <th scope="col" class="text-center" colspan="2">A+</th>
                                                <th scope="col" class="text-center" colspan="2">A-</th>
                                                <th scope="col" class="text-center" colspan="2">B+</th>
                                                <th scope="col" class="text-center" colspan="2">B-</th>
                                                <th scope="col" class="text-center" colspan="2">AB+</th>
                                                <th scope="col" class="text-center" colspan="2">AB-</th>
                                                <th scope="col" class="text-center" colspan="2">O+</th>
                                                <th scope="col" class="text-center" colspan="2">O-</th>
                                                <th scope="col" colspan="2"></th>
                                            </tr>
                                        </thead>
                                    </table>
                                @else
                                    <h5 class="text-center">No Transfer Aggregates found</h5>
                                @endif
                            </div>
                            <div class="tab-pane {{ (!empty($tab) && $tab == 'individual')? 'active':'' }}" id="individual" role="tabpanel" aria-labelledby="individual-tab">
                                @if (!$transfers->isEmpty())
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">From</th>
                                                <th scope="col">To</th>
                                                <th scope="col">Officer</th>
                                                <th scope="col" class="text-center">Group</th>
                                                <th scope="col" class="text-right">Units</th>
                                                <th scope="col" class="text-right">Date</th>
                                                <th scope="col" class="text-right">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $counter = 0; ?>
                                            @foreach($transfers as $transfer)
                                                <tr>
                                                    <th scope="row">{{ ++$counter }}</th>
                                                    <td>
                                                        {{ $transfer->from_center }}<br/>
                                                        <small>
                                                            {{ $transfer->from_zone }} zone, {{ $transfer->from_region }} region, {{ $transfer->from_district }} district
                                                        </small>
                                                    </td>
                                                    <td>
                                                        {{ $transfer->to_center }}<br/>
                                                        <small>
                                                            {{ $transfer->to_zone }} zone, {{ $transfer->to_region }} region, {{ $transfer->to_district }} district
                                                        </small>
                                                    </td>
                                                    <td>
                                                        {{ $transfer->firstname." ".$transfer->lastname }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $transfer->group }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ $transfer->units }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ date('D d M, Y', strtotime($transfer->date)) }}
                                                        <br/> at {{ date('h:i A', strtotime($transfer->date)) }}
                                                    </td>
                                                    <td class="text-right">
                                                        <a href="/transfers/transfer/{{ $transfer->_transfer }}" class="btn btn-primary">View</a>

                                                        @if ($user->role_id == 1 || $user->role_id == 2)
                                                            <a href="/transfers/edit/{{ $transfer->_transfer }}" class="btn btn-warning">Edit</a>
                                                            <a href="/transfers/delete/{{ $transfer->_transfer }}" class="btn btn-danger">Delete</a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <thead>
                                            <tr>
                                                <th scope="col"></th>
                                                <th scope="col">From</th>
                                                <th scope="col">To</th>
                                                <th scope="col">Officer</th>
                                                <th scope="col" class="text-center">Group</th>
                                                <th scope="col" class="text-right">Units</th>
                                                <th scope="col" class="text-right">Date</th>
                                                <th scope="col" class="text-right">Actions</th>
                                            </tr>
                                        </thead>
                                    </table>
                                @else
                                    <h5 class="text-center">No Transfer Records found</h5>
                                @endif
                            </div>
                        </div>
                    @else
                        <h5 class="text-center">No Transfers found</h5>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
