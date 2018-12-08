@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            @include('layouts.filter')
            <!-- <br /> -->
            <div class="container py-3">

                @if ($user->role_id == 1 || $user->role_id == 2)
                    <a href="{{ route('addDistributionForm') }}" class="btn btn-danger float-right ml-2">Distribute</a>

                    <a href="{{ route('addTransferForm') }}" class="btn btn-warning float-right">Transfer</a>

                    <a href="{{ route('addCollectionForm') }}" class="btn btn-success float-right mr-2">Collect</a>
                @endif

                <h3 class="pt-1">Dashboard</h3>

                <div class="clear-fix"></div>
            </div>
            <!-- <br /> -->
            <div class="row pl-2">

                <?php
                    $i = 0;

                    $storage = (Array) $stock->storage;

                    $keys = array_keys($storage);

                    $total = (Array) $stock->total;
                ?>
                @while ($i < count($keys))

                    <?php

                        if (substr_count($keys[$i], 'plus'))
                            $name = strtoupper(str_replace('_', '', str_replace('plus', '+', $keys[$i])));

                        else if (substr_count($keys[$i], 'minus'))
                            $name = strtoupper(str_replace('_', '', str_replace('minus', '-', $keys[$i])));

                        $_total = (isset($total[$keys[$i]]))? $total[$keys[$i]]:0;
                        $_storage = (isset($storage[$keys[$i]]))? $storage[$keys[$i]]:0;

                        if ($_storage == 0) $percent = 0;
                        else $percent = round(($_total /  $_storage) * 100, 1);

                    ?>

                    <div class="my-0 mb-2 ml-2">
                        <div class="card">
                            <div class="card-body p-0">
                                <div class="container gaugemeter p-0" data-percent="{{ $percent }}" data-used="{{ floor($percent) }}" data-total="100" data-append="%" data-label="{{ $name }}" data-color="#da291c" data-label-color="#da291c" data-size="130" data-width="10"></div>
                            </div>

                            <div class="card-header">
                                <h5 class="pt-1">
                                    {{ $_total }}
                                </h5>
                                <small>of {{ $_storage }}</small>
                            </div>
                        </div>

                        <?php $i += 1; ?>

                    </div>

                @endwhile

            </div>
            <br/>
            <div class="card">
                <div class="card-header">
                  <h5 class="pt-1">{{ $stock_title }} Stock Levels</h5>
                </div>

                <div class="card-body">
                    @if ((!empty($stock->stock) && !empty($stock->total)))
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">
                                        {{ $stock_title }}
                                    </th>
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
                                </tr>
                            </thead>
                            <tbody>
                                <?php $counter = 0; ?>
                                @foreach($stock->stock as $_stock)
                                    <tr>
                                        <th scope="row">{{ ++$counter }}</th>
                                        <td>
                                            @if (isset($_stock->center))
                                                {{ $_stock->center }}
                                            @elseif (isset($_stock->district))
                                                {{ $_stock->district }}
                                            @elseif (isset($_stock->region))
                                                {{ $_stock->region }}
                                            @elseif (isset($_stock->name))
                                                {{ $_stock->name }}
                                            @endif
                                            <br/>
                                            <small>
                                                @if (isset($_stock->zone))
                                                    {{ $_stock->zone.' zone,' }}
                                                @endif
                                                @if (isset($stock->region))
                                                    {{ $_stock->region.' region,' }}
                                                @endif
                                                @if (isset($stock->district))
                                                    {{ $_stock->district.' district' }}
                                                @endif
                                            </small>
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
                        <h5 class="text-center">No Stock Data found</h5>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
