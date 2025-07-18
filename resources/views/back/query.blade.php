@extends('layouts.back.index')

@section('content')

<section class="section">
    <div class="card shadow-sm p-4 table-responsive">
        <h6 class="border-bottom mb-3 profile_pge">Run Query</h6>
        <div class="row">
            <form action="{{route('post.admin.query')}}" method="post">
                @csrf
                <textarea name="form_query" class="form-control" required="" rows="4" cols="50"></textarea>
                <br>
                <input type="submit" />
            </form>
        </div>
        <p>
            @if(isset($query_result))

            @endif
        </p>
    </div>
</section>
@endsection