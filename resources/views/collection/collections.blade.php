@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">

        @include('layouts.filter')

        <div class="col-md-12">
            <div class="card">
                <div class="card-header container">

                  @if ($user->role_id == 1 || $user->role_id == 2)
                      <a href="{{ route('addCollectionForm') }}" class="btn btn-success float-right">Collect</a>
                  @endif

                  <h3 class="float-left">Collections&nbsp;</h3>

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
                    @if (!$collections->isEmpty() || (!$aggregates->aggregates->isEmpty() && isset($aggregates->total->a_plus)))
                        <div class="tab-content">
                            <div class="tab-pane {{ (empty($tab))? 'active':($tab == 'aggregate')? 'active':'' }}" id="aggregate" role="tabpanel" aria-labelledby="aggregate-tab">
                                @if (!$aggregates->aggregates->isEmpty() && isset($aggregates->total->a_plus))
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Center</th>
                                                <th scope="col" class="text-right">A+</th>
                                                <th scope="col" class="text-right">A-</th>
                                                <th scope="col" class="text-right">B+</th>
                                                <th scope="col" class="text-right">B-</th>
                                                <th scope="col" class="text-right">AB+</th>
                                                <th scope="col" class="text-right">AB-</th>
                                                <th scope="col" class="text-right">O+</th>
                                                <th scope="col" class="text-right">O-</th>
                                                <th scope="col" class="text-right">Total</th>
                                            </tr>
                                            <tr>
                                                <th scope="col"></th>
                                                <th scope="col">Total</th>
                                                <th scope="col" class="text-right">
                                                    {{ $aggregates->total->a_plus }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    {{ $aggregates->total->a_minus }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    {{ $aggregates->total->b_plus }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    {{ $aggregates->total->b_minus }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    {{ $aggregates->total->ab_plus }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    {{ $aggregates->total->ab_minus }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    {{ $aggregates->total->o_plus }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    {{ $aggregates->total->o_minus }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    {{ $aggregates->total->total }}
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
                                                        {{ $aggregate->a_plus }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ $aggregate->a_minus }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ $aggregate->b_plus }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ $aggregate->b_minus }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ $aggregate->ab_plus }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ $aggregate->ab_minus }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ $aggregate->o_plus }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ $aggregate->o_minus }}
                                                    </td>
                                                    <td class="text-right font-weight-bold">
                                                        {{ $aggregate->total }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <thead>
                                            <tr>
                                                <th scope="col"></th>
                                                <th scope="col">Total</th>
                                                <th scope="col" class="text-right">
                                                    {{ $aggregates->total->a_plus }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    {{ $aggregates->total->a_minus }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    {{ $aggregates->total->b_plus }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    {{ $aggregates->total->b_minus }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    {{ $aggregates->total->ab_plus }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    {{ $aggregates->total->ab_minus }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    {{ $aggregates->total->o_plus }}
                                                </th>
                                                <th scope="col" class="text-right">
                                                    {{ $aggregates->total->o_minus }}
                                                </th><th scope="col" class="text-right">
                                                    {{ $aggregates->total->total }}
                                                </th>
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
                                            </tr>
                                        </thead>
                                    </table>
                                @else
                                    <h5 class="text-center">No Collection Aggregates found</h5>
                                @endif
                            </div>
                            <div class="tab-pane {{ (!empty($tab) && $tab == 'individual')? 'active':'' }}" id="individual" role="tabpanel" aria-labelledby="individual-tab">
                                @if (!$collections->isEmpty())
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Center</th>
                                                <th scope="col">Officer</th>
                                                <th scope="col" class="text-center">Group</th>
                                                <th scope="col" class="text-right">Units</th>
                                                <th scope="col" class="text-right">Date</th>
                                                <th scope="col" class="text-right">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $counter = 0; ?>
                                            @foreach($collections as $collection)
                                                <tr>
                                                    <th scope="row">{{ ++$counter }}</th>
                                                    <td>
                                                        {{ $collection->center }}<br/>
                                                        <small>
                                                            {{ $collection->zone }} zone, {{ $collection->region }} region, {{ $collection->district }} district
                                                        </small>
                                                    </td>
                                                    <td>
                                                        {{ $collection->firstname." ".$collection->lastname }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $collection->group }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ $collection->units }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ date('D d M, Y', strtotime($collection->date)) }}
                                                        <br/> at {{ date('h:i A', strtotime($collection->date)) }}
                                                    </td>
                                                    <td class="text-right">
                                                        <a href="/collections/collection/{{ $collection->_collection }}" class="btn btn-primary">View</a>
                                                        
                                                        @if ($user->role_id == 1 || $user->role_id == 2)
                                                            <a href="/collections/edit/{{ $collection->_collection }}" class="btn btn-warning">Edit</a>
                                                            <a href="/collections/delete/{{ $collection->_collection }}" class="btn btn-danger">Delete</a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <thead>
                                            <tr>
                                                <th scope="col"></th>
                                                <th scope="col">Center</th>
                                                <th scope="col">Officer</th>
                                                <th scope="col" class="text-center">Group</th>
                                                <th scope="col" class="text-right">Units</th>
                                                <th scope="col" class="text-right">Date</th>
                                                <th scope="col" class="text-right">Actions</th>
                                            </tr>
                                        </thead>
                                    </table>
                                @else
                                    <h5 class="text-center">No Collection Records found</h5>
                                @endif
                            </div>
                        </div>
                    @else
                        <h5 class="text-center">No Collections found</h5>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
